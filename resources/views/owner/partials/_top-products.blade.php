<x-admin.card title="Analisis Produk Terlaris (Top Selling iPhone Models)" icon="award" flush>
    <x-slot:actions>
        <a href="{{ route('admin.products.index') }}" class="text-xs font-medium flex items-center gap-1 hover:underline" style="color:var(--adm-accent)">
            <span>Kelola Katalog</span>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        </a>
    </x-slot:actions>

    @if($topProducts->isEmpty())
        <div class="p-8 text-center" style="color:var(--adm-muted)">
            <p class="text-sm">Belum Ada Data Penjualan Produk Lunas</p>
            <p class="text-xs mt-1">Peringkat model iPhone terlaris akan tampil otomatis setelah transaksi lunas tercatat.</p>
        </div>
    @else
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th style="padding-left:1.5rem">Rank</th>
                        <th>Model iPhone &amp; Varian Favorit</th>
                        <th style="text-align:center">Unit</th>
                        <th class="adm-num">Total Omzet</th>
                        <th class="adm-num">Laba Produk</th>
                        <th class="adm-num" style="padding-right:1.5rem">Pangsa</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topProducts as $item)
                        <tr>
                            <td style="padding-left:1.5rem">
                                <span class="w-6 h-6 rounded inline-flex items-center justify-center text-xs font-semibold"
                                      style="{{ $item['rank'] === 1 ? 'background:var(--adm-paper);color:var(--adm-ochre);border:1px solid var(--adm-hairline)' : 'background:var(--adm-paper);color:var(--adm-muted);border:1px solid var(--adm-hairline)' }}">
                                    #{{ $item['rank'] }}
                                </span>
                            </td>
                            <td>
                                <p class="adm-cell-title">{{ $item['product_name'] }}</p>
                                <p class="adm-cell-sub">
                                    {{ $item['series'] }} &bull; Favorit: <strong style="color:var(--adm-ink)">{{ $item['popular_variant'] }}</strong>
                                </p>
                            </td>
                            <td style="text-align:center">
                                <span class="px-2 py-0.5 rounded text-xs font-medium" style="background:var(--adm-paper);border:1px solid var(--adm-hairline);color:var(--adm-ink)">
                                    {{ $item['units_sold'] }} Unit
                                </span>
                            </td>
                            <td class="adm-num font-medium tabular-nums" style="color:var(--adm-ink)">
                                Rp {{ number_format($item['revenue'], 0, ',', '.') }}
                            </td>
                            <td class="adm-num">
                                <span class="block font-medium tabular-nums" style="color:var(--adm-moss)">Rp {{ number_format($item['gross_profit'], 0, ',', '.') }}</span>
                                <span class="adm-cell-sub">Margin {{ $item['profit_margin'] }}%</span>
                            </td>
                            <td class="adm-num" style="min-width:100px;padding-right:1.5rem">
                                <span class="text-xs font-medium tabular-nums" style="color:var(--adm-ink)">{{ $item['share_pct'] }}%</span>
                                <div class="w-full h-1.5 rounded mt-1 overflow-hidden" style="background:var(--adm-paper);border:1px solid var(--adm-hairline)">
                                    <div style="height:100%;width:{{ min(100, $item['share_pct']) }}%;background:var(--adm-accent)"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-admin.card>

