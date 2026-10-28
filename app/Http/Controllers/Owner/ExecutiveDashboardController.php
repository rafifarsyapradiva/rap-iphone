<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\FinancialAnalyticsService;
use Illuminate\View\View;

class ExecutiveDashboardController extends Controller
{
    public function __construct(protected FinancialAnalyticsService $analyticsService)
    {
    }

    /**
     * Tampilkan Portal Finansial & Analitik Laba Pemilik Usaha (Owner).
     */
    public function index(): View
    {
        $metrics = $this->analyticsService->getExecutiveMetrics();
        $cashFlow = $this->analyticsService->getCashFlowSummary();
        $salesTrend = $this->analyticsService->getSalesTrend30Days();
        $topProducts = $this->analyticsService->getTopSellingProducts(5);
        $conversion = $this->analyticsService->getOrderConversionBreakdown();

        $totalRevenue = $metrics['all_time']['revenue'];
        $totalAdmins = User::where('role', 'admin')->count();
        $totalOwners = User::where('role', 'owner')->count();
        $totalCustomers = User::where('role', 'customer')->count();

        $recentRealizedOrders = Order::with(['user', 'items'])
            ->whereIn('status', FinancialAnalyticsService::REALIZED_STATUSES)
            ->latest('created_at')
            ->take(6)
            ->get()
            ->map(function (Order $order) {
                $cogs = (float) $order->items->sum(fn (OrderItem $i) => (float) $i->cost_price * (int) $i->quantity);
                $grossProfit = (float) $order->items->sum(fn (OrderItem $i) => ((float) $i->price - (float) $i->cost_price) * (int) $i->quantity);
                $netProfit = $grossProfit - (float) $order->discount_amount;

                $order->setAttribute('calculated_cogs', $cogs);
                $order->setAttribute('calculated_net_profit', $netProfit);

                return $order;
            });

        return view('owner.dashboard', compact(
            'metrics',
            'cashFlow',
            'salesTrend',
            'topProducts',
            'conversion',
            'totalRevenue',
            'totalAdmins',
            'totalOwners',
            'totalCustomers',
            'recentRealizedOrders'
        ));
    }
}

