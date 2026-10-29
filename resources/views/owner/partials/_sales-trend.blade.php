@php
    $trendDays = $salesTrend['days'] ?? [];
    $trendSummary = $salesTrend['summary'] ?? [];
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

<!-- Grafik Tren Transaksi Penjualan & Laba Bersih -->
<x-admin.card title="Grafik Tren Transaksi Penjualan &amp; Laba Bersih (30 Hari Terakhir)" icon="activity"
    description="Pergerakan harian omzet penjualan kotor dan laba bersih produk selama 30 hari kalender terakhir.">
    <x-slot:actions>
        <div class="flex flex-wrap items-center gap-3 text-xs">
            <div class="px-3 py-1.5 rounded" style="background:var(--adm-paper);border:1px solid var(--adm-hairline)">
                <span class="adm-muted text-[10px] block">Omzet 30 Hari</span>
                <strong class="tabular-nums" style="color:var(--adm-ink)">Rp {{ number_format($trendSummary['total_revenue_30d'] ?? 0, 0, ',', '.') }}</strong>
            </div>
            <div class="px-3 py-1.5 rounded" style="background:var(--adm-paper);border:1px solid var(--adm-hairline)">
                <span class="adm-muted text-[10px] block">Laba 30 Hari</span>
                <strong class="tabular-nums" style="color:var(--adm-moss)">Rp {{ number_format($trendSummary['total_profit_30d'] ?? 0, 0, ',', '.') }}</strong>
            </div>
            <div class="px-3 py-1.5 rounded" style="background:var(--adm-paper);border:1px solid var(--adm-hairline)">
                <span class="adm-muted text-[10px] block">Volume 30 Hari</span>
                <strong style="color:var(--adm-accent)">{{ $trendSummary['total_orders_30d'] ?? 0 }} Pesanan</strong>
            </div>
        </div>
    </x-slot:actions>

    <div class="space-y-4">
        <!-- Legend & Interactive Tooltip Readout -->
        <div class="flex flex-wrap items-center justify-between gap-4 text-xs">
            <div class="flex items-center gap-5">
                <span class="inline-flex items-center gap-2 font-medium" style="color:var(--adm-ink)">
                    <span class="w-2.5 h-2.5 rounded-full" style="background:var(--adm-accent)"></span>
                    Omzet Penjualan Harian
                </span>
                <span class="inline-flex items-center gap-2 font-medium" style="color:var(--adm-ink)">
                    <span class="w-2.5 h-2.5 rounded-full" style="background:var(--adm-moss)"></span>
                    Estimasi Laba Bersih
                </span>
            </div>
            <div id="chart-hover-readout" class="px-3.5 py-1.5 rounded text-xs" style="background:var(--adm-paper);border:1px solid var(--adm-hairline);color:var(--adm-muted)">
                Arahkan kursor pada titik grafik untuk melihat rincian tanggal, omzet, dan laba bersih.
            </div>
        </div>

        <!-- SVG Container -->
        <div class="w-full overflow-x-auto">
            <div class="min-w-[680px]">
                <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" class="w-full h-60 overflow-visible">
                    <defs>
                        <linearGradient id="ownerRevGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="var(--adm-accent)" stop-opacity="0.2"/>
                            <stop offset="100%" stop-color="var(--adm-accent)" stop-opacity="0.0"/>
                        </linearGradient>
                        <linearGradient id="ownerProfitGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="var(--adm-moss)" stop-opacity="0.2"/>
                            <stop offset="100%" stop-color="var(--adm-moss)" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>

                    <!-- Horizontal Grid Lines -->
                    @for($g = 0; $g <= 4; $g++)
                        @php $gy = round(10 + (($chartHeight - 20) / 4) * $g, 2); @endphp
                        <line x1="0" y1="{{ $gy }}" x2="{{ $chartWidth }}" y2="{{ $gy }}" stroke="var(--adm-hairline)" stroke-dasharray="4 4" stroke-width="1"/>
                    @endfor

                    <!-- Revenue Area & Line -->
                    @if(count($revPoints) > 1)
                        <polygon points="{{ $revArea }}" fill="url(#ownerRevGrad)" />
                        <polyline fill="none" stroke="var(--adm-accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" points="{{ $revPolyline }}" />

                        <!-- Net Profit Area & Line -->
                        <polygon points="{{ $profitArea }}" fill="url(#ownerProfitGrad)" />
                        <polyline fill="none" stroke="var(--adm-moss)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" points="{{ $profitPolyline }}" />
                    @endif

                    <!-- Interactive Data Points -->
                    @foreach($trendDays as $idx => $d)
                        @php
                            $cx = round($idx * $stepX, 2);
                            $cyRev = round($chartHeight - (($d['revenue'] / $maxRev) * ($chartHeight - 28)) - 10, 2);
                            $cyProf = round($chartHeight - ((max(0, $d['net_profit']) / $maxRev) * ($chartHeight - 28)) - 10, 2);
                        @endphp
                        <g class="cursor-pointer trend-point"
                           data-date="{{ $d['full_label'] }}"
                           data-revenue="{{ number_format($d['revenue'], 0, ',', '.') }}"
                           data-profit="{{ number_format($d['net_profit'], 0, ',', '.') }}"
                           data-orders="{{ $d['orders_count'] }}"
                           data-units="{{ $d['units_sold'] }}">
                            <line x1="{{ $cx }}" y1="10" x2="{{ $cx }}" y2="{{ $chartHeight - 10 }}" stroke="transparent" stroke-width="1.5"/>
                            <circle cx="{{ $cx }}" cy="{{ $cyRev }}" r="{{ $d['revenue'] > 0 ? '4' : '2' }}" fill="{{ $d['revenue'] > 0 ? 'var(--adm-accent)' : 'var(--adm-muted)' }}" stroke="var(--adm-surface)" stroke-width="1.5" />
                            @if($d['net_profit'] > 0)
                                <circle cx="{{ $cx }}" cy="{{ $cyProf }}" r="3" fill="var(--adm-moss)" stroke="var(--adm-surface)" stroke-width="1.2" />
                            @endif
                        </g>
                    @endforeach
                </svg>

                <!-- X-Axis Date Labels -->
                <div class="flex items-center justify-between text-[11px] pt-2" style="border-top:1px solid var(--adm-hairline);color:var(--adm-muted)">
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

