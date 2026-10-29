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

<!-- 1. Owner Executive Page Header -->
<x-admin.page-header
    eyebrow="Tingkat Akses Tertinggi: Pemilik Usaha (Owner)"
    icon="crown"
    title="Portal Finansial &amp; Analitik Laba"
    subtitle="Pantau total omzet penjualan (Harian, Mingguan, Bulanan), kalkulasi otomatis laba bersih terhadap HPP unit iPhone, arus kas Trade-In masuk vs penjualan keluar, serta tren bisnis 30 hari terakhir.">
    <x-slot:actions>
        <a href="{{ route('owner.financial.report') }}" class="glass-btn glass-btn-primary glass-btn-sm flex items-center gap-2">
            <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5"></i>
            <span>Laporan &amp; Laba Rugi</span>
        </a>
        <a href="{{ route('owner.financial.export.csv') }}" class="glass-btn glass-btn-secondary glass-btn-sm flex items-center gap-2">
            <i data-lucide="download" class="w-3.5 h-3.5 text-[#008040]"></i>
            <span>Ekspor CSV / Excel</span>
        </a>
        <a href="{{ route('owner.financial.export.pdf') }}" target="_blank" rel="noopener" class="glass-btn glass-btn-secondary glass-btn-sm flex items-center gap-2">
            <i data-lucide="file-text" class="w-3.5 h-3.5 text-[#B25000]"></i>
            <span>Ekspor PDF</span>
        </a>
        <a href="{{ route('owner.staff.index') }}" class="glass-btn glass-btn-secondary glass-btn-sm flex items-center gap-2">
            <i data-lucide="users" class="w-3.5 h-3.5 text-[#0066CC]"></i>
            <span>Kelola Staf ({{ ($totalAdmins ?? 0) + ($totalOwners ?? 1) }})</span>
        </a>
    </x-slot:actions>
</x-admin.page-header>

<!-- 2. Metrik Keuangan Eksekutif -->
<x-admin.card title="Metrik Keuangan Eksekutif &amp; Kalkulasi Laba Bersih Otomatis" icon="wallet"
    description="Dihitung otomatis dari transaksi berstatus Lunas (paid, processing, shipped, completed).">
    <x-slot:actions>
        <span class="adm-badge adm-tone-gray">
            Total Kumulatif Lunas: <strong class="text-[#1D1D1F] ml-1">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</strong>
        </span>
    </x-slot:actions>

    <div class="adm-stat-grid cols-4">
        <!-- Card 1: Omzet Penjualan Harian -->
        <div class="adm-stat">
            <div class="adm-stat-top">
                <div class="min-w-0">
                    <p class="adm-stat-label">Omzet Hari Ini (Harian)</p>
                    <p class="adm-stat-value">Rp {{ number_format($daily['revenue'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <span class="adm-stat-icon adm-tone-blue">
                    <i data-lucide="calendar-clock"></i>
                </span>
            </div>
            <div class="space-y-1 pt-2 border-t border-[#E5E5EA] text-xs">
                @php $dailyGrowth = (float) ($daily['revenue_growth'] ?? 0); @endphp
                <div class="flex items-center gap-2">
                    <span class="adm-badge {{ $dailyGrowth >= 0 ? 'adm-tone-green' : 'adm-tone-red' }}" style="padding:.0625rem .45rem">
                        {{ $dailyGrowth >= 0 ? '+' : '' }}{{ $dailyGrowth }}%
                    </span>
                    <span class="adm-muted text-[11px]">vs kemarin</span>
                </div>
                <div class="flex items-center justify-between pt-1">
                    <span class="adm-muted">Laba Bersih Hari Ini:</span>
                    <strong class="text-[#008040]">Rp {{ number_format($daily['net_profit'] ?? 0, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        <!-- Card 2: Omzet Penjualan Mingguan -->
        <div class="adm-stat">
            <div class="adm-stat-top">
                <div class="min-w-0">
                    <p class="adm-stat-label">Omzet 7 Hari (Mingguan)</p>
                    <p class="adm-stat-value">Rp {{ number_format($weekly['revenue'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <span class="adm-stat-icon adm-tone-blue">
                    <i data-lucide="calendar-range"></i>
                </span>
            </div>
            <div class="space-y-1 pt-2 border-t border-[#E5E5EA] text-xs">
                @php $weeklyGrowth = (float) ($weekly['revenue_growth'] ?? 0); @endphp
                <div class="flex items-center gap-2">
                    <span class="adm-badge {{ $weeklyGrowth >= 0 ? 'adm-tone-green' : 'adm-tone-red' }}" style="padding:.0625rem .45rem">
                        {{ $weeklyGrowth >= 0 ? '+' : '' }}{{ $weeklyGrowth }}%
                    </span>
                    <span class="adm-muted text-[11px]">{{ $weekly['orders_count'] ?? 0 }} pesanan ({{ $weekly['units_sold'] ?? 0 }} unit)</span>
                </div>
                <div class="flex items-center justify-between pt-1">
                    <span class="adm-muted">Laba Bersih 7 Hari:</span>
                    <strong class="text-[#008040]">Rp {{ number_format($weekly['net_profit'] ?? 0, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        <!-- Card 3: Omzet Penjualan Bulanan -->
        <div class="adm-stat">
            <div class="adm-stat-top">
                <div class="min-w-0">
                    <p class="adm-stat-label">Omzet Bulan Ini (Bulanan)</p>
                    <p class="adm-stat-value">Rp {{ number_format($monthly['revenue'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <span class="adm-stat-icon adm-tone-purple">
                    <i data-lucide="bar-chart-3"></i>
                </span>
            </div>
            <div class="space-y-1 pt-2 border-t border-[#E5E5EA] text-xs">
                @php $monthlyGrowth = (float) ($monthly['revenue_growth'] ?? 0); @endphp
                <div class="flex items-center gap-2">
                    <span class="adm-badge {{ $monthlyGrowth >= 0 ? 'adm-tone-green' : 'adm-tone-red' }}" style="padding:.0625rem .45rem">
                        {{ $monthlyGrowth >= 0 ? '+' : '' }}{{ $monthlyGrowth }}%
                    </span>
                    <span class="adm-muted text-[11px]">{{ $monthly['orders_count'] ?? 0 }} pesanan ({{ $monthly['units_sold'] ?? 0 }} unit)</span>
                </div>
                <div class="flex items-center justify-between pt-1">
                    <span class="adm-muted">Laba Bersih Bulan Ini:</span>
                    <strong class="text-[#008040]">Rp {{ number_format($monthly['net_profit'] ?? 0, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        <!-- Card 4: Kalkulasi Laba Bersih Otomatis -->
        <div class="adm-stat" style="background:#F0FDF4;border-color:#BBF7D0">
            <div class="adm-stat-top">
                <div class="min-w-0">
                    <p class="adm-stat-label" style="color:#008040">Total Laba Bersih Otomatis</p>
                    <p class="adm-stat-value" style="color:#008040">Rp {{ number_format($allTime['net_profit'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <span class="adm-badge adm-tone-green" style="background:#fff">
                    Margin {{ $allTime['profit_margin'] ?? 0 }}%
                </span>
            </div>
            <div class="space-y-1 pt-2 border-t border-[#BBF7D0] text-xs">
                <p class="adm-muted text-[11px]">
                    Omzet {{ number_format($allTime['gross_items_sales'] ?? 0, 0, ',', '.') }} &minus; HPP &minus; Diskon
                </p>
                <div class="flex items-center justify-between pt-1">
                    <span class="adm-muted">Total HPP Unit:</span>
                    <strong class="text-[#B25000]">Rp {{ number_format($allTime['cogs'] ?? 0, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>
    </div>
</x-admin.card>

<!-- 3. Ringkasan Arus Kas: Trade-In Masuk vs Penjualan Keluar -->
<x-admin.card title="Ringkasan Arus Kas: Trade-In Masuk vs Penjualan Keluar" icon="arrow-left-right"
    description="Perbandingan nilai kas masuk dari unit iPhone terjual keluar terhadap nilai akuisisi unit Trade-In / Buyback yang masuk ke toko.">
    <x-slot:actions>
        @php $netCash = (float) ($cashFlow['net_cash_flow'] ?? 0); @endphp
        <div class="px-3.5 py-1.5 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA] text-right">
            <span class="text-[10px] uppercase tracking-wider text-[#6E6E73] block font-semibold">Surplus Arus Kas Bersih</span>
            <span class="text-sm font-extrabold {{ $netCash >= 0 ? 'text-[#008040]' : 'text-[#D70015]' }}">
                {{ $netCash >= 0 ? '+' : '-' }} Rp {{ number_format(abs($netCash), 0, ',', '.') }}
            </span>
        </div>
    </x-slot:actions>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
        <!-- Box A: Penjualan Keluar (Kas Masuk) -->
        <div class="p-5 rounded-2xl bg-[#FAFAFC] border border-[#E5E5EA] space-y-3">
            <div class="flex items-center justify-between">
                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-[#008040] uppercase tracking-wider">
                    <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                    Penjualan Keluar (Kas Masuk)
                </span>
                <x-admin.status-badge tone="green" :label="($cashFlow['sales_units_out'] ?? 0) . ' Unit Keluar'" :dot="false" />
            </div>
            <p class="text-2xl font-extrabold text-[#1D1D1F]">
                Rp {{ number_format($cashFlow['sales_cash_in'] ?? 0, 0, ',', '.') }}
            </p>
            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-[#E5E5EA] text-xs">
                <div>
                    <span class="adm-muted block text-[11px]">Pesanan Lunas</span>
                    <span class="font-semibold text-[#1D1D1F]">{{ $cashFlow['sales_orders_count'] ?? 0 }} Transaksi</span>
                </div>
                <div>
                    <span class="adm-muted block text-[11px]">Laba Bersih Penjualan</span>
                    <span class="font-semibold text-[#008040]">Rp {{ number_format($cashFlow['sales_net_profit'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Box B: Trade-In Masuk (Akuisisi Unit) -->
        <div class="p-5 rounded-2xl bg-[#FAFAFC] border border-[#E5E5EA] space-y-3">
            <div class="flex items-center justify-between">
                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-[#B25000] uppercase tracking-wider">
                    <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
                    Trade-In Masuk (Akuisisi Unit)
                </span>
                <x-admin.status-badge tone="amber" :label="($cashFlow['trade_in_units_in'] ?? 0) . ' Unit Masuk'" :dot="false" />
            </div>
            <p class="text-2xl font-extrabold text-[#1D1D1F]">
                Rp {{ number_format($cashFlow['trade_in_total_value'] ?? 0, 0, ',', '.') }}
            </p>
            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-[#E5E5EA] text-xs">
                <div>
                    <span class="adm-muted block text-[11px]">Transfer Tunai (Kas Keluar)</span>
                    <span class="font-semibold text-[#B25000]">Rp {{ number_format($cashFlow['trade_in_cash_out'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="adm-muted block text-[11px]">Kupon Potongan Belanja</span>
                    <span class="font-semibold text-[#0066CC]">Rp {{ number_format($cashFlow['trade_in_voucher_out'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Box C: Posisi Kas Likuid -->
        <div class="p-5 rounded-2xl bg-[#FAFAFC] border border-[#E5E5EA] space-y-3 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-[#0066CC] uppercase tracking-wider">
                        <i data-lucide="scale" class="w-4 h-4"></i>
                        Arus Kas Tunai Likuid
                    </span>
                    <x-admin.status-badge tone="blue" :label="'Inflow ' . ($cashFlow['inflow_ratio_pct'] ?? 100) . '%'" :dot="false" />
                </div>
                <p class="text-2xl font-extrabold text-[#1D1D1F] mt-2">
                    Rp {{ number_format($cashFlow['net_liquid_cash_flow'] ?? 0, 0, ',', '.') }}
                </p>
                <p class="adm-muted text-[11px] mt-1">
                    Kas Masuk Penjualan dikurangi Pencairan Transfer Bank Trade-In.
                </p>
            </div>

            <div class="pt-2 border-t border-[#E5E5EA] flex items-center justify-between text-xs">
                <span class="adm-muted">Antrean Inspeksi Trade-In:</span>
                <a href="{{ route('admin.trade-ins.index') }}" class="font-semibold text-[#B25000] hover:underline">
                    {{ $cashFlow['trade_in_pending_count'] ?? 0 }} Unit (Est. Rp {{ number_format($cashFlow['trade_in_pending_estimated'] ?? 0, 0, ',', '.') }})
                </a>
            </div>
        </div>
    </div>

    <!-- Visual Cash Flow Proportion Bar -->
    <div class="space-y-2">
        <div class="flex items-center justify-between text-xs font-semibold">
            <span class="text-[#008040] flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-[#008040]"></span>
                Penjualan Keluar ({{ $cashFlow['inflow_ratio_pct'] ?? 100 }}%)
            </span>
            <span class="text-[#B25000] flex items-center gap-1.5">
                Akuisisi Trade-In Masuk ({{ $cashFlow['outflow_ratio_pct'] ?? 0 }}%)
                <span class="w-2.5 h-2.5 rounded-full bg-[#B25000]"></span>
            </span>
        </div>
        <div class="w-full h-3 rounded-full bg-[#F5F5F7] border border-[#E5E5EA] overflow-hidden flex">
            <div class="h-full bg-gradient-to-r from-[#0071E3] to-[#008040] transition-all duration-500" style="width: {{ $cashFlow['inflow_ratio_pct'] ?? 100 }}%"></div>
            <div class="h-full bg-gradient-to-r from-[#B25000] to-[#D70015] transition-all duration-500" style="width: {{ $cashFlow['outflow_ratio_pct'] ?? 0 }}%"></div>
        </div>
    </div>
</x-admin.card>

<!-- 4. Grafik Performa Bisnis: Tren Transaksi Penjualan 30 Hari Terakhir -->
<x-admin.card title="Grafik Tren Transaksi Penjualan &amp; Laba Bersih (30 Hari Terakhir)" icon="activity"
    description="Pergerakan harian omzet penjualan kotor dan laba bersih produk selama 30 hari kalender terakhir.">
    <x-slot:actions>
        <div class="flex flex-wrap items-center gap-3 text-xs">
            <div class="px-3 py-1.5 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA]">
                <span class="adm-muted text-[10px] uppercase block">Omzet 30 Hari</span>
                <strong class="text-[#1D1D1F]">Rp {{ number_format($trendSummary['total_revenue_30d'] ?? 0, 0, ',', '.') }}</strong>
            </div>
            <div class="px-3 py-1.5 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA]">
                <span class="adm-muted text-[10px] uppercase block">Laba 30 Hari</span>
                <strong class="text-[#008040]">Rp {{ number_format($trendSummary['total_profit_30d'] ?? 0, 0, ',', '.') }}</strong>
            </div>
            <div class="px-3 py-1.5 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA]">
                <span class="adm-muted text-[10px] uppercase block">Volume 30 Hari</span>
                <strong class="text-[#0066CC]">{{ $trendSummary['total_orders_30d'] ?? 0 }} Pesanan</strong>
            </div>
        </div>
    </x-slot:actions>

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

    <div class="space-y-4">
        <!-- Legend & Interactive Tooltip Readout -->
        <div class="flex flex-wrap items-center justify-between gap-4 text-xs">
            <div class="flex items-center gap-5">
                <span class="inline-flex items-center gap-2 text-[#1D1D1F] font-semibold">
                    <span class="w-3 h-3 rounded-full bg-[#0071E3]"></span>
                    Omzet Penjualan Harian
                </span>
                <span class="inline-flex items-center gap-2 text-[#1D1D1F] font-semibold">
                    <span class="w-3 h-3 rounded-full bg-[#008040]"></span>
                    Estimasi Laba Bersih
                </span>
            </div>
            <div id="chart-hover-readout" class="px-3.5 py-1.5 rounded-xl bg-[#FAFAFC] border border-[#E5E5EA] text-xs text-[#6E6E73]">
                Arahkan kursor pada titik grafik untuk melihat rincian tanggal, omzet, dan laba bersih.
            </div>
        </div>

        <!-- SVG Container -->
        <div class="w-full overflow-x-auto">
            <div class="min-w-[680px]">
                <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" class="w-full h-60 overflow-visible">
                    <defs>
                        <linearGradient id="ownerRevGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#0071E3" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#0071E3" stop-opacity="0.0"/>
                        </linearGradient>
                        <linearGradient id="ownerProfitGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#008040" stop-opacity="0.22"/>
                            <stop offset="100%" stop-color="#008040" stop-opacity="0.0"/>
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
                        <polyline fill="none" stroke="#008040" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" points="{{ $profitPolyline }}" />
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
                                <circle cx="{{ $cx }}" cy="{{ $cyProf }}" r="3.5" fill="#008040" stroke="#FFFFFF" stroke-width="1.2" />
                            @endif
                        </g>
                    @endforeach
                </svg>

                <!-- X-Axis Date Labels -->
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
</x-admin.card>

<!-- 5. Analisis Produk Terlaris & Konversi Pesanan -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Left 2 Columns: Top Selling iPhone Models -->
    <div class="lg:col-span-2">
        <x-admin.card title="Analisis Produk Terlaris (Top Selling iPhone Models)" icon="award" flush>
            <x-slot:actions>
                <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-[#0066CC] hover:underline flex items-center gap-1">
                    <span>Kelola Katalog</span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            </x-slot:actions>

            @if($topProducts->isEmpty())
                <x-admin.empty-state icon="smartphone" title="Belum Ada Data Penjualan Produk Lunas"
                    text="Peringkat model iPhone terlaris akan tampil otomatis setelah transaksi lunas tercatat." />
            @else
                <div class="adm-table-wrap">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th style="padding-left:1.25rem">Rank</th>
                                <th>Model iPhone &amp; Varian Favorit</th>
                                <th style="text-align:center">Unit</th>
                                <th class="adm-num">Total Omzet</th>
                                <th class="adm-num">Laba Produk</th>
                                <th class="adm-num" style="padding-right:1.25rem">Pangsa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProducts as $item)
                                <tr>
                                    <td style="padding-left:1.25rem">
                                        <span class="w-7 h-7 rounded-lg inline-flex items-center justify-center font-extrabold text-xs {{ $item['rank'] === 1 ? 'bg-amber-50 text-[#B25000] border border-amber-200' : ($item['rank'] <= 3 ? 'bg-blue-50 text-[#0066CC] border border-blue-200' : 'bg-[#F5F5F7] text-[#6E6E73]') }}">
                                            #{{ $item['rank'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <p class="adm-cell-title">{{ $item['product_name'] }}</p>
                                        <p class="adm-cell-sub">
                                            {{ $item['series'] }} &bull; Favorit: <strong class="text-[#0066CC]">{{ $item['popular_variant'] }}</strong>
                                        </p>
                                    </td>
                                    <td style="text-align:center">
                                        <span class="px-2.5 py-1 rounded-full bg-[#F5F5F7] border border-[#E5E5EA] text-[#1D1D1F] font-bold text-xs">
                                            {{ $item['units_sold'] }} Unit
                                        </span>
                                    </td>
                                    <td class="adm-num">
                                        <span class="adm-cell-title">Rp {{ number_format($item['revenue'], 0, ',', '.') }}</span>
                                    </td>
                                    <td class="adm-num">
                                        <span class="adm-cell-title text-[#008040] block">Rp {{ number_format($item['gross_profit'], 0, ',', '.') }}</span>
                                        <span class="adm-cell-sub">Margin {{ $item['profit_margin'] }}%</span>
                                    </td>
                                    <td class="adm-num" style="min-width:110px;padding-right:1.25rem">
                                        <span class="text-xs font-bold text-[#1D1D1F]">{{ $item['share_pct'] }}%</span>
                                        <div class="w-full h-1.5 rounded-full bg-[#F5F5F7] border border-[#E5E5EA] mt-1 overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-[#0071E3] to-[#008040]" style="width: {{ min(100, $item['share_pct']) }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-admin.card>
    </div>

    <!-- Right 1 Column: Konversi Status & Organisasi -->
    <div class="space-y-6">
        <!-- Order Conversion Rate Card -->
        <x-admin.card title="Status Penyelesaian Transaksi" icon="pie-chart">
            <x-slot:actions>
                <x-admin.status-badge tone="green" :label="($conversion['conversion_rate'] ?? 0) . '% Lunas'" :dot="false" />
            </x-slot:actions>

            <div class="grid grid-cols-2 gap-3 text-xs">
                <div class="p-3 rounded-xl bg-[#FAFAFC] border border-[#E5E5EA]">
                    <span class="adm-muted block text-[11px]">Lunas / Selesai</span>
                    <span class="text-lg font-extrabold text-[#008040]">{{ $conversion['realized_orders'] ?? 0 }}</span>
                </div>
                <div class="p-3 rounded-xl bg-[#FAFAFC] border border-[#E5E5EA]">
                    <span class="adm-muted block text-[11px]">Menunggu Bayar</span>
                    <span class="text-lg font-extrabold text-[#B25000]">{{ $conversion['pending'] ?? 0 }}</span>
                </div>
                <div class="p-3 rounded-xl bg-[#FAFAFC] border border-[#E5E5EA]">
                    <span class="adm-muted block text-[11px]">Dalam Pengiriman</span>
                    <span class="text-lg font-extrabold text-[#0066CC]">{{ $conversion['shipped'] ?? 0 }}</span>
                </div>
                <div class="p-3 rounded-xl bg-[#FAFAFC] border border-[#E5E5EA]">
                    <span class="adm-muted block text-[11px]">Dibatalkan</span>
                    <span class="text-lg font-extrabold text-[#D70015]">{{ $conversion['cancelled'] ?? 0 }}</span>
                </div>
            </div>
        </x-admin.card>

        <!-- Staff Summary Card -->
        <x-admin.card title="Manajemen Akun &amp; Staf" icon="users">
            <x-slot:actions>
                <a href="{{ route('owner.staff.index') }}" class="text-xs font-semibold text-[#0066CC] hover:underline">
                    Kelola &rarr;
                </a>
            </x-slot:actions>

            <div class="grid grid-cols-2 gap-3 text-xs mb-3">
                <div class="p-3.5 rounded-xl bg-[#FAFAFC] border border-[#E5E5EA]">
                    <span class="adm-muted block text-[11px]">Staf Admin</span>
                    <p class="text-xl font-bold text-[#1D1D1F] mt-1">
                        {{ $totalAdmins ?? 1 }} <span class="text-xs font-normal adm-muted">Akun</span>
                    </p>
                </div>
                <div class="p-3.5 rounded-xl bg-[#FAFAFC] border border-[#E5E5EA]">
                    <span class="adm-muted block text-[11px]">Pelanggan</span>
                    <p class="text-xl font-bold text-[#1D1D1F] mt-1">
                        {{ $totalCustomers ?? 1 }} <span class="text-xs font-normal adm-muted">Akun</span>
                    </p>
                </div>
            </div>

            <a href="{{ route('owner.staff.index') }}" class="glass-btn glass-btn-secondary glass-btn-sm w-full flex items-center justify-center gap-2">
                <i data-lucide="user-plus" class="w-3.5 h-3.5 text-[#0066CC]"></i>
                <span>Kelola Akun Staf Admin</span>
            </a>
        </x-admin.card>
    </div>
</div>

<!-- 6. Transaksi Penjualan Lunas Terbaru & Audit Margin Laba -->
<x-admin.card title="Transaksi Penjualan Lunas Terbaru &amp; Audit Margin Laba" icon="receipt"
    description="Rincian HPP dan laba bersih per nota pesanan yang telah terverifikasi lunas." flush>
    <x-slot:actions>
        <a href="{{ route('owner.financial.report') }}" class="glass-btn glass-btn-secondary glass-btn-sm flex items-center gap-1.5">
            <span>Buka Laporan &amp; Ekspor</span>
            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
        </a>
    </x-slot:actions>

    @if($recentRealizedOrders->isEmpty())
        <x-admin.empty-state icon="receipt" title="Belum Ada Transaksi Lunas" text="Transaksi lunas akan tampil di sini." />
    @else
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th style="padding-left:1.25rem">No. Nota</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th style="text-align:center">Status</th>
                        <th class="adm-num">Omzet (Grand Total)</th>
                        <th class="adm-num">Total HPP</th>
                        <th class="adm-num" style="padding-right:1.25rem">Laba Bersih</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentRealizedOrders as $order)
                        <tr>
                            <td style="padding-left:1.25rem">
                                <a href="{{ route('admin.orders.show', $order->order_number) }}" class="adm-mono font-bold text-[#0066CC] hover:underline">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="adm-cell-sub">{{ $order->created_at?->format('d M Y, H:i') }}</td>
                            <td class="adm-cell-title">{{ $order->recipient_name ?: ($order->user?->name ?? 'Pelanggan') }}</td>
                            <td style="text-align:center"><x-order-badge :status="$order->status" /></td>
                            <td class="adm-num font-bold text-[#1D1D1F]">
                                Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}
                            </td>
                            <td class="adm-num text-[#B25000]">
                                Rp {{ number_format((float) $order->calculated_cogs, 0, ',', '.') }}
                            </td>
                            <td class="adm-num font-extrabold text-[#008040]" style="padding-right:1.25rem">
                                + Rp {{ number_format((float) $order->calculated_net_profit, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-admin.card>
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
                    readout.innerHTML = `<strong class="text-[#1D1D1F]">${date}</strong> &mdash; Omzet: <strong class="text-[#0066CC]">Rp ${rev}</strong> | Laba Bersih: <strong class="text-[#008040]">Rp ${profit}</strong> (${orders} pesanan / ${units} unit)`;
                }
            });
        });
    });
</script>
@endpush
