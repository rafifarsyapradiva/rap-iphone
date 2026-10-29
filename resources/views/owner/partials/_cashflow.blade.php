@php
    $netCash = (float) ($cashFlow['net_cash_flow'] ?? 0);
@endphp

<!-- Ringkasan Arus Kas: Trade-In Masuk vs Penjualan Keluar -->
<x-admin.card title="Ringkasan Arus Kas: Trade-In Masuk vs Penjualan Keluar" icon="arrow-left-right"
    description="Perbandingan nilai kas masuk dari unit iPhone terjual keluar terhadap nilai akuisisi unit Trade-In / Buyback yang masuk ke toko.">
    <x-slot:actions>
        <div class="px-3.5 py-1.5 rounded text-right" style="background:var(--adm-paper);border:1px solid var(--adm-hairline)">
            <span class="text-[11px] block font-medium" style="color:var(--adm-muted)">Surplus Arus Kas Bersih</span>
            <span class="text-sm font-bold tabular-nums" style="color:{{ $netCash >= 0 ? 'var(--adm-moss)' : 'var(--adm-rose)' }}">
                {{ $netCash >= 0 ? '+' : '-' }} Rp {{ number_format(abs($netCash), 0, ',', '.') }}
            </span>
        </div>
    </x-slot:actions>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
        <!-- Box A: Penjualan Keluar (Kas Masuk) -->
        <div class="p-5 rounded" style="background:var(--adm-surface);border:1px solid var(--adm-hairline)">
            <div class="flex items-center justify-between mb-3">
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold" style="color:var(--adm-moss)">
                    <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                    Penjualan Keluar (Kas Masuk)
                </span>
                <x-admin.status-badge tone="green" :label="($cashFlow['sales_units_out'] ?? 0) . ' Unit Keluar'" :dot="false" />
            </div>
            <p class="text-2xl font-light tabular-nums" style="font-family:var(--adm-font-display);color:var(--adm-ink)">
                Rp {{ number_format($cashFlow['sales_cash_in'] ?? 0, 0, ',', '.') }}
            </p>
            <div class="grid grid-cols-2 gap-2 pt-3 mt-3 text-xs" style="border-top:1px solid var(--adm-hairline)">
                <div>
                    <span class="adm-muted block text-[11px]">Pesanan Lunas</span>
                    <span class="font-semibold" style="color:var(--adm-ink)">{{ $cashFlow['sales_orders_count'] ?? 0 }} Transaksi</span>
                </div>
                <div>
                    <span class="adm-muted block text-[11px]">Laba Bersih Penjualan</span>
                    <span class="font-semibold tabular-nums" style="color:var(--adm-moss)">Rp {{ number_format($cashFlow['sales_net_profit'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Box B: Trade-In Masuk (Akuisisi Unit) -->
        <div class="p-5 rounded" style="background:var(--adm-surface);border:1px solid var(--adm-hairline)">
            <div class="flex items-center justify-between mb-3">
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold" style="color:var(--adm-ochre)">
                    <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
                    Trade-In Masuk (Akuisisi Unit)
                </span>
                <x-admin.status-badge tone="amber" :label="($cashFlow['trade_in_units_in'] ?? 0) . ' Unit Masuk'" :dot="false" />
            </div>
            <p class="text-2xl font-light tabular-nums" style="font-family:var(--adm-font-display);color:var(--adm-ink)">
                Rp {{ number_format($cashFlow['trade_in_total_value'] ?? 0, 0, ',', '.') }}
            </p>
            <div class="grid grid-cols-2 gap-2 pt-3 mt-3 text-xs" style="border-top:1px solid var(--adm-hairline)">
                <div>
                    <span class="adm-muted block text-[11px]">Pencairan Tunai</span>
                    <span class="font-semibold tabular-nums" style="color:var(--adm-ochre)">Rp {{ number_format($cashFlow['trade_in_cash_out'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="adm-muted block text-[11px]">Kupon Belanja</span>
                    <span class="font-semibold tabular-nums" style="color:var(--adm-accent)">Rp {{ number_format($cashFlow['trade_in_voucher_out'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Box C: Posisi Kas Likuid -->
        <div class="p-5 rounded flex flex-col justify-between" style="background:var(--adm-surface);border:1px solid var(--adm-hairline)">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold" style="color:var(--adm-accent)">
                        <i data-lucide="scale" class="w-4 h-4"></i>
                        Arus Kas Tunai Likuid
                    </span>
                    <x-admin.status-badge tone="blue" :label="'Inflow ' . ($cashFlow['inflow_ratio_pct'] ?? 100) . '%'" :dot="false" />
                </div>
                <p class="text-2xl font-light tabular-nums mt-2" style="font-family:var(--adm-font-display);color:var(--adm-ink)">
                    Rp {{ number_format($cashFlow['net_liquid_cash_flow'] ?? 0, 0, ',', '.') }}
                </p>
                <p class="adm-muted text-[11px] mt-1.5">
                    Kas Masuk Penjualan dikurangi Pencairan Transfer Bank Trade-In.
                </p>
            </div>

            <div class="pt-3 mt-3 flex items-center justify-between text-xs" style="border-top:1px solid var(--adm-hairline)">
                <span class="adm-muted">Antrean Inspeksi Trade-In:</span>
                <a href="{{ route('admin.trade-ins.index') }}" class="font-semibold hover:underline" style="color:var(--adm-ochre)">
                    {{ $cashFlow['trade_in_pending_count'] ?? 0 }} Unit (Est. Rp {{ number_format($cashFlow['trade_in_pending_estimated'] ?? 0, 0, ',', '.') }})
                </a>
            </div>
        </div>
    </div>

    <!-- Visual Cash Flow Proportion Bar -->
    <div class="space-y-2">
        <div class="flex items-center justify-between text-xs font-medium">
            <span class="flex items-center gap-1.5" style="color:var(--adm-moss)">
                <span class="w-2 h-2 rounded-full" style="background:var(--adm-moss)"></span>
                Penjualan Keluar ({{ $cashFlow['inflow_ratio_pct'] ?? 100 }}%)
            </span>
            <span class="flex items-center gap-1.5" style="color:var(--adm-ochre)">
                Akuisisi Trade-In Masuk ({{ $cashFlow['outflow_ratio_pct'] ?? 0 }}%)
                <span class="w-2 h-2 rounded-full" style="background:var(--adm-ochre)"></span>
            </span>
        </div>
        <div class="w-full h-2 rounded overflow-hidden flex" style="background:var(--adm-paper);border:1px solid var(--adm-hairline)">
            <div style="width: {{ $cashFlow['inflow_ratio_pct'] ?? 100 }}%; background: var(--adm-moss); transition: width 0.3s ease;"></div>
            <div style="width: {{ $cashFlow['outflow_ratio_pct'] ?? 0 }}%; background: var(--adm-ochre); transition: width 0.3s ease;"></div>
        </div>
    </div>
</x-admin.card>

