@php
    $daily = $metrics['daily'] ?? [];
    $weekly = $metrics['weekly'] ?? [];
    $monthly = $metrics['monthly'] ?? [];
    $allTime = $metrics['all_time'] ?? [];
@endphp

<!-- Metrik Keuangan Eksekutif -->
<x-admin.card title="Metrik Keuangan Eksekutif &amp; Kalkulasi Laba Bersih Otomatis" icon="wallet"
    description="Dihitung otomatis dari transaksi berstatus Lunas (paid, processing, shipped, completed).">
    <x-slot:actions>
        <span class="adm-badge adm-tone-gray">
            Total Kumulatif Lunas: <strong class="ml-1" style="color:var(--adm-ink)">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</strong>
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
            <div class="space-y-1.5 pt-2 text-xs" style="border-top:1px solid var(--adm-hairline)">
                @php $dailyGrowth = (float) ($daily['revenue_growth'] ?? 0); @endphp
                <div class="flex items-center gap-2">
                    <span class="adm-badge {{ $dailyGrowth >= 0 ? 'adm-tone-green' : 'adm-tone-red' }}" style="padding:.0625rem .45rem">
                        {{ $dailyGrowth >= 0 ? '+' : '' }}{{ $dailyGrowth }}%
                    </span>
                    <span class="adm-muted text-[11px]">vs kemarin</span>
                </div>
                <div class="flex items-center justify-between pt-1">
                    <span class="adm-muted">Laba Bersih Hari Ini:</span>
                    <strong style="color:var(--adm-moss)">Rp {{ number_format($daily['net_profit'] ?? 0, 0, ',', '.') }}</strong>
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
            <div class="space-y-1.5 pt-2 text-xs" style="border-top:1px solid var(--adm-hairline)">
                @php $weeklyGrowth = (float) ($weekly['revenue_growth'] ?? 0); @endphp
                <div class="flex items-center gap-2">
                    <span class="adm-badge {{ $weeklyGrowth >= 0 ? 'adm-tone-green' : 'adm-tone-red' }}" style="padding:.0625rem .45rem">
                        {{ $weeklyGrowth >= 0 ? '+' : '' }}{{ $weeklyGrowth }}%
                    </span>
                    <span class="adm-muted text-[11px]">{{ $weekly['orders_count'] ?? 0 }} pesanan ({{ $weekly['units_sold'] ?? 0 }} unit)</span>
                </div>
                <div class="flex items-center justify-between pt-1">
                    <span class="adm-muted">Laba Bersih 7 Hari:</span>
                    <strong style="color:var(--adm-moss)">Rp {{ number_format($weekly['net_profit'] ?? 0, 0, ',', '.') }}</strong>
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
            <div class="space-y-1.5 pt-2 text-xs" style="border-top:1px solid var(--adm-hairline)">
                @php $monthlyGrowth = (float) ($monthly['revenue_growth'] ?? 0); @endphp
                <div class="flex items-center gap-2">
                    <span class="adm-badge {{ $monthlyGrowth >= 0 ? 'adm-tone-green' : 'adm-tone-red' }}" style="padding:.0625rem .45rem">
                        {{ $monthlyGrowth >= 0 ? '+' : '' }}{{ $monthlyGrowth }}%
                    </span>
                    <span class="adm-muted text-[11px]">{{ $monthly['orders_count'] ?? 0 }} pesanan ({{ $monthly['units_sold'] ?? 0 }} unit)</span>
                </div>
                <div class="flex items-center justify-between pt-1">
                    <span class="adm-muted">Laba Bersih Bulan Ini:</span>
                    <strong style="color:var(--adm-moss)">Rp {{ number_format($monthly['net_profit'] ?? 0, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        <!-- Card 4: Total Laba Bersih Otomatis -->
        <div class="adm-stat" style="background:var(--adm-paper);border-color:var(--adm-hairline)">
            <div class="adm-stat-top">
                <div class="min-w-0">
                    <p class="adm-stat-label" style="color:var(--adm-moss)">Total Laba Bersih Otomatis</p>
                    <p class="adm-stat-value" style="color:var(--adm-moss)">Rp {{ number_format($allTime['net_profit'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <span class="adm-badge adm-tone-green">
                    Margin {{ $allTime['profit_margin'] ?? 0 }}%
                </span>
            </div>
            <div class="space-y-1.5 pt-2 text-xs" style="border-top:1px solid var(--adm-hairline)">
                <p class="adm-muted text-[11px]">
                    Omzet {{ number_format($allTime['gross_items_sales'] ?? 0, 0, ',', '.') }} &minus; HPP &minus; Diskon
                </p>
                <div class="flex items-center justify-between pt-1">
                    <span class="adm-muted">Total HPP Unit:</span>
                    <strong style="color:var(--adm-ochre)">Rp {{ number_format($allTime['cogs'] ?? 0, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>
    </div>
</x-admin.card>

