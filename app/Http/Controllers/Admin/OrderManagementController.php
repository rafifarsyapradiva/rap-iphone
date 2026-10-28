<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkPrintRequest;
use App\Models\Order;
use App\Services\InvoicePdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class OrderManagementController extends Controller
{
    /**
     * Status yang tersedia sebagai tab filter.
     */
    public const STATUSES = ['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled'];

    public function __construct(protected InvoicePdfService $invoicePdfService)
    {
    }

    /**
     * Daftar pesanan dengan filter status, pencarian, dan aksi cetak massal.
     * (Phase 10.2 akan menambahkan input resi & pembaruan status di controller ini.)
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
            'bulkMax' => (int) config('invoice.bulk_max', 50),
            'labelStatuses' => config('invoice.label_statuses', []),
        ]);
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

