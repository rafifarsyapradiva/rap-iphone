<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TradeIn;
use App\Services\TradeInCalculatorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TradeInVerificationController extends Controller
{
    protected TradeInCalculatorService $calculatorService;

    public function __construct(TradeInCalculatorService $calculatorService)
    {
        $this->calculatorService = $calculatorService;
    }

    /**
     * Display the queue of customer Trade-In submissions for Admin & Owner.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status', 'all');
        $search = trim((string) $request->query('q', ''));

        $query = TradeIn::with(['user', 'targetOrder'])->latest();

        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('trade_in_code', 'like', "%{$search}%")
                    ->orWhere('phone_model', 'like', "%{$search}%")
                    ->orWhere('imei', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone_number', 'like', "%{$search}%");
                    });
            });
        }

        $tradeIns = $query->paginate(12)->withQueryString();

        // Queue Metrics
        $metrics = [
            'total' => TradeIn::count(),
            'submitted' => TradeIn::where('status', 'submitted')->count(),
            'inspecting' => TradeIn::whereIn('status', ['unit_received', 'inspected'])->count(),
            'approved' => TradeIn::whereIn('status', ['approved', 'paid_out'])->count(),
            'rejected' => TradeIn::where('status', 'rejected')->count(),
        ];

        return view('admin.trade-ins.index', compact('tradeIns', 'metrics', 'statusFilter', 'search'));
    }

    /**
     * Display the technician inspection form and final price decision panel.
     */
    public function show(TradeIn $tradeIn): View
    {
        $tradeIn->load(['user', 'targetOrder']);

        $modelCatalog = $this->calculatorService->getModelCatalog();
        $physicalConditions = $this->calculatorService->getPhysicalConditions();
        $screenConditions = $this->calculatorService->getScreenConditions();
        $accessoriesOptions = $this->calculatorService->getAccessoriesOptions();

        $breakdown = $this->calculatorService->calculateEstimate([
            'phone_model' => $tradeIn->phone_model,
            'storage_capacity' => $tradeIn->storage_capacity,
            'battery_health' => $tradeIn->battery_health,
            'physical_condition' => $tradeIn->physical_condition,
            'screen_condition' => $tradeIn->screen_condition,
            'accessories_status' => $tradeIn->accessories_status,
        ]);

        return view('admin.trade-ins.show', compact(
            'tradeIn',
            'modelCatalog',
            'physicalConditions',
            'screenConditions',
            'accessoriesOptions',
            'breakdown'
        ));
    }

    /**
     * Save technician's physical & functional inspection results when the unit arrives in store.
     */
    public function updateInspection(Request $request, TradeIn $tradeIn): RedirectResponse
    {
        $validated = $request->validate([
            'physical_condition' => 'required|string|max:50',
            'screen_condition' => 'required|string|max:50',
            'battery_health' => 'required|integer|min:1|max:100',
            'accessories_status' => 'required|string|max:50',
            'imei' => 'nullable|string|max:30',
            'status' => 'nullable|in:submitted,unit_received,inspected',
            'final_price' => 'nullable|numeric|min:0',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        // Recalculate system estimate if technician updated physical parameters and requested recalculation
        $recalculated = $this->calculatorService->calculateEstimate([
            'phone_model' => $tradeIn->phone_model,
            'storage_capacity' => $tradeIn->storage_capacity,
            'battery_health' => $validated['battery_health'],
            'physical_condition' => $validated['physical_condition'],
            'screen_condition' => $validated['screen_condition'],
            'accessories_status' => $validated['accessories_status'],
        ]);

        $newStatus = $validated['status'] ?? (in_array($tradeIn->status, ['submitted', 'unit_received'], true) ? 'inspected' : $tradeIn->status);

        $finalPrice = isset($validated['final_price']) && $validated['final_price'] !== ''
            ? (float) $validated['final_price']
            : ($tradeIn->final_price ?? $recalculated['estimated_price']);

        $tradeIn->update([
            'physical_condition' => $validated['physical_condition'],
            'screen_condition' => $validated['screen_condition'],
            'battery_health' => (int) $validated['battery_health'],
            'accessories_status' => $validated['accessories_status'],
            'imei' => $validated['imei'] ?? $tradeIn->imei,
            'status' => $newStatus,
            'final_price' => $finalPrice,
            'admin_notes' => $validated['admin_notes'] ?? $tradeIn->admin_notes,
        ]);

        return redirect()
            ->route('admin.trade-ins.show', $tradeIn->trade_in_code)
            ->with('success', "Hasil inspeksi teknisi untuk unit {$tradeIn->trade_in_code} berhasil disimpan (Harga Penawaran: Rp " . number_format((float) $tradeIn->final_price, 0, ',', '.') . ').');
    }

    /**
     * Handle final offer adjustment and Approve / Reject / Paid Out actions.
     */
    public function updateDecision(Request $request, TradeIn $tradeIn): RedirectResponse
    {
        $validated = $request->validate([
            'decision' => 'required|in:unit_received,inspected,approved,rejected,paid_out',
            'final_price' => 'nullable|numeric|min:0',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $decision = $validated['decision'];

        $finalPrice = $tradeIn->final_price;
        if (isset($validated['final_price']) && $validated['final_price'] !== '') {
            $finalPrice = (float) $validated['final_price'];
        } elseif ($decision === 'approved' && is_null($finalPrice)) {
            $finalPrice = (float) $tradeIn->estimated_price;
        }

        $tradeIn->update([
            'status' => $decision,
            'final_price' => $finalPrice,
            'admin_notes' => $validated['admin_notes'] ?? $tradeIn->admin_notes,
        ]);

        $statusMessages = [
            'unit_received' => "Status pengajuan {$tradeIn->trade_in_code} diperbarui menjadi: Unit Diterima di Toko.",
            'inspected' => "Status pengajuan {$tradeIn->trade_in_code} diperbarui menjadi: Selesai Diinspeksi Teknisi.",
            'approved' => "Pengajuan {$tradeIn->trade_in_code} DISETUJUI dengan penawaran harga final Rp " . number_format((float) $finalPrice, 0, ',', '.') . '.',
            'rejected' => "Pengajuan {$tradeIn->trade_in_code} telah DITOLAK.",
            'paid_out' => "Pencairan dana / kupon untuk {$tradeIn->trade_in_code} telah ditandai SELESAI (Paid Out).",
        ];

        return redirect()
            ->route('admin.trade-ins.show', $tradeIn->trade_in_code)
            ->with('success', $statusMessages[$decision] ?? 'Status pengajuan berhasil diperbarui.');
    }
}
