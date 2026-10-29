@extends('layouts.admin')

@section('title', 'Laporan Penjualan & Laba Rugi — Owner Rap-iPhone')
@section('page_title', 'Laporan Keuangan & Ekspor Data')

@section('content')
@php
    $activePeriod = $dateRange['period'] ?? 'all';
    $exportParams = [
        'period' => $activePeriod,
        'start_date' => $dateRange['start_input'] ?? '',
        'end_date' => $dateRange['end_input'] ?? '',
    ];
@endphp

<div class="adm-page">
    <!-- 1. Header with Title & Export Actions -->
    <x-admin.page-header
        title="Laporan Penjualan &amp; Laba Rugi Eksekutif"
        subtitle="Audit rincian omzet penjualan, Harga Pokok Penjualan (HPP), laba bersih per transaksi, dan arus kas akuisisi Trade-In. Unduh laporan resmi dalam format Excel/CSV atau PDF A4.">
        <x-slot:actions>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('owner.dashboard') }}" class="adm-btn adm-btn-secondary">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Dashboard Owner</span>
                </a>
                <a href="{{ route('owner.financial.export.csv', $exportParams) }}" class="adm-btn adm-btn-secondary">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4" style="color:var(--adm-moss)"></i>
                    <span>Unduh Excel / CSV</span>
                </a>
                <a href="{{ route('owner.financial.export.pdf', $exportParams) }}" target="_blank" rel="noopener" class="adm-btn adm-btn-primary">
                    <i data-lucide="file-down" class="w-4 h-4"></i>
                    <span>Unduh Laporan PDF</span>
                </a>
            </div>
        </x-slot:actions>
    </x-admin.page-header>

    <!-- 2. Filter Bar: Preset Periods & Custom Date Range -->
    <div class="adm-card p-4 sm:p-5 flex flex-col xl:flex-row xl:items-center justify-between gap-4">
        <!-- Preset Segmented Control -->
        <div class="flex flex-wrap items-center gap-1.5">
            @php
                $presets = [
                    'all' => 'Seluruh Waktu',
                    'today' => 'Hari Ini',
                    'week' => '7 Hari Terakhir',
                    'month' => 'Bulan Ini',
                    'year' => 'Tahun Ini',
                ];
            @endphp
            @foreach($presets as $key => $label)
                <a href="{{ route('owner.financial.report', ['period' => $key]) }}"
                   class="px-3.5 py-1.5 rounded text-xs font-medium transition-colors"
                   style="{{ $activePeriod === $key ? 'background:var(--adm-ink);color:var(--adm-surface);' : 'background:var(--adm-paper);color:var(--adm-muted);border:1px solid var(--adm-hairline);' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <!-- Custom Date Range Form -->
        <form action="{{ route('owner.financial.report') }}" method="GET" class="flex flex-wrap items-center gap-2.5 text-xs">
            <input type="hidden" name="period" value="custom">
            <div class="flex items-center gap-2">
                <label for="start_date" class="font-medium" style="color:var(--adm-muted)">Dari:</label>
                <input type="date" id="start_date" name="start_date" value="{{ $dateRange['start_input'] ?? '' }}"
                       class="adm-input !py-1 !px-2.5 text-xs w-auto">
            </div>
            <div class="flex items-center gap-2">
                <label for="end_date" class="font-medium" style="color:var(--adm-muted)">Sampai:</label>
                <input type="date" id="end_date" name="end_date" value="{{ $dateRange['end_input'] ?? '' }}"
                       class="adm-input !py-1 !px-2.5 text-xs w-auto">
            </div>
            <button type="submit" class="adm-btn adm-btn-primary" style="padding:.35rem .75rem;font-size:.75rem">
                <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                <span>Terapkan Rentang</span>
            </button>
        </form>
    </div>

    <!-- 3. Ringkasan Laporan Laba Rugi (P&L) & Arus Kas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Left: Ikhtisar Laba Rugi Penjualan (Profit & Loss Statement) -->
        <x-admin.card title="Laporan Laba Rugi Penjualan (P&amp;L)" subtitle="Ikhtisar Akuntansi Standar">
            <x-slot:actions>
                <span class="adm-badge adm-tone-green">
                    Margin {{ $summary['profit_margin'] ?? 0 }}%
                </span>
            </x-slot:actions>

            <div class="space-y-2 text-xs">
                <div class="flex items-center justify-between py-2" style="border-bottom:1px solid var(--adm-hairline)">
                    <span style="color:var(--adm-muted)">Penjualan Kotor Produk ({{ $summary['units_sold'] ?? 0 }} Unit / {{ $summary['orders_count'] ?? 0 }} Nota)</span>
                    <span class="font-medium tabular-nums" style="color:var(--adm-ink)">Rp {{ number_format($summary['gross_items_sales'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2" style="border-bottom:1px solid var(--adm-hairline)">
                    <span style="color:var(--adm-muted)">Potongan Diskon / Kupon Trade-In</span>
                    <span class="font-medium tabular-nums" style="color:var(--adm-rose)">&minus; Rp {{ number_format($summary['discount_total'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2" style="border-bottom:1px solid var(--adm-hairline)">
                    <span style="color:var(--adm-muted)">Pendapatan Ongkos Kirim</span>
                    <span class="font-medium tabular-nums" style="color:var(--adm-ink)">+ Rp {{ number_format($summary['shipping_revenue'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2.5 px-3 rounded" style="background:var(--adm-paper);border:1px solid var(--adm-hairline)">
                    <span class="font-semibold" style="color:var(--adm-ink)">Total Omzet Penjualan (Grand Total)</span>
                    <span class="font-bold tabular-nums text-sm" style="color:var(--adm-ink)">Rp {{ number_format($summary['revenue'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2" style="border-bottom:1px solid var(--adm-hairline)">
                    <span style="color:var(--adm-muted)">Harga Pokok Penjualan / HPP Unit (<code class="font-mono text-[11px]" style="color:var(--adm-ochre)">cost_price &times; qty</code>)</span>
                    <span class="font-medium tabular-nums" style="color:var(--adm-ochre)">&minus; Rp {{ number_format($summary['cogs'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-3.5 px-4 rounded mt-2" style="background:var(--adm-paper);border:1px solid var(--adm-moss)">
                    <div>
                        <span class="font-bold block text-sm" style="color:var(--adm-moss)">Laba Bersih Penjualan Otomatis</span>
                        <span class="text-[11px]" style="color:var(--adm-muted)">Laba Kotor Produk dikurangi Potongan Diskon</span>
                    </div>
                    <span class="font-light text-lg tabular-nums" style="font-family:var(--adm-font-display);color:var(--adm-moss)">
                        Rp {{ number_format($summary['net_profit'] ?? 0, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </x-admin.card>

        <!-- Right: Ringkasan Arus Kas Trade-In Masuk vs Penjualan Keluar -->
        <x-admin.card title="Trade-In Masuk vs Penjualan Keluar" subtitle="Rekonsiliasi Arus Kas Likuid &amp; Inventaris">
            <x-slot:actions>
                <span class="adm-badge adm-tone-blue">
                    {{ $cashFlow['trade_in_units_in'] ?? 0 }} Unit Masuk / {{ $cashFlow['sales_units_out'] ?? 0 }} Unit Keluar
                </span>
            </x-slot:actions>

            <div class="space-y-2 text-xs">
                <div class="flex items-center justify-between py-2" style="border-bottom:1px solid var(--adm-hairline)">
                    <span style="color:var(--adm-muted)">Arus Kas Masuk dari Penjualan Unit Keluar</span>
                    <span class="font-medium tabular-nums" style="color:var(--adm-moss)">+ Rp {{ number_format($cashFlow['sales_cash_in'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2" style="border-bottom:1px solid var(--adm-hairline)">
                    <span style="color:var(--adm-muted)">Arus Kas Keluar Trade-In (Transfer Bank Pelanggan)</span>
                    <span class="font-medium tabular-nums" style="color:var(--adm-ochre)">&minus; Rp {{ number_format($cashFlow['trade_in_cash_out'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2" style="border-bottom:1px solid var(--adm-hairline)">
                    <span style="color:var(--adm-muted)">Nilai Kupon Potongan Trade-In (Voucher Belanja)</span>
                    <span class="font-medium tabular-nums" style="color:var(--adm-accent)">Rp {{ number_format($cashFlow['trade_in_voucher_out'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2.5 px-3 rounded" style="background:var(--adm-paper);border:1px solid var(--adm-hairline)">
                    <span class="font-semibold" style="color:var(--adm-ink)">Total Nilai Akuisisi Trade-In Masuk ({{ $cashFlow['trade_in_units_in'] ?? 0 }} Unit)</span>
                    <span class="font-bold tabular-nums text-sm" style="color:var(--adm-ochre)">Rp {{ number_format($cashFlow['trade_in_total_value'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2" style="border-bottom:1px solid var(--adm-hairline)">
                    <span style="color:var(--adm-muted)">Antrean Trade-In Menunggu Penyelesaian ({{ $cashFlow['trade_in_pending_count'] ?? 0 }} Unit)</span>
                    <span class="tabular-nums" style="color:var(--adm-muted)">Est. Rp {{ number_format($cashFlow['trade_in_pending_estimated'] ?? 0, 0, ',', '.') }}</span>
                </div>
                @php $netFlow = (float) ($cashFlow['net_cash_flow'] ?? 0); @endphp
                <div class="flex items-center justify-between py-3.5 px-4 rounded mt-2" style="background:var(--adm-paper);border:1px solid var(--adm-hairline)">
                    <div>
                        <span class="font-bold block text-sm" style="color:var(--adm-ink)">Surplus Arus Kas Bersih (Net Cash Flow)</span>
                        <span class="text-[11px]" style="color:var(--adm-muted)">Penjualan Keluar dikurangi Total Akuisisi Trade-In</span>
                    </div>
                    <span class="font-light text-lg tabular-nums" style="font-family:var(--adm-font-display);color:{{ $netFlow >= 0 ? 'var(--adm-moss)' : 'var(--adm-rose)' }}">
                        {{ $netFlow >= 0 ? '+' : '-' }} Rp {{ number_format(abs($netFlow), 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </x-admin.card>

    </div>

    <!-- 4. Tabel Rincian Transaksi Penjualan & Kalkulasi Laba Bersih per Nota -->
    <x-admin.card
        title="Tabel Rincian Transaksi Penjualan &amp; Laba Bersih ({{ $orders->count() }} Nota Lunas)"
        subtitle="Rincian harga jual, diskon, HPP, dan laba bersih setiap pesanan pada periode terpilih."
        flush
    >
        @if($orders->isEmpty())
            <div class="p-8 text-center" style="color:var(--adm-muted)">
                <p class="text-sm">Tidak Ada Transaksi Penjualan</p>
                <p class="text-xs mt-1">Tidak ada transaksi penjualan lunas yang tercatat pada rentang tanggal ini.</p>
            </div>
        @else
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th style="padding-left:1.5rem">No. Nota</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Rincian Unit</th>
                            <th style="text-align:center">Status</th>
                            <th class="adm-num">Omzet</th>
                            <th class="adm-num">HPP Unit</th>
                            <th class="adm-num">Diskon</th>
                            <th class="adm-num" style="padding-right:1.5rem">Laba Bersih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td style="padding-left:1.5rem">
                                    <a href="{{ route('admin.orders.show', $order->order_number) }}" class="adm-mono font-medium hover:underline" style="color:var(--adm-accent)">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="adm-cell-sub">{{ $order->created_at?->format('d M Y H:i') }}</td>
                                <td class="adm-cell-title">{{ $order->recipient_name ?: ($order->user?->name ?? 'Pelanggan') }}</td>
                                <td class="adm-cell-sub max-w-xs truncate" title="{{ $order->items_summary }}">
                                    {{ $order->items_summary }}
                                </td>
                                <td style="text-align:center">
                                    <x-admin.status-badge :status="$order->status" />
                                </td>
                                <td class="adm-num font-medium tabular-nums" style="color:var(--adm-ink)">
                                    Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}
                                </td>
                                <td class="adm-num tabular-nums" style="color:var(--adm-ochre)">
                                    Rp {{ number_format((float) $order->calculated_cogs, 0, ',', '.') }}
                                </td>
                                <td class="adm-num tabular-nums" style="color:var(--adm-muted)">
                                    Rp {{ number_format((float) $order->discount_amount, 0, ',', '.') }}
                                </td>
                                <td class="adm-num font-medium tabular-nums" style="color:var(--adm-moss);padding-right:1.5rem">
                                    Rp {{ number_format((float) $order->calculated_net_profit, 0, ',', '.') }}
                                    <span class="block text-[10px] font-normal" style="color:var(--adm-muted)">({{ $order->calculated_margin }}%)</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background:var(--adm-paper);border-top:2px solid var(--adm-hairline);border-bottom:3px double var(--adm-ink);font-weight:600">
                            <td colspan="5" class="py-3 px-4 text-right text-xs" style="color:var(--adm-muted)">Total Periode Ini:</td>
                            <td class="py-3 px-4 text-right tabular-nums" style="color:var(--adm-ink)">Rp {{ number_format($summary['revenue'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right tabular-nums" style="color:var(--adm-ochre)">Rp {{ number_format($summary['cogs'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right tabular-nums" style="color:var(--adm-muted)">Rp {{ number_format($summary['discount_total'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right tabular-nums font-bold" style="color:var(--adm-moss);padding-right:1.5rem">Rp {{ number_format($summary['net_profit'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </x-admin.card>

    <!-- 5. Tabel Rincian Akuisisi Unit Trade-In Masuk -->
    <x-admin.card
        title="Rincian Akuisisi Unit Trade-In Masuk ({{ $tradeIns->count() }} Unit Disetujui / Dicairkan)"
        subtitle="Daftar unit iPhone pelanggan yang masuk melalui program Jual / Tukar Tambah."
        flush
    >
        @if($tradeIns->isEmpty())
            <div class="p-8 text-center" style="color:var(--adm-muted)">
                <p class="text-sm">Tidak Ada Unit Trade-In</p>
                <p class="text-xs mt-1">Tidak ada unit Trade-In berstatus disetujui atau lunas pada rentang tanggal ini.</p>
            </div>
        @else
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th style="padding-left:1.5rem">Kode Trade-In</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Unit iPhone Masuk</th>
                            <th>Kondisi &amp; BH</th>
                            <th>Metode Pencairan</th>
                            <th class="adm-num" style="padding-right:1.5rem">Nilai Deal / Akuisisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tradeIns as $ti)
                            <tr>
                                <td style="padding-left:1.5rem">
                                    <a href="{{ route('admin.trade-ins.show', $ti->trade_in_code) }}" class="adm-mono font-medium hover:underline" style="color:var(--adm-accent)">
                                        {{ $ti->trade_in_code }}
                                    </a>
                                </td>
                                <td class="adm-cell-sub">{{ $ti->created_at?->format('d M Y') }}</td>
                                <td class="adm-cell-title">{{ $ti->user?->name ?? $ti->customer_account_holder ?? 'Pelanggan' }}</td>
                                <td class="adm-cell-title">{{ $ti->phone_model }} ({{ $ti->storage_capacity }})</td>
                                <td class="adm-cell-sub">{{ $ti->physical_condition }} &bull; BH {{ $ti->battery_health }}%</td>
                                <td>
                                    @if($ti->isVoucherPayout())
                                        <x-admin.status-badge tone="blue" label="Voucher Potongan" :dot="false" />
                                    @else
                                        <x-admin.status-badge tone="amber" :label="'Transfer ' . $ti->customer_bank_name" :dot="false" />
                                    @endif
                                </td>
                                <td class="adm-num font-medium tabular-nums" style="color:var(--adm-ochre);padding-right:1.5rem">
                                    Rp {{ number_format($ti->effective_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-admin.card>
</div>
@endsection
