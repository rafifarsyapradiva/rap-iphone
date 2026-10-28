<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\TradeIn;
use App\Services\NotificationOrchestrator;
use App\Services\TradeInCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TradeInCustomerController extends Controller
{
    protected TradeInCalculatorService $calculatorService;
    protected NotificationOrchestrator $notificationOrchestrator;

    public function __construct(
        TradeInCalculatorService $calculatorService,
        NotificationOrchestrator $notificationOrchestrator
    ) {
        $this->calculatorService = $calculatorService;
        $this->notificationOrchestrator = $notificationOrchestrator;
    }

    /**
     * Display the 4-step interactive Trade-In Wizard page.
     */
    public function index(): View
    {
        $modelCatalog = $this->calculatorService->getModelCatalog();
        $physicalConditions = $this->calculatorService->getPhysicalConditions();
        $screenConditions = $this->calculatorService->getScreenConditions();
        $accessoriesOptions = $this->calculatorService->getAccessoriesOptions();
        $supportedBanks = $this->calculatorService->getSupportedBanks();

        $seriesGroups = collect($modelCatalog)
            ->pluck('series')
            ->unique()
            ->values();

        $myTradeIns = auth()->check()
            ? TradeIn::where('user_id', auth()->id())->latest()->get()
            : collect([]);

        return view('customer.trade-in', compact(
            'modelCatalog',
            'seriesGroups',
            'physicalConditions',
            'screenConditions',
            'accessoriesOptions',
            'supportedBanks',
            'myTradeIns'
        ));
    }

    /**
     * Calculate estimated trade-in price dynamically via AJAX.
     */
    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone_model' => 'required|string|max:100',
            'storage_capacity' => 'required|string|max:20',
            'battery_health' => 'required|integer|min:50|max:100',
            'physical_condition' => 'required|string|max:50',
            'screen_condition' => 'required|string|max:50',
            'accessories_status' => 'required|string|max:50',
        ]);

        $breakdown = $this->calculatorService->calculateEstimate($validated);

        return response()->json([
            'success' => true,
            'breakdown' => $breakdown,
            'formatted_estimated_price' => 'Rp ' . number_format($breakdown['estimated_price'], 0, ',', '.'),
        ]);
    }

    /**
     * Store a newly submitted Trade-In application from the 4-step wizard.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'phone_model' => 'required|string|max:100',
            'storage_capacity' => 'required|string|max:20',
            'battery_health' => 'required|integer|min:50|max:100',
            'physical_condition' => 'required|string|max:50',
            'screen_condition' => 'required|string|max:50',
            'accessories_status' => 'required|string|max:50',
            'imei' => 'nullable|string|max:30',
            'payout_method' => 'required|in:bank_transfer,voucher',
            'customer_bank_name' => 'required_if:payout_method,bank_transfer|nullable|string|max:50',
            'customer_account_number' => 'required_if:payout_method,bank_transfer|nullable|string|max:50',
            'customer_account_holder' => 'required_if:payout_method,bank_transfer|nullable|string|max:100',
        ]);

        // Calculate authoritative server-side estimate
        $breakdown = $this->calculatorService->calculateEstimate($validated);

        // Generate Unique Trade-In Code: TI-YYYYMMDD-XXXX
        $datePrefix = date('Ymd');
        $countToday = TradeIn::whereDate('created_at', today())->count() + 1;
        $uniqueCode = str_pad((string) $countToday, 3, '0', STR_PAD_LEFT);
        $tradeInCode = "TI-{$datePrefix}-{$uniqueCode}";

        if (TradeIn::where('trade_in_code', $tradeInCode)->exists()) {
            $tradeInCode = "TI-{$datePrefix}-" . strtoupper(Str::random(4));
        }

        $isVoucher = $validated['payout_method'] === 'voucher';

        $bankName = $isVoucher
            ? TradeIn::VOUCHER_BANK_MARKER
            : $validated['customer_bank_name'];

        $accountNumber = $isVoucher
            ? 'VCH-' . strtoupper(Str::random(6))
            : $validated['customer_account_number'];

        $accountHolder = $isVoucher
            ? (auth()->user()->name ?? 'Customer Rap-iPhone')
            : $validated['customer_account_holder'];

        $tradeIn = TradeIn::create([
            'trade_in_code' => $tradeInCode,
            'user_id' => auth()->id(),
            'target_order_id' => null,
            'phone_model' => $validated['phone_model'],
            'storage_capacity' => $validated['storage_capacity'],
            'battery_health' => (int) $validated['battery_health'],
            'physical_condition' => $validated['physical_condition'],
            'screen_condition' => $validated['screen_condition'],
            'accessories_status' => $validated['accessories_status'],
            'imei' => $validated['imei'] ?? null,
            'estimated_price' => $breakdown['estimated_price'],
            'final_price' => null,
            'status' => 'submitted',
            'customer_bank_name' => $bankName,
            'customer_account_number' => $accountNumber,
            'customer_account_holder' => $accountHolder,
        ]);

        // Trigger simultaneous WhatsApp & Email Trade-In status notification (Phase 8)
        $this->notificationOrchestrator->notifyTradeInStatus($tradeIn);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'trade_in_code' => $tradeIn->trade_in_code,
                'estimated_price' => (float) $tradeIn->estimated_price,
                'redirect_url' => route('trade-in.show', $tradeIn->trade_in_code),
                'message' => 'Pengajuan Tukar Tambah berhasil dibuat.',
            ]);
        }

        return redirect()
            ->route('trade-in.show', $tradeIn->trade_in_code)
            ->with('success', "Pengajuan Tukar Tambah ({$tradeIn->trade_in_code}) berhasil dikirim! Silakan bawa atau kirimkan unit Anda ke Store Rap-iPhone untuk inspeksi fisik 15 menit.");
    }

    /**
     * Display the customer's Trade-In submission detail and inspection status tracker.
     */
    public function show(TradeIn $tradeIn): View
    {
        abort_unless(
            $tradeIn->user_id === auth()->id() || (auth()->check() && auth()->user()->hasAdminAccess()),
            403,
            'Anda tidak memiliki akses untuk melihat pengajuan Trade-In ini.'
        );

        $tradeIn->load(['user', 'targetOrder']);

        $breakdown = $this->calculatorService->calculateEstimate([
            'phone_model' => $tradeIn->phone_model,
            'storage_capacity' => $tradeIn->storage_capacity,
            'battery_health' => $tradeIn->battery_health,
            'physical_condition' => $tradeIn->physical_condition,
            'screen_condition' => $tradeIn->screen_condition,
            'accessories_status' => $tradeIn->accessories_status,
        ]);

        return view('customer.trade-in-detail', compact('tradeIn', 'breakdown'));
    }
}
