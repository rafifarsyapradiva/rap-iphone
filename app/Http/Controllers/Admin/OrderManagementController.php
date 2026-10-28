<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkPrintRequest;
use App\Models\NotificationLog;
use App\Models\Order;
use App\Services\InvoicePdfService;
use App\Services\NotificationOrchestrator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class OrderManagementController extends Controller
{
    /**
     * Status yang tersedia sebagai tab filter dan state machine pesanan.
     */
    public const STATUSES = ['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled'];

    /**
     * Pilihan kurir ekspedisi resmi Rap-iPhone.
     *
     * @var array<string, string>
     */
    public const COURIERS = [
        'JNE Express' => 'JNE Express (Reguler / YES)',
        'SiCepat Ekspres' => 'SiCepat Ekspres (BEST)',
        'J&T Express' => 'J&T Express Super',
        'Pick-up Store' => 'Ambil Sendiri di Store Resmi Rap-iPhone',
    ];

    public function __construct(
        protected InvoicePdfService $invoicePdfService,
        protected NotificationOrchestrator $notificationOrchestrator
    ) {
    }

    /**
     * Daftar pesanan dengan filter status, pencarian, modal input resi, dan aksi cetak massal.
     */
    public function index(Request $request): View
    {
        $statusFilter = (string) $request->query('status', 'all');
        $search = trim((string) $request->query('q', ''));

        $query = Order::with(['user', 'items', 'payment'])->latest();

        if (in_array($statusFilter, self::STATUSES, true)) {
            $query->where('status', $statusFilter);
        } else {
            $statusFilter = 'all';
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('recipient_name', 'like', "%{$search}%")
                    ->orWhere('recipient_phone', 'like', "%{$search}%")
                    ->orWhere('shipping_tracking_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('email', 'like', "%{$search}%"));
            });
        }

        $orders = $query->paginate(20)->withQueryString();

        $counts = Order::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        $metrics = [
            'total' => (int) $counts->sum(),
            'pending' => (int) ($counts['pending'] ?? 0),
            'to_process' => (int) (($counts['paid'] ?? 0) + ($counts['processing'] ?? 0)),
            'shipped' => (int) ($counts['shipped'] ?? 0),
            'completed' => (int) ($counts['completed'] ?? 0),
        ];

        return view('admin.orders.index', [
            'orders' => $orders,
            'metrics' => $metrics,
            'counts' => $counts,
            'statusFilter' => $statusFilter,
            'search' => $search,
            'couriers' => self::COURIERS,
            'bulkMax' => (int) config('invoice.bulk_max', 50),
            'labelStatuses' => config('invoice.label_statuses', []),
        ]);
    }

    /**
     * Tampilkan halaman detail operasional pesanan, alokasi IMEI, status, dan logistik pengiriman.
     */
    public function show(Order $order): View
    {
        $order->load(['user', 'items.variant.product', 'payment', 'tradeIn']);

        $notificationLogs = NotificationLog::query()
            ->where(function ($q) use ($order) {
                $q->where('response_payload->order_number', $order->order_number);
                if ($order->user?->email) {
                    $q->orWhere('recipient', $order->user->email);
                }
            })
            ->latest('created_at')
            ->take(10)
            ->get();

        return view('admin.orders.show', [
            'order' => $order,
            'couriers' => self::COURIERS,
            'statuses' => self::STATUSES,
            'notificationLogs' => $notificationLogs,
        ]);
    }

    /**
     * Perbarui status pesanan dan alokasi nomor IMEI unit pada item pesanan.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
            'allocated_imei' => ['nullable', 'array'],
            'allocated_imei.*' => ['nullable', 'string', 'max:50'],
            'shipping_courier' => ['nullable', 'string', 'max:50'],
            'shipping_tracking_number' => ['nullable', 'string', 'max:100'],
        ]);

        $previousStatus = $order->status;
        $newStatus = $validated['status'];

        DB::transaction(function () use ($order, $validated, $newStatus) {
            if (!empty($validated['allocated_imei']) && is_array($validated['allocated_imei'])) {
                foreach ($order->items as $item) {
                    if (array_key_exists($item->id, $validated['allocated_imei'])) {
                        $imeiValue = trim((string) $validated['allocated_imei'][$item->id]);
                        $item->update([
                            'allocated_imei' => $imeiValue !== '' ? $imeiValue : null,
                        ]);
                    }
                }
            }

            $updateData = ['status' => $newStatus];

            if (!empty($validated['shipping_courier'])) {
                $updateData['shipping_courier'] = trim($validated['shipping_courier']);
            }

            if (array_key_exists('shipping_tracking_number', $validated) && $validated['shipping_tracking_number'] !== null) {
                $tracking = strtoupper(trim((string) $validated['shipping_tracking_number']));
                if ($tracking !== '') {
                    $updateData['shipping_tracking_number'] = $tracking;
                }
            }

            $order->update($updateData);
        });

        $order->refresh();

        $notifNote = '';
        if ($newStatus === 'shipped' && $previousStatus !== 'shipped' && !empty($order->shipping_tracking_number)) {
            $this->notificationOrchestrator->notifyOrderShipped($order);
            $notifNote = ' Notifikasi WhatsApp & Email resi pengiriman telah dikirim ke pelanggan.';
        }

        return back()->with(
            'success',
            "Status pesanan #{$order->order_number} berhasil diperbarui menjadi \"" . strtoupper($newStatus) . "\".{$notifNote}"
        );
    }

    /**
     * Input nomor resi pengiriman kurir (JNE / SiCepat / J&T) yang otomatis memicu notifikasi WA & Email ke customer.
     */
    public function updateShipping(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'shipping_courier' => ['required', 'string', 'max:50'],
            'shipping_tracking_number' => ['required', 'string', 'max:100'],
            'allocated_imei' => ['nullable', 'array'],
            'allocated_imei.*' => ['nullable', 'string', 'max:50'],
        ], [
            'shipping_courier.required' => 'Pilih atau isi nama kurir ekspedisi pengiriman.',
            'shipping_tracking_number.required' => 'Nomor resi pengiriman wajib diisi.',
        ]);

        $trackingNumber = strtoupper(trim($validated['shipping_tracking_number']));
        $courier = trim($validated['shipping_courier']);

        DB::transaction(function () use ($order, $validated, $courier, $trackingNumber) {
            if (!empty($validated['allocated_imei']) && is_array($validated['allocated_imei'])) {
                foreach ($order->items as $item) {
                    if (array_key_exists($item->id, $validated['allocated_imei'])) {
                        $imeiValue = trim((string) $validated['allocated_imei'][$item->id]);
                        if ($imeiValue !== '') {
                            $item->update(['allocated_imei' => $imeiValue]);
                        }
                    }
                }
            }

            $nextStatus = in_array($order->status, ['completed', 'cancelled'], true)
                ? $order->status
                : 'shipped';

            $order->update([
                'shipping_courier' => $courier,
                'shipping_tracking_number' => $trackingNumber,
                'status' => $nextStatus,
            ]);
        });

        $order->refresh();

        // Otomatis picu pengiriman notifikasi WhatsApp (Fonnte) & Email (OrderShippedMail)
        $this->notificationOrchestrator->notifyOrderShipped($order);

        return back()->with(
            'success',
            "Nomor resi {$trackingNumber} ({$courier}) untuk pesanan #{$order->order_number} berhasil disimpan. Status otomatis diperbarui ke DIKIRIM dan notifikasi WhatsApp & Email telah dikirim ke pelanggan."
        );
    }

    /**
     * Tampilkan satu nota (semua status, dengan watermark bila belum lunas).
     */
    public function invoice(Order $order): Response
    {
        return $this->invoicePdfService
            ->generatePdf($order)
            ->stream($this->invoicePdfService->getFilename($order));
    }

    /**
     * Cetak massal nota faktur dalam satu dokumen PDF.
     */
    public function bulkInvoices(BulkPrintRequest $request): Response
    {
        $orders = $this->selectedOrders($request);

        return $this->invoicePdfService
            ->generateBulkPdf($orders)
            ->stream($this->invoicePdfService->getBulkFilename('invoice'));
    }

    /**
     * Cetak massal label pengiriman thermal 100x150mm (hanya pesanan lunas yang belum selesai).
     */
    public function bulkLabels(BulkPrintRequest $request): Response|RedirectResponse
    {
        $orders = $this->selectedOrders($request)
            ->filter(fn (Order $order) => in_array($order->status, config('invoice.label_statuses', []), true))
            ->values();

        if ($orders->isEmpty()) {
            return redirect()
                ->route('admin.orders.index')
                ->with('error', 'Label pengiriman hanya dapat dicetak untuk pesanan berstatus Lunas, Diproses, atau Dikirim.');
        }

        return $this->invoicePdfService
            ->generateShippingLabelsPdf($orders)
            ->stream($this->invoicePdfService->getBulkFilename('label'));
    }

    /**
     * Ambil pesanan terpilih sesuai urutan terbaru.
     */
    protected function selectedOrders(BulkPrintRequest $request)
    {
        return Order::with(['user', 'items.variant.product', 'payment', 'tradeIn'])
            ->whereIn('id', $request->validated('order_ids'))
            ->latest()
            ->get();
    }
}
