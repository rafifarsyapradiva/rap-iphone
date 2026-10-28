@extends('layouts.admin')

@section('title', 'Portal Finansial & Analitik Laba — Owner Rap-iPhone')
@section('page_title', 'Eksekutif Finansial & Omzet (Owner Only)')

@section('content')
@php
    $daily = $metrics['daily'] ?? [];
    $weekly = $metrics['weekly'] ?? [];
    $monthly = $metrics['monthly'] ?? [];
    $allTime = $metrics['all_time'] ?? [];
    $trendDays = $salesTrend['days'] ?? [];
    $trendSummary = $salesTrend['summary'] ?? [];
@endphp

<div class="space-y-8">

    <!-- 1. Owner Executive Banner -->
    <div class="relative overflow-hidden rounded-3xl p-6 sm:p-8 bg-white border border-[#E5E5EA] shadow-sm">
        <div class="relative z-10 flex flex-col xl:flex-row xl:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-[#008009] text-xs font-semibold mb-3">
                    <i data-lucide="crown" class="w-3.5 h-3.5"></i>
                    <span>Tingkat Akses Tertinggi: Pemilik Usaha (Owner)</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1D1D1F] tracking-tight">
                    Portal Finansial &amp; Analitik Laba
                </h1>
                <p class="text-xs sm:text-sm text-[#6E6E73] mt-1.5 max-w-2xl leading-relaxed">
                    Pantau total omzet penjualan (Harian, Mingguan, Bulanan), kalkulasi otomatis laba bersih terhadap HPP unit iPhone, arus kas Trade-In masuk vs penjualan keluar, serta tren bisnis 30 hari terakhir.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ route('owner.financial.report') }}" class="glass-btn glass-btn-primary glass-btn-sm flex items-center gap-2">
                    <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5"></i>
                    <span>Laporan &amp; Laba Rugi</span>
                </a>
                <a href="{{ route('owner.financial.export.csv') }}" class="glass-btn glass-btn-secondary glass-btn-sm flex items-center gap-2">
                    <i data-lucide="download" class="w-3.5 h-3.5 text-[#008009]"></i>
                    <span>Ekspor CSV / Excel</span>
                </a>
                <a href="{{ route('owner.financial.export.pdf') }}" target="_blank" class="glass-btn glass-btn-secondary glass-btn-sm flex items-center gap-2">
                    <i data-lucide="file-text" class="w-3.5 h-3.5 text-[#BF4800]"></i>
                    <span>Ekspor PDF</span>
                </a>
                <a href="{{ route('owner.staff.index') }}" class="glass-btn glass-btn-secondary glass-btn-sm flex items-center gap-2">
                    <i data-lucide="users" class="w-3.5 h-3.5 text-[#0066CC]"></i>
                    <span>Kelola Staf ({{ ($totalAdmins ?? 0) + ($totalOwners ?? 1) }})</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. (Phase 11.1) Metrik Keuangan Eksekutif: Omzet Harian, Mingguan, Bulanan & Kalkulasi Laba Bersih Otomatis -->
    <div class="space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-base font-bold text-[#1D1D1F] flex items-center gap-2">
                    <i data-lucide="wallet" class="w-4 h-4 text-[#0071E3]"></i>
                    <span>Metrik Keuangan Eksekutif &amp; Kalkulasi Laba Bersih Otomatis</span>
                </h2>
                <p class="text-xs text-[#6E6E73]">
                    Dihitung otomatis dari transaksi berstatus Lunas (<code class="text-[#0066CC] font-semibold">paid, processing, shipped, completed</code>).
                </p>
            </div>
            <span class="text-[11px] font-semibold text-[#6E6E73] bg-white border border-[#E5E5EA] px-3 py-1 rounded-full">
                Total Kumulatif Lunas: <strong class="text-[#1D1D1F]">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</strong>
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
            <!-- Card 1: Omzet Penjualan Harian -->
            <div class="bg-white rounded-2xl p-5 border border-[#E5E5EA] shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-[#6E6E73] uppercase tracking-wider">Omzet Hari Ini (Harian)</span>
                        <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-200 text-[#0066CC] flex items-center justify-center">
                            <i data-lucide="calendar-clock" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-extrabold text-[#1D1D1F] mt-3 tracking-tight">
                        Rp {{ number_format($daily['revenue'] ?? 0, 0, ',', '.') }}
                    </p>
                    <div class="mt-2 flex items-center gap-2 text-xs">
                        @php $dailyGrowth = (float) ($daily['revenue_growth'] ?? 0); @endphp
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $dailyGrowth >= 0 ? 'bg-emerald-50 text-[#008009]' : 'bg-red-50 text-[#E30000]' }}">
                            <i data-lucide="{{ $dailyGrowth >= 0 ? 'trending-up' : 'trending-down' }}" class="w-3 h-3"></i>
                            {{ $dailyGrowth >= 0 ? '+' : '' }}{{ $dailyGrowth }}%
                        </span>
                        <span class="text-[#6E6E73] text-[11px]">vs kemarin</span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-[#E5E5EA] flex items-center justify-between text-xs">
                    <span class="text-[#6E6E73]">Laba Bersih Hari Ini:</span>
                    <span class="font-bold text-[#008009]">Rp {{ number_format($daily['net_profit'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Card 2: Omzet Penjualan Mingguan -->
            <div class="bg-white rounded-2xl p-5 border border-[#E5E5EA] shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-[#6E6E73] uppercase tracking-wider">Omzet 7 Hari (Mingguan)</span>
                        <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-200 text-[#0066CC] flex items-center justify-center">
                            <i data-lucide="calendar-range" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-extrabold text-[#1D1D1F] mt-3 tracking-tight">
                        Rp {{ number_format($weekly['revenue'] ?? 0, 0, ',', '.') }}
                    </p>
                    <div class="mt-2 flex items-center gap-2 text-xs">
                        @php $weeklyGrowth = (float) ($weekly['revenue_growth'] ?? 0); @endphp
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $weeklyGrowth >= 0 ? 'bg-emerald-50 text-[#008009]' : 'bg-red-50 text-[#E30000]' }}">
                            <i data-lucide="{{ $weeklyGrowth >= 0 ? 'trending-up' : 'trending-down' }}" class="w-3 h-3"></i>
                            {{ $weeklyGrowth >= 0 ? '+' : '' }}{{ $weeklyGrowth }}%
                        </span>
                        <span class="text-[#6E6E73] text-[11px]">{{ $weekly['orders_count'] ?? 0 }} pesanan ({{ $weekly['units_sold'] ?? 0 }} unit)</span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-[#E5E5EA] flex items-center justify-between text-xs">
                    <span class="text-[#6E6E73]">Laba Bersih Mingguan:</span>
                    <span class="font-bold text-[#008009]">Rp {{ number_format($weekly['net_profit'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Card 3: Omzet Penjualan Bulanan -->
            <div class="bg-white rounded-2xl p-5 border border-[#E5E5EA] shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-[#6E6E73] uppercase tracking-wider">Omzet Bulan Ini (Bulanan)</span>
                        <div class="w-9 h-9 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA] text-[#1D1D1F] flex items-center justify-center">
                            <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-extrabold text-[#1D1D1F] mt-3 tracking-tight">
                        Rp {{ number_format($monthly['revenue'] ?? 0, 0, ',', '.') }}
                    </p>
                    <div class="mt-2 flex items-center gap-2 text-xs">
                        @php $monthlyGrowth = (float) ($monthly['revenue_growth'] ?? 0); @endphp
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $monthlyGrowth >= 0 ? 'bg-emerald-50 text-[#008009]' : 'bg-red-50 text-[#E30000]' }}">
                            <i data-lucide="{{ $monthlyGrowth >= 0 ? 'trending-up' : 'trending-down' }}" class="w-3 h-3"></i>
                            {{ $monthlyGrowth >= 0 ? '+' : '' }}{{ $monthlyGrowth }}%
                        </span>
                        <span class="text-[#6E6E73] text-[11px]">{{ $monthly['orders_count'] ?? 0 }} pesanan ({{ $monthly['units_sold'] ?? 0 }} unit)</span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-[#E5E5EA] flex items-center justify-between text-xs">
                    <span class="text-[#6E6E73]">Laba Bersih Bulan Ini:</span>
                    <span class="font-bold text-[#008009]">Rp {{ number_format($monthly['net_profit'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Card 4: Kalkulasi Laba Bersih Otomatis (Omzet - HPP) -->
            <div class="bg-emerald-50/70 rounded-2xl p-5 border border-emerald-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-[#008009] uppercase tracking-wider">Total Laba Bersih Otomatis</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white text-[#008009] border border-emerald-200">
                            Margin {{ $allTime['profit_margin'] ?? 0 }}%
                        </span>
                    </div>
                    <p class="text-2xl font-extrabold text-[#008009] mt-3 tracking-tight">
                        Rp {{ number_format($allTime['net_profit'] ?? 0, 0, ',', '.') }}
                    </p>
                    <p class="text-[11px] text-[#6E6E73] mt-1.5">
                        Rumus: <span class="text-[#1D1D1F] font-medium">Harga Jual ({{ number_format($allTime['gross_items_sales'] ?? 0, 0, ',', '.') }}) &minus; HPP ({{ number_format($allTime['cogs'] ?? 0, 0, ',', '.') }}) &minus; Diskon</span>
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-emerald-200 flex items-center justify-between text-xs">
                    <span class="text-[#6E6E73]">Total HPP Unit Terjual:</span>
                    <span class="font-bold text-[#BF4800]">Rp {{ number_format($allTime['cogs'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. (Phase 11.1) Ringkasan Arus Kas: Trade-In Masuk vs Penjualan Keluar -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-[#E5E5EA] shadow-sm space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-[#E5E5EA] pb-4">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#0066CC] block mb-1">Komparasi Arus Kas &amp; Perputaran Unit</span>
                <h2 class="text-lg font-bold text-[#1D1D1F] flex items-center gap-2">
                    <i data-lucide="arrow-left-right" class="w-5 h-5 text-[#008009]"></i>
                    <span>Ringkasan Arus Kas: Trade-In Masuk vs Penjualan Keluar</span>
                </h2>
                <p class="text-xs text-[#6E6E73] mt-0.5">
                    Perbandingan nilai kas masuk dari unit iPhone terjual keluar terhadap nilai akuisisi unit Trade-In / Buyback yang masuk ke toko.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-4 py-2 rounded-2xl bg-[#F5F5F7] border border-[#E5E5EA] text-right">
                    <span class="text-[10px] uppercase tracking-wider text-[#6E6E73] block">Surplus Arus Kas Bersih (Net Cash Flow)</span>
                    @php $netCash = (float) ($cashFlow['net_cash_flow'] ?? 0); @endphp
                    <span class="text-base font-extrabold {{ $netCash >= 0 ? 'text-[#008009]' : 'text-[#E30000]' }}">
                        {{ $netCash >= 0 ? '+' : '-' }} Rp {{ number_format(abs($netCash), 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <!-- Box A: Penjualan Keluar (Arus Kas Masuk) -->
            <div class="p-5 rounded-2xl bg-[#F5F5F7] border border-[#E5E5EA] space-y-3">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-[#008009] uppercase tracking-wider">
                        <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                        Penjualan Keluar (Kas Masuk)
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-[#008009] border border-emerald-200">
                        {{ $cashFlow['sales_units_out'] ?? 0 }} Unit Keluar
                    </span>
                </div>
                <p class="text-2xl font-extrabold text-[#1D1D1F]">
                    Rp {{ number_format($cashFlow['sales_cash_in'] ?? 0, 0, ',', '.') }}
                </p>
                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-[#E5E5EA] text-xs">
                    <div>
                        <span class="text-[#6E6E73] block text-[11px]">Pesanan Lunas</span>
                        <span class="font-semibold text-[#1D1D1F]">{{ $cashFlow['sales_orders_count'] ?? 0 }} Transaksi</span>
                    </div>
                    <div>
                        <span class="text-[#6E6E73] block text-[11px]">Laba Bersih Penjualan</span>
                        <span class="font-semibold text-[#008009]">Rp {{ number_format($cashFlow['sales_net_profit'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Box B: Trade-In Masuk (Arus Kas / Voucher Keluar) -->
            <div class="p-5 rounded-2xl bg-[#F5F5F7] border border-[#E5E5EA] space-y-3">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-[#BF4800] uppercase tracking-wider">
                        <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
                        Trade-In Masuk (Akuisisi Unit)
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-[#BF4800] border border-amber-200">
                        {{ $cashFlow['trade_in_units_in'] ?? 0 }} Unit Masuk
                    </span>
                </div>
                <p class="text-2xl font-extrabold text-[#1D1D1F]">
                    Rp {{ number_format($cashFlow['trade_in_total_value'] ?? 0, 0, ',', '.') }}
                </p>
                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-[#E5E5EA] text-xs">
                    <div>
                        <span class="text-[#6E6E73] block text-[11px]">Transfer Tunai (Kas Keluar)</span>
                        <span class="font-semibold text-[#BF4800]">Rp {{ number_format($cashFlow['trade_in_cash_out'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-[#6E6E73] block text-[11px]">Kupon Potongan Belanja</span>
                        <span class="font-semibold text-[#0066CC]">Rp {{ number_format($cashFlow['trade_in_voucher_out'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Box C: Posisi Kas Likuid & Antrean Trade-In -->
            <div class="p-5 rounded-2xl bg-[#F5F5F7] border border-[#E5E5EA] space-y-3 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-[#0066CC] uppercase tracking-wider">
                            <i data-lucide="scale" class="w-4 h-4"></i>
                            Arus Kas Tunai Likuid
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-[#0066CC] border border-blue-200">
                            Inflow {{ $cashFlow['inflow_ratio_pct'] ?? 100 }}%
                        </span>
                    </div>
                    <p class="text-2xl font-extrabold text-[#1D1D1F] mt-2">
                        Rp {{ number_format($cashFlow['net_liquid_cash_flow'] ?? 0, 0, ',', '.') }}
                    </p>
                    <p class="text-[11px] text-[#6E6E73] mt-1">
                        Kas Masuk Penjualan dikurangi Pencairan Transfer Bank Trade-In.
                    </p>
                </div>

                <div class="pt-2 border-t border-[#E5E5EA] flex items-center justify-between text-xs">
                    <span class="text-[#6E6E73]">Antrean Inspeksi Trade-In:</span>
                    <a href="{{ route('admin.trade-ins.index') }}" class="font-semibold text-[#BF4800] hover:underline">
                        {{ $cashFlow['trade_in_pending_count'] ?? 0 }} Unit (Est. Rp {{ number_format($cashFlow['trade_in_pending_estimated'] ?? 0, 0, ',', '.') }})
                    </a>
                </div>
            </div>
        </div>

        <!-- Visual Cash Flow Proportion Bar -->
        <div class="space-y-2">
            <div class="flex items-center justify-between text-xs">
                <span class="text-[#008009] font-semibold flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#008009]"></span>
                    Penjualan Keluar ({{ $cashFlow['inflow_ratio_pct'] ?? 100 }}%)
                </span>
                <span class="text-[#BF4800] font-semibold flex items-center gap-1.5">
                    Akuisisi Trade-In Masuk ({{ $cashFlow['outflow_ratio_pct'] ?? 0 }}%)
                    <span class="w-2.5 h-2.5 rounded-full bg-[#BF4800]"></span>
                </span>
            </div>
            <div class="w-full h-3 rounded-full bg-[#F5F5F7] border border-[#E5E5EA] overflow-hidden flex">
                <div class="h-full bg-gradient-to-r from-[#0071E3] to-[#008009] transition-all duration-500" style="width: {{ $cashFlow['inflow_ratio_pct'] ?? 100 }}%"></div>
                <div class="h-full bg-gradient-to-r from-[#BF4800] to-[#E30000] transition-all duration-500" style="width: {{ $cashFlow['outflow_ratio_pct'] ?? 0 }}%"></div>
            </div>
        </div>
    </div>

    <!-- 4. (Phase 11.2) Grafik Performa Bisnis: Tren Transaksi Penjualan 30 Hari Terakhir -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-[#E5E5EA] shadow-sm space-y-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-[#E5E5EA] pb-4">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#0066CC] block mb-1">Visualisasi Tren Performa Bisnis</span>
                <h2 class="text-lg font-bold text-[#1D1D1F] flex items-center gap-2">
                    <i data-lucide="activity" class="w-5 h-5 text-[#0071E3]"></i>
                    <span>Grafik Tren Transaksi Penjualan &amp; Laba Bersih (30 Hari Terakhir)</span>
                </h2>
                <p class="text-xs text-[#6E6E73] mt-0.5">
                    Pergerakan harian omzet penjualan kotor dan laba bersih produk selama 30 hari kalender terakhir.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-4 text-xs">
                <div class="px-3.5 py-2 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA]">
                    <span class="text-[#6E6E73] block text-[10px] uppercase">Omzet 30 Hari</span>
                    <span class="font-bold text-[#1D1D1F]">Rp {{ number_format($trendSummary['total_revenue_30d'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="px-3.5 py-2 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA]">
                    <span class="text-[#6E6E73] block text-[10px] uppercase">Laba Bersih 30 Hari</span>
                    <span class="font-bold text-[#008009]">Rp {{ number_format($trendSummary['total_profit_30d'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="px-3.5 py-2 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA]">
                    <span class="text-[#6E6E73] block text-[10px] uppercase">Volume 30 Hari</span>
                    <span class="font-bold text-[#0066CC]">{{ $trendSummary['total_orders_30d'] ?? 0 }} Pesanan ({{ $trendSummary['total_units_30d'] ?? 0 }} Unit)</span>
                </div>
            </div>
        </div>

        <!-- SVG Area Chart & Interactive Bar Hover (30 Days) -->
        @php
            $maxRev = max((float) ($trendSummary['max_revenue'] ?? 1), 1.0);
            $chartWidth = 900;
            $chartHeight = 240;
            $countDays = count($trendDays);
            $stepX = $countDays > 1 ? ($chartWidth / ($countDays - 1)) : $chartWidth;

            $revPoints = [];
            $profitPoints = [];

            foreach ($trendDays as $idx => $d) {
                $x = round($idx * $stepX, 2);
                $yRev = round($chartHeight - (($d['revenue'] / $maxRev) * ($chartHeight - 28)) - 10, 2);
                $yProf = round($chartHeight - ((max(0, $d['net_profit']) / $maxRev) * ($chartHeight - 28)) - 10, 2);
                $revPoints[] = "{$x},{$yRev}";
                $profitPoints[] = "{$x},{$yProf}";
            }

            $revPolyline = implode(' ', $revPoints);
            $revArea = "0,{$chartHeight} " . $revPolyline . " {$chartWidth},{$chartHeight}";

            $profitPolyline = implode(' ', $profitPoints);
            $profitArea = "0,{$chartHeight} " . $profitPolyline . " {$chartWidth},{$chartHeight}";
        @endphp

        <div class="relative">
            <!-- Legend & Interactive Tooltip Readout -->
            <div class="flex flex-wrap items-center justify-between gap-4 mb-4 text-xs">
                <div class="flex items-center gap-5">
                    <span class="inline-flex items-center gap-2 text-[#1D1D1F] font-medium">
                        <span class="w-3 h-3 rounded-full bg-[#0071E3]"></span>
                        Omzet Penjualan Harian
                    </span>
                    <span class="inline-flex items-center gap-2 text-[#1D1D1F] font-medium">
                        <span class="w-3 h-3 rounded-full bg-[#008009]"></span>
                        Estimasi Laba Bersih
                    </span>
                </div>
                <div id="chart-hover-readout" class="px-3.5 py-1.5 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA] text-xs text-[#6E6E73]">
                    Arahkan kursor pada titik grafik untuk melihat rincian tanggal, omzet, dan laba bersih.
                </div>
            </div>

            <!-- SVG Responsive Container -->
            <div class="w-full overflow-x-auto">
                <div class="min-w-[680px]">
                    <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" class="w-full h-60 overflow-visible">
                        <defs>
                            <linearGradient id="ownerRevGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#0071E3" stop-opacity="0.25"/>
                                <stop offset="100%" stop-color="#0071E3" stop-opacity="0.0"/>
                            </linearGradient>
                            <linearGradient id="ownerProfitGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#008009" stop-opacity="0.22"/>
                                <stop offset="100%" stop-color="#008009" stop-opacity="0.0"/>
                            </linearGradient>
                        </defs>

                        <!-- Horizontal Grid Lines -->
                        @for($g = 0; $g <= 4; $g++)
                            @php $gy = round(10 + (($chartHeight - 20) / 4) * $g, 2); @endphp
                            <line x1="0" y1="{{ $gy }}" x2="{{ $chartWidth }}" y2="{{ $gy }}" stroke="#E5E5EA" stroke-dasharray="4 4" stroke-width="1"/>
                        @endfor

                        <!-- Revenue Area & Line -->
                        @if(count($revPoints) > 1)
                            <polygon points="{{ $revArea }}" fill="url(#ownerRevGrad)" />
                            <polyline fill="none" stroke="#0071E3" stroke-width="2.75" stroke-linecap="round" stroke-linejoin="round" points="{{ $revPolyline }}" />

                            <!-- Net Profit Area & Line -->
                            <polygon points="{{ $profitArea }}" fill="url(#ownerProfitGrad)" />
                            <polyline fill="none" stroke="#008009" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" points="{{ $profitPolyline }}" />
                        @endif

                        <!-- Interactive Data Points -->
                        @foreach($trendDays as $idx => $d)
                            @php
                                $cx = round($idx * $stepX, 2);
                                $cyRev = round($chartHeight - (($d['revenue'] / $maxRev) * ($chartHeight - 28)) - 10, 2);
                                $cyProf = round($chartHeight - ((max(0, $d['net_profit']) / $maxRev) * ($chartHeight - 28)) - 10, 2);
                            @endphp
                            <g class="group cursor-pointer trend-point"
                               data-date="{{ $d['full_label'] }}"
                               data-revenue="{{ number_format($d['revenue'], 0, ',', '.') }}"
                               data-profit="{{ number_format($d['net_profit'], 0, ',', '.') }}"
                               data-orders="{{ $d['orders_count'] }}"
                               data-units="{{ $d['units_sold'] }}">
                                <line x1="{{ $cx }}" y1="10" x2="{{ $cx }}" y2="{{ $chartHeight - 10 }}" stroke="rgba(0,0,0,0)" class="group-hover:stroke-[#D2D2D7] transition-all" stroke-width="1.5"/>
                                <circle cx="{{ $cx }}" cy="{{ $cyRev }}" r="{{ $d['revenue'] > 0 ? '4.5' : '2.5' }}" fill="{{ $d['revenue'] > 0 ? '#0071E3' : '#D2D2D7' }}" stroke="#FFFFFF" stroke-width="1.5" />
                                @if($d['net_profit'] > 0)
                                    <circle cx="{{ $cx }}" cy="{{ $cyProf }}" r="3.5" fill="#008009" stroke="#FFFFFF" stroke-width="1.2" />
                                @endif
                            </g>
                        @endforeach
                    </svg>

                    <!-- X-Axis Date Labels (Every 3 days) -->
                    <div class="flex items-center justify-between text-[10px] text-[#6E6E73] pt-2 border-t border-[#E5E5EA]">
                        @foreach($trendDays as $idx => $d)
                            @if($idx % 3 === 0 || $idx === count($trendDays) - 1)
                                <span>{{ $d['label'] }}</span>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. (Phase 11.2) Analisis Produk Terlaris (Top Selling iPhone Models) & Konversi Pesanan -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Columns: Top Selling iPhone Models -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-[#E5E5EA] shadow-sm space-y-5">
            <div class="flex items-center justify-between border-b border-[#E5E5EA] pb-4">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#BF4800] block mb-0.5">Performa Katalog Produk</span>
                    <h2 class="text-base font-bold text-[#1D1D1F] flex items-center gap-2">
                        <i data-lucide="award" class="w-4 h-4 text-[#BF4800]"></i>
                        <span>Analisis Produk Terlaris (Top Selling iPhone Models)</span>
                    </h2>
                </div>
                <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-[#0066CC] hover:underline flex items-center gap-1">
                    <span>Kelola Katalog</span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            @if($topProducts->isEmpty())
                <div class="p-8 text-center rounded-2xl bg-[#F5F5F7] border border-[#E5E5EA]">
                    <i data-lucide="smartphone" class="w-8 h-8 text-[#6E6E73] mx-auto mb-2"></i>
                    <p class="text-sm font-semibold text-[#1D1D1F]">Belum Ada Data Penjualan Produk Lunas</p>
                    <p class="text-xs text-[#6E6E73] mt-1">Peringkat model iPhone terlaris akan tampil otomatis setelah transaksi lunas tercatat.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-[#E5E5EA] text-[#6E6E73] uppercase tracking-wider font-semibold">
                                <th class="py-3 px-3">Rank</th>
                                <th class="py-3 px-3">Model iPhone &amp; Varian Terpopuler</th>
                                <th class="py-3 px-3 text-center">Unit Terjual</th>
                                <th class="py-3 px-3 text-right">Total Omzet</th>
                                <th class="py-3 px-3 text-right">Laba Produk</th>
                                <th class="py-3 px-3 text-right">Pangsa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E5E5EA] text-[#1D1D1F]">
                            @foreach($topProducts as $item)
                                <tr class="hover:bg-[#F5F5F7] transition-colors">
                                    <td class="py-3.5 px-3">
                                        <span class="w-7 h-7 rounded-lg inline-flex items-center justify-center font-extrabold text-xs {{ $item['rank'] === 1 ? 'bg-amber-50 text-[#BF4800] border border-amber-200' : ($item['rank'] <= 3 ? 'bg-blue-50 text-[#0066CC] border border-blue-200' : 'bg-[#F5F5F7] text-[#6E6E73]') }}">
                                            #{{ $item['rank'] }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3">
                                        <p class="font-bold text-[#1D1D1F] text-sm">{{ $item['product_name'] }}</p>
                                        <p class="text-[11px] text-[#6E6E73]">
                                            {{ $item['series'] }} &bull; Favorit: <span class="text-[#0066CC] font-medium">{{ $item['popular_variant'] }}</span>
                                        </p>
                                    </td>
                                    <td class="py-3.5 px-3 text-center">
                                        <span class="px-2.5 py-1 rounded-full bg-[#F5F5F7] border border-[#E5E5EA] text-[#1D1D1F] font-bold">
                                            {{ $item['units_sold'] }} Unit
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3 text-right font-semibold text-[#1D1D1F]">
                                        Rp {{ number_format($item['revenue'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-3 text-right">
                                        <span class="font-bold text-[#008009] block">Rp {{ number_format($item['gross_profit'], 0, ',', '.') }}</span>
                                        <span class="text-[10px] text-[#6E6E73]">Margin {{ $item['profit_margin'] }}%</span>
                                    </td>
                                    <td class="py-3.5 px-3 text-right min-w-[100px]">
                                        <span class="text-xs font-bold text-[#1D1D1F]">{{ $item['share_pct'] }}%</span>
                                        <div class="w-full h-1.5 rounded-full bg-[#F5F5F7] border border-[#E5E5EA] mt-1 overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-[#0071E3] to-[#008009]" style="width: {{ min(100, $item['share_pct']) }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Right 1 Column: Konversi Status Transaksi & Manajemen Organisasi -->
        <div class="space-y-6">
            <!-- Order Conversion Rate Card -->
            <div class="bg-white rounded-3xl p-6 border border-[#E5E5EA] shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-[#E5E5EA] pb-3">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-[#008009] block">Rasio Konversi Checkout</span>
                        <h3 class="text-sm font-bold text-[#1D1D1F]">Status Penyelesaian Transaksi</h3>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-[#008009] border border-emerald-200">
                        {{ $conversion['conversion_rate'] ?? 0 }}% Lunas
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="p-3 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA]">
                        <span class="text-[#6E6E73] block text-[11px]">Pesanan Lunas / Selesai</span>
                        <span class="text-lg font-extrabold text-[#008009]">{{ $conversion['realized_orders'] ?? 0 }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA]">
                        <span class="text-[#6E6E73] block text-[11px]">Menunggu Pembayaran</span>
                        <span class="text-lg font-extrabold text-[#BF4800]">{{ $conversion['pending'] ?? 0 }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA]">
                        <span class="text-[#6E6E73] block text-[11px]">Dalam Pengiriman</span>
                        <span class="text-lg font-extrabold text-[#0066CC]">{{ $conversion['shipped'] ?? 0 }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA]">
                        <span class="text-[#6E6E73] block text-[11px]">Dibatalkan / Expired</span>
                        <span class="text-lg font-extrabold text-[#E30000]">{{ $conversion['cancelled'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Staff & Customer Summary Card -->
            <div class="bg-white rounded-3xl p-6 border border-[#E5E5EA] shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-[#E5E5EA] pb-3">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-[#0066CC] block">Organisasi Toko</span>
                        <h3 class="text-sm font-bold text-[#1D1D1F]">Manajemen Akun &amp; Staf</h3>
                    </div>
                    <a href="{{ route('owner.staff.index') }}" class="text-xs font-semibold text-[#0066CC] hover:underline">
                        Kelola &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="p-3.5 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA]">
                        <span class="text-[#6E6E73] block text-[11px]">Staf Admin Operasional</span>
                        <p class="text-xl font-bold text-[#1D1D1F] mt-1">
                            {{ $totalAdmins ?? 1 }} <span class="text-xs font-normal text-[#6E6E73]">Akun Aktif</span>
                        </p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA]">
                        <span class="text-[#6E6E73] block text-[11px]">Pelanggan Terdaftar</span>
                        <p class="text-xl font-bold text-[#1D1D1F] mt-1">
                            {{ $totalCustomers ?? 1 }} <span class="text-xs font-normal text-[#6E6E73]">Pelanggan</span>
                        </p>
                    </div>
                </div>

                <a href="{{ route('owner.staff.index') }}" class="glass-btn glass-btn-secondary glass-btn-sm w-full flex items-center justify-center gap-2">
                    <i data-lucide="user-plus" class="w-3.5 h-3.5 text-[#0066CC]"></i>
                    <span>Tambah / Kelola Akun Staf Admin</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 6. Rincian Transaksi Lunas Terbaru beserta Kalkulasi HPP & Laba Bersih -->
    <div class="bg-white rounded-3xl p-6 border border-[#E5E5EA] shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-[#E5E5EA] pb-4">
            <div>
                <h2 class="text-base font-bold text-[#1D1D1F] flex items-center gap-2">
                    <i data-lucide="receipt" class="w-4 h-4 text-[#008009]"></i>
                    <span>Transaksi Penjualan Lunas Terbaru &amp; Audit Margin Laba</span>
                </h2>
                <p class="text-xs text-[#6E6E73]">Rincian HPP dan laba bersih per nota pesanan yang telah terverifikasi lunas.</p>
            </div>
            <a href="{{ route('owner.financial.report') }}" class="glass-btn glass-btn-secondary glass-btn-sm flex items-center gap-1.5">
                <span>Buka Laporan Penuh &amp; Ekspor</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        @if($recentRealizedOrders->isEmpty())
            <div class="p-6 text-center text-xs text-[#6E6E73]">
                Belum ada transaksi lunas yang tercatat.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-[#E5E5EA] text-[#6E6E73] uppercase tracking-wider font-semibold">
                            <th class="py-3 px-3">No. Nota</th>
                            <th class="py-3 px-3">Tanggal</th>
                            <th class="py-3 px-3">Pelanggan</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3 text-right">Omzet (Grand Total)</th>
                            <th class="py-3 px-3 text-right">Total HPP</th>
                            <th class="py-3 px-3 text-right">Laba Bersih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E5EA] text-[#1D1D1F]">
                        @foreach($recentRealizedOrders as $order)
                            <tr class="hover:bg-[#F5F5F7] transition-colors">
                                <td class="py-3 px-3 font-mono font-bold text-[#1D1D1F]">
                                    <a href="{{ route('admin.orders.show', $order->order_number) }}" class="hover:text-[#0066CC] underline decoration-[#D2D2D7]">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="py-3 px-3 text-[#6E6E73]">{{ $order->created_at?->format('d M Y, H:i') }}</td>
                                <td class="py-3 px-3">{{ $order->recipient_name ?: ($order->user?->name ?? 'Pelanggan') }}</td>
                                <td class="py-3 px-3"><x-order-badge :status="$order->status" /></td>
                                <td class="py-3 px-3 text-right font-semibold text-[#1D1D1F]">
                                    Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-right text-[#BF4800]">
                                    Rp {{ number_format((float) $order->calculated_cogs, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-right font-bold text-[#008009]">
                                    + Rp {{ number_format((float) $order->calculated_net_profit, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const readout = document.getElementById('chart-hover-readout');
        const points = document.querySelectorAll('.trend-point');

        points.forEach(function (pt) {
            pt.addEventListener('mouseenter', function () {
                const date = pt.getAttribute('data-date');
                const rev = pt.getAttribute('data-revenue');
                const profit = pt.getAttribute('data-profit');
                const orders = pt.getAttribute('data-orders');
                const units = pt.getAttribute('data-units');

                if (readout) {
                    readout.innerHTML = `<strong class="text-[#1D1D1F]">${date}</strong> &mdash; Omzet: <strong class="text-[#0066CC]">Rp ${rev}</strong> | Laba Bersih: <strong class="text-[#008009]">Rp ${profit}</strong> (${orders} pesanan / ${units} unit)`;
                }
            });
        });
    });
</script>
@endpush
