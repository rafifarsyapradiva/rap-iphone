<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TradeIn;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FinancialAnalyticsService
{
    /**
     * Status pesanan yang diakui sebagai transaksi penjualan sah (lunas).
     */
    public const REALIZED_STATUSES = ['paid', 'processing', 'shipped', 'completed'];

    /**
     * Status Trade-In yang diakui sebagai akuisisi unit / pencairan dana sah.
     */
    public const REALIZED_TRADE_IN_STATUSES = ['approved', 'paid_out'];

    /**
     * Status Trade-In yang masih berada dalam antrean operasional.
     */
    public const PENDING_TRADE_IN_STATUSES = ['submitted', 'unit_received', 'inspected'];

    /**
     * Konversi filter periode ('today', 'week', 'month', 'year', 'all', 'custom') menjadi rentang Carbon.
     *
     * @return array{period: string, start: ?Carbon, end: ?Carbon, label: string, start_input: string, end_input: string}
     */
    public function resolveDateRange(
        string $period = 'month',
        ?string $startDate = null,
        ?string $endDate = null,
        ?Carbon $referenceDate = null
    ): array {
        $now = ($referenceDate ?? now())->copy();

        if ($period === 'custom' && ($startDate || $endDate)) {
            $start = $startDate ? Carbon::parse($startDate)->startOfDay() : $now->copy()->startOfMonth();
            $end = $endDate ? Carbon::parse($endDate)->endOfDay() : $now->copy()->endOfDay();

            if ($start->greaterThan($end)) {
                [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
            }

            return [
                'period' => 'custom',
                'start' => $start,
                'end' => $end,
                'label' => $start->format('d M Y') . ' — ' . $end->format('d M Y'),
                'start_input' => $start->format('Y-m-d'),
                'end_input' => $end->format('Y-m-d'),
            ];
        }

        return match ($period) {
            'today' => [
                'period' => 'today',
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
                'label' => 'Hari Ini (' . $now->format('d M Y') . ')',
                'start_input' => $now->format('Y-m-d'),
                'end_input' => $now->format('Y-m-d'),
            ],
            'week' => [
                'period' => 'week',
                'start' => $now->copy()->subDays(6)->startOfDay(),
                'end' => $now->copy()->endOfDay(),
                'label' => '7 Hari Terakhir (' . $now->copy()->subDays(6)->format('d M') . ' — ' . $now->format('d M Y') . ')',
                'start_input' => $now->copy()->subDays(6)->format('Y-m-d'),
                'end_input' => $now->format('Y-m-d'),
            ],
            'year' => [
                'period' => 'year',
                'start' => $now->copy()->startOfYear(),
                'end' => $now->copy()->endOfYear(),
                'label' => 'Tahun ' . $now->format('Y'),
                'start_input' => $now->copy()->startOfYear()->format('Y-m-d'),
                'end_input' => $now->format('Y-m-d'),
            ],
            'all' => [
                'period' => 'all',
                'start' => null,
                'end' => null,
                'label' => 'Seluruh Periode (Kumulatif)',
                'start_input' => '',
                'end_input' => '',
            ],
            default => [
                'period' => 'month',
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
                'label' => 'Bulan Ini (' . $now->translatedFormat('F Y') . ')',
                'start_input' => $now->copy()->startOfMonth()->format('Y-m-d'),
                'end_input' => $now->format('Y-m-d'),
            ],
        };
    }

    /**
     * Hitung metrik keuangan utama (Omzet, HPP, Diskon, Laba Kotor, Laba Bersih) pada rentang waktu tertentu.
     *
     * @return array{
     *     orders_count: int,
     *     units_sold: int,
     *     gross_items_sales: float,
     *     shipping_revenue: float,
     *     discount_total: float,
     *     revenue: float,
     *     net_product_sales: float,
     *     cogs: float,
     *     gross_item_profit: float,
     *     net_profit: float,
     *     profit_margin: float,
     *     average_order_value: float
     * }
     */
    public function calculatePeriodFinancials(?Carbon $start = null, ?Carbon $end = null): array
    {
        $ordersQuery = Order::query()->whereIn('status', self::REALIZED_STATUSES);

        if ($start && $end) {
            $ordersQuery->whereBetween('created_at', [$start, $end]);
        } elseif ($start) {
            $ordersQuery->where('created_at', '>=', $start);
        } elseif ($end) {
            $ordersQuery->where('created_at', '<=', $end);
        }

        $orderAggregate = (clone $ordersQuery)
            ->selectRaw('
                COUNT(*) as orders_count,
                COALESCE(SUM(total_items_price), 0) as gross_items_sales,
                COALESCE(SUM(shipping_cost), 0) as shipping_revenue,
                COALESCE(SUM(discount_amount), 0) as discount_total,
                COALESCE(SUM(grand_total), 0) as revenue
            ')
            ->first();

        $itemsQuery = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.status', self::REALIZED_STATUSES);

        if ($start && $end) {
            $itemsQuery->whereBetween('orders.created_at', [$start, $end]);
        } elseif ($start) {
            $itemsQuery->where('orders.created_at', '>=', $start);
        } elseif ($end) {
            $itemsQuery->where('orders.created_at', '<=', $end);
        }

        $itemAggregate = $itemsQuery
            ->selectRaw('
                COALESCE(SUM(order_items.quantity), 0) as units_sold,
                COALESCE(SUM(order_items.price * order_items.quantity), 0) as items_revenue,
                COALESCE(SUM(order_items.cost_price * order_items.quantity), 0) as cogs,
                COALESCE(SUM((order_items.price - order_items.cost_price) * order_items.quantity), 0) as gross_item_profit
            ')
            ->first();

        $ordersCount = (int) ($orderAggregate->orders_count ?? 0);
        $unitsSold = (int) ($itemAggregate->units_sold ?? 0);
        $grossItemsSales = (float) ($orderAggregate->gross_items_sales ?? 0);
        $shippingRevenue = (float) ($orderAggregate->shipping_revenue ?? 0);
        $discountTotal = (float) ($orderAggregate->discount_total ?? 0);
        $revenue = (float) ($orderAggregate->revenue ?? 0);
        $cogs = (float) ($itemAggregate->cogs ?? 0);
        $grossItemProfit = (float) ($itemAggregate->gross_item_profit ?? 0);

        // Laba Bersih Otomatis = Laba Kotor Item (Harga Jual - HPP) dikurangi Diskon Pesanan
        $netProfit = $grossItemProfit - $discountTotal;
        $netProductSales = max(0.0, $grossItemsSales - $discountTotal);
        $profitMargin = $revenue > 0 ? round(($netProfit / $revenue) * 100, 1) : 0.0;
        $averageOrderValue = $ordersCount > 0 ? round($revenue / $ordersCount, 2) : 0.0;

        return [
            'orders_count' => $ordersCount,
            'units_sold' => $unitsSold,
            'gross_items_sales' => $grossItemsSales,
            'shipping_revenue' => $shippingRevenue,
            'discount_total' => $discountTotal,
            'revenue' => $revenue,
            'net_product_sales' => $netProductSales,
            'cogs' => $cogs,
            'gross_item_profit' => $grossItemProfit,
            'net_profit' => $netProfit,
            'profit_margin' => $profitMargin,
            'average_order_value' => $averageOrderValue,
        ];
    }

    /**
     * Ambil metrik keuangan eksekutif lengkap (Harian, Mingguan, Bulanan, dan Kumulatif) beserta persentase pertumbuhan.
     *
     * @return array<string, mixed>
     */
    public function getExecutiveMetrics(?Carbon $referenceDate = null): array
    {
        $now = ($referenceDate ?? now())->copy();

        // 1. Harian (Hari Ini vs Kemarin)
        $today = $this->calculatePeriodFinancials(
            $now->copy()->startOfDay(),
            $now->copy()->endOfDay()
        );
        $yesterday = $this->calculatePeriodFinancials(
            $now->copy()->subDay()->startOfDay(),
            $now->copy()->subDay()->endOfDay()
        );
        $today['previous_revenue'] = $yesterday['revenue'];
        $today['previous_profit'] = $yesterday['net_profit'];
        $today['revenue_growth'] = $this->calculateGrowthPercentage($today['revenue'], $yesterday['revenue']);
        $today['profit_growth'] = $this->calculateGrowthPercentage($today['net_profit'], $yesterday['net_profit']);

        // 2. Mingguan (7 Hari Terakhir vs 7 Hari Sebelumnya)
        $thisWeek = $this->calculatePeriodFinancials(
            $now->copy()->subDays(6)->startOfDay(),
            $now->copy()->endOfDay()
        );
        $lastWeek = $this->calculatePeriodFinancials(
            $now->copy()->subDays(13)->startOfDay(),
            $now->copy()->subDays(7)->endOfDay()
        );
        $thisWeek['previous_revenue'] = $lastWeek['revenue'];
        $thisWeek['previous_profit'] = $lastWeek['net_profit'];
        $thisWeek['revenue_growth'] = $this->calculateGrowthPercentage($thisWeek['revenue'], $lastWeek['revenue']);
        $thisWeek['profit_growth'] = $this->calculateGrowthPercentage($thisWeek['net_profit'], $lastWeek['net_profit']);

        // 3. Bulanan (Bulan Berjalan vs Bulan Lalu)
        $thisMonth = $this->calculatePeriodFinancials(
            $now->copy()->startOfMonth(),
            $now->copy()->endOfMonth()
        );
        $lastMonth = $this->calculatePeriodFinancials(
            $now->copy()->subMonthNoOverflow()->startOfMonth(),
            $now->copy()->subMonthNoOverflow()->endOfMonth()
        );
        $thisMonth['previous_revenue'] = $lastMonth['revenue'];
        $thisMonth['previous_profit'] = $lastMonth['net_profit'];
        $thisMonth['revenue_growth'] = $this->calculateGrowthPercentage($thisMonth['revenue'], $lastMonth['revenue']);
        $thisMonth['profit_growth'] = $this->calculateGrowthPercentage($thisMonth['net_profit'], $lastMonth['net_profit']);

        // 4. Kumulatif Seluruh Waktu (All-Time)
        $allTime = $this->calculatePeriodFinancials();

        return [
            'daily' => $today,
            'weekly' => $thisWeek,
            'monthly' => $thisMonth,
            'all_time' => $allTime,
        ];
    }

    /**
     * Ringkasan Arus Kas: Unit Trade-In Masuk (Kas/Voucher Keluar) vs Penjualan Unit Keluar (Kas Masuk).
     *
     * @return array<string, mixed>
     */
    public function getCashFlowSummary(?Carbon $start = null, ?Carbon $end = null): array
    {
        $sales = $this->calculatePeriodFinancials($start, $end);

        $tradeInsQuery = TradeIn::query();
        if ($start && $end) {
            $tradeInsQuery->whereBetween('created_at', [$start, $end]);
        } elseif ($start) {
            $tradeInsQuery->where('created_at', '>=', $start);
        } elseif ($end) {
            $tradeInsQuery->where('created_at', '<=', $end);
        }

        $realizedTradeIns = (clone $tradeInsQuery)
            ->whereIn('status', self::REALIZED_TRADE_IN_STATUSES)
            ->get();

        $pendingTradeIns = (clone $tradeInsQuery)
            ->whereIn('status', self::PENDING_TRADE_IN_STATUSES)
            ->get();

        $tradeInUnitsIn = $realizedTradeIns->count();
        $tradeInTotalValue = (float) $realizedTradeIns->sum(fn (TradeIn $t) => $t->effective_price);
        $tradeInCashOut = (float) $realizedTradeIns
            ->filter(fn (TradeIn $t) => !$t->isVoucherPayout())
            ->sum(fn (TradeIn $t) => $t->effective_price);
        $tradeInVoucherOut = (float) $realizedTradeIns
            ->filter(fn (TradeIn $t) => $t->isVoucherPayout())
            ->sum(fn (TradeIn $t) => $t->effective_price);

        $paidOutCount = $realizedTradeIns->where('status', 'paid_out')->count();
        $approvedCount = $realizedTradeIns->where('status', 'approved')->count();

        $pendingCount = $pendingTradeIns->count();
        $pendingEstimatedValue = (float) $pendingTradeIns->sum(fn (TradeIn $t) => $t->effective_price);

        $salesCashIn = $sales['revenue'];
        $netCashFlow = $salesCashIn - $tradeInTotalValue;
        $netLiquidCashFlow = $salesCashIn - $tradeInCashOut;

        $totalVolume = $salesCashIn + $tradeInTotalValue;
        $inflowRatioPct = $totalVolume > 0 ? round(($salesCashIn / $totalVolume) * 100, 1) : 100.0;
        $outflowRatioPct = $totalVolume > 0 ? round(($tradeInTotalValue / $totalVolume) * 100, 1) : 0.0;

        return [
            // Penjualan Keluar (Kas Masuk)
            'sales_cash_in' => $salesCashIn,
            'sales_net_product' => $sales['net_product_sales'],
            'sales_orders_count' => $sales['orders_count'],
            'sales_units_out' => $sales['units_sold'],
            'sales_cogs' => $sales['cogs'],
            'sales_net_profit' => $sales['net_profit'],

            // Trade-In Masuk (Kas / Voucher Keluar)
            'trade_in_units_in' => $tradeInUnitsIn,
            'trade_in_total_value' => $tradeInTotalValue,
            'trade_in_cash_out' => $tradeInCashOut,
            'trade_in_voucher_out' => $tradeInVoucherOut,
            'trade_in_paid_out_count' => $paidOutCount,
            'trade_in_approved_count' => $approvedCount,
            'trade_in_pending_count' => $pendingCount,
            'trade_in_pending_estimated' => $pendingEstimatedValue,

            // Selisih & Rasio Arus Kas
            'net_cash_flow' => $netCashFlow,
            'net_liquid_cash_flow' => $netLiquidCashFlow,
            'inflow_ratio_pct' => $inflowRatioPct,
            'outflow_ratio_pct' => $outflowRatioPct,
        ];
    }

    /**
     * Data grafik tren transaksi penjualan dan laba bersih selama 30 hari terakhir.
     *
     * @return array{
     *     days: array<int, array{date: string, label: string, full_label: string, revenue: float, cogs: float, net_profit: float, orders_count: int, units_sold: int}>,
     *     summary: array{total_revenue_30d: float, total_profit_30d: float, total_cogs_30d: float, total_orders_30d: int, total_units_30d: int, max_revenue: float, max_profit: float, peak_day: ?array<string, mixed>}
     * }
     */
    public function getSalesTrend30Days(?Carbon $endDate = null): array
    {
        $end = ($endDate ?? now())->copy()->endOfDay();
        $start = $end->copy()->subDays(29)->startOfDay();

        $orders = Order::with('items')
            ->whereIn('status', self::REALIZED_STATUSES)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $groupedByDate = $orders->groupBy(fn (Order $order) => $order->created_at->format('Y-m-d'));

        $days = [];
        $totalRevenue30d = 0.0;
        $totalProfit30d = 0.0;
        $totalCogs30d = 0.0;
        $totalOrders30d = 0;
        $totalUnits30d = 0;
        $maxRevenue = 0.0;
        $maxProfit = 0.0;
        $peakDay = null;

        for ($i = 0; $i < 30; $i++) {
            $cursor = $start->copy()->addDays($i);
            $dateKey = $cursor->format('Y-m-d');
            /** @var Collection<int, Order> $dayOrders */
            $dayOrders = $groupedByDate->get($dateKey, collect());

            $dayRevenue = 0.0;
            $dayCogs = 0.0;
            $dayGrossProfit = 0.0;
            $dayDiscount = 0.0;
            $dayUnits = 0;

            foreach ($dayOrders as $order) {
                $dayRevenue += (float) $order->grand_total;
                $dayDiscount += (float) $order->discount_amount;

                foreach ($order->items as $item) {
                    $qty = (int) $item->quantity;
                    $price = (float) $item->price;
                    $cost = (float) $item->cost_price;

                    $dayUnits += $qty;
                    $dayCogs += ($cost * $qty);
                    $dayGrossProfit += (($price - $cost) * $qty);
                }
            }

            $dayNetProfit = $dayGrossProfit - $dayDiscount;
            $dayOrdersCount = $dayOrders->count();

            $dayEntry = [
                'date' => $dateKey,
                'label' => $cursor->format('d M'),
                'full_label' => $cursor->format('d M Y'),
                'revenue' => round($dayRevenue, 2),
                'cogs' => round($dayCogs, 2),
                'net_profit' => round($dayNetProfit, 2),
                'orders_count' => $dayOrdersCount,
                'units_sold' => $dayUnits,
            ];

            $days[] = $dayEntry;

            $totalRevenue30d += $dayRevenue;
            $totalProfit30d += $dayNetProfit;
            $totalCogs30d += $dayCogs;
            $totalOrders30d += $dayOrdersCount;
            $totalUnits30d += $dayUnits;

            if ($dayRevenue > $maxRevenue) {
                $maxRevenue = $dayRevenue;
                $peakDay = $dayEntry;
            }

            if ($dayNetProfit > $maxProfit) {
                $maxProfit = $dayNetProfit;
            }
        }

        return [
            'days' => $days,
            'summary' => [
                'total_revenue_30d' => round($totalRevenue30d, 2),
                'total_profit_30d' => round($totalProfit30d, 2),
                'total_cogs_30d' => round($totalCogs30d, 2),
                'total_orders_30d' => $totalOrders30d,
                'total_units_30d' => $totalUnits30d,
                'max_revenue' => max($maxRevenue, 1.0),
                'max_profit' => max($maxProfit, 1.0),
                'peak_day' => $peakDay,
            ],
        ];
    }

    /**
     * Analisis produk iPhone terlaris (Top Selling iPhone models) berdasarkan unit terjual, omzet, dan laba.
     *
     * @return Collection<int, array{
     *     rank: int,
     *     product_name: string,
     *     series: string,
     *     condition: string,
     *     popular_variant: string,
     *     units_sold: int,
     *     orders_count: int,
     *     revenue: float,
     *     cogs: float,
     *     gross_profit: float,
     *     profit_margin: float,
     *     share_pct: float
     * }>
     */
    public function getTopSellingProducts(int $limit = 5, ?Carbon $start = null, ?Carbon $end = null): Collection
    {
        $itemsQuery = OrderItem::query()
            ->with(['variant.product'])
            ->whereHas('order', function ($q) use ($start, $end) {
                $q->whereIn('status', self::REALIZED_STATUSES);
                if ($start && $end) {
                    $q->whereBetween('created_at', [$start, $end]);
                } elseif ($start) {
                    $q->where('created_at', '>=', $start);
                } elseif ($end) {
                    $q->where('created_at', '<=', $end);
                }
            });

        $items = $itemsQuery->get();

        if ($items->isEmpty()) {
            return collect();
        }

        $totalUnitsAll = max(1, (int) $items->sum('quantity'));

        $grouped = $items->groupBy(fn (OrderItem $item) => $item->product_name);

        $ranked = $grouped->map(function (Collection $group, string $productName) use ($totalUnitsAll) {
            /** @var OrderItem $firstItem */
            $firstItem = $group->first();
            $product = $firstItem->variant?->product;

            $unitsSold = (int) $group->sum('quantity');
            $ordersCount = $group->pluck('order_id')->unique()->count();
            $revenue = (float) $group->sum(fn (OrderItem $i) => (float) $i->price * (int) $i->quantity);
            $cogs = (float) $group->sum(fn (OrderItem $i) => (float) $i->cost_price * (int) $i->quantity);
            $grossProfit = $revenue - $cogs;
            $profitMargin = $revenue > 0 ? round(($grossProfit / $revenue) * 100, 1) : 0.0;
            $sharePct = round(($unitsSold / $totalUnitsAll) * 100, 1);

            // Cari varian paling sering dibeli pada model ini
            $popularVariant = $group
                ->groupBy('variant_details')
                ->sortByDesc(fn (Collection $vGroup) => $vGroup->sum('quantity'))
                ->keys()
                ->first() ?? '-';

            return [
                'product_name' => $productName,
                'series' => $product->series ?? 'Apple iPhone',
                'condition' => $product->condition ?? 'new',
                'popular_variant' => (string) $popularVariant,
                'units_sold' => $unitsSold,
                'orders_count' => $ordersCount,
                'revenue' => round($revenue, 2),
                'cogs' => round($cogs, 2),
                'gross_profit' => round($grossProfit, 2),
                'profit_margin' => $profitMargin,
                'share_pct' => $sharePct,
            ];
        })
            ->sort(function (array $a, array $b) {
                if ($b['units_sold'] !== $a['units_sold']) {
                    return $b['units_sold'] <=> $a['units_sold'];
                }
                return $b['revenue'] <=> $a['revenue'];
            })
            ->values()
            ->take($limit)
            ->map(function (array $row, int $idx) {
                $row['rank'] = $idx + 1;
                return $row;
            });

        return $ranked;
    }

    /**
     * Ringkasan konversi transaksi berdasarkan status pesanan.
     *
     * @return array<string, mixed>
     */
    public function getOrderConversionBreakdown(?Carbon $start = null, ?Carbon $end = null): array
    {
        $query = Order::query();

        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        } elseif ($start) {
            $query->where('created_at', '>=', $start);
        } elseif ($end) {
            $query->where('created_at', '<=', $end);
        }

        $counts = $query
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $pending = (int) ($counts['pending'] ?? 0);
        $paid = (int) ($counts['paid'] ?? 0);
        $processing = (int) ($counts['processing'] ?? 0);
        $shipped = (int) ($counts['shipped'] ?? 0);
        $completed = (int) ($counts['completed'] ?? 0);
        $cancelled = (int) ($counts['cancelled'] ?? 0);

        $totalOrders = $pending + $paid + $processing + $shipped + $completed + $cancelled;
        $realizedOrders = $paid + $processing + $shipped + $completed;

        $conversionRate = $totalOrders > 0 ? round(($realizedOrders / $totalOrders) * 100, 1) : 0.0;
        $cancellationRate = $totalOrders > 0 ? round(($cancelled / $totalOrders) * 100, 1) : 0.0;

        return [
            'total_orders' => $totalOrders,
            'realized_orders' => $realizedOrders,
            'pending' => $pending,
            'paid' => $paid,
            'processing' => $processing,
            'shipped' => $shipped,
            'completed' => $completed,
            'cancelled' => $cancelled,
            'conversion_rate' => $conversionRate,
            'cancellation_rate' => $cancellationRate,
        ];
    }

    /**
     * Ambil paket data lengkap untuk halaman Laporan Penjualan & Laba Rugi serta Ekspor PDF / CSV.
     *
     * @return array<string, mixed>
     */
    public function getDetailedFinancialReport(?Carbon $start = null, ?Carbon $end = null): array
    {
        $summary = $this->calculatePeriodFinancials($start, $end);
        $cashFlow = $this->getCashFlowSummary($start, $end);
        $topProducts = $this->getTopSellingProducts(10, $start, $end);
        $conversion = $this->getOrderConversionBreakdown($start, $end);

        $ordersQuery = Order::with(['user', 'items.variant.product', 'payment'])
            ->whereIn('status', self::REALIZED_STATUSES)
            ->latest('created_at');

        if ($start && $end) {
            $ordersQuery->whereBetween('created_at', [$start, $end]);
        } elseif ($start) {
            $ordersQuery->where('created_at', '>=', $start);
        } elseif ($end) {
            $ordersQuery->where('created_at', '<=', $end);
        }

        $orders = $ordersQuery->get()->map(function (Order $order) {
            $orderCogs = 0.0;
            $orderGrossProfit = 0.0;
            $unitsCount = 0;

            foreach ($order->items as $item) {
                $qty = (int) $item->quantity;
                $price = (float) $item->price;
                $cost = (float) $item->cost_price;

                $unitsCount += $qty;
                $orderCogs += ($cost * $qty);
                $orderGrossProfit += (($price - $cost) * $qty);
            }

            $discount = (float) $order->discount_amount;
            $revenue = (float) $order->grand_total;
            $orderNetProfit = $orderGrossProfit - $discount;
            $orderMargin = $revenue > 0 ? round(($orderNetProfit / $revenue) * 100, 1) : 0.0;

            $itemsSummary = $order->items
                ->map(fn (OrderItem $i) => "{$i->quantity}x {$i->product_name} ({$i->variant_details})")
                ->implode('; ');

            $order->setAttribute('calculated_units', $unitsCount);
            $order->setAttribute('calculated_cogs', round($orderCogs, 2));
            $order->setAttribute('calculated_gross_profit', round($orderGrossProfit, 2));
            $order->setAttribute('calculated_net_profit', round($orderNetProfit, 2));
            $order->setAttribute('calculated_margin', $orderMargin);
            $order->setAttribute('items_summary', $itemsSummary);

            return $order;
        });

        $tradeInsQuery = TradeIn::with(['user', 'targetOrder'])
            ->whereIn('status', self::REALIZED_TRADE_IN_STATUSES)
            ->latest('created_at');

        if ($start && $end) {
            $tradeInsQuery->whereBetween('created_at', [$start, $end]);
        } elseif ($start) {
            $tradeInsQuery->where('created_at', '>=', $start);
        } elseif ($end) {
            $tradeInsQuery->where('created_at', '<=', $end);
        }

        $tradeIns = $tradeInsQuery->get();

        return [
            'summary' => $summary,
            'cash_flow' => $cashFlow,
            'top_products' => $topProducts,
            'conversion' => $conversion,
            'orders' => $orders,
            'trade_ins' => $tradeIns,
        ];
    }

    /**
     * Hitung persentase pertumbuhan antara nilai periode saat ini dan periode sebelumnya.
     */
    public function calculateGrowthPercentage(float $current, float $previous): float
    {
        if ($previous <= 0.0) {
            return $current > 0.0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}

