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

<div class="space-y-6">

    <!-- 1. Header with Title & Export Actions -->
    <x-admin.page-header
        title="Laporan Penjualan &amp; Laba Rugi Eksekutif"
        subtitle="Audit rincian omzet penjualan, Harga Pokok Penjualan (HPP), laba bersih per transaksi, dan arus kas akuisisi Trade-In. Unduh laporan resmi dalam format Excel/CSV atau PDF A4."
    >
        <x-slot:actions>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('owner.dashboard') }}" class="adm-btn adm-btn-secondary adm-btn-sm inline-flex items-center gap-2">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-slate-500"></i>
                    <span>Dashboard Owner</span>
                </a>
                <a href="{{ route('owner.financial.export.csv', $exportParams) }}" class="adm-btn adm-btn-secondary adm-btn-sm inline-flex items-center gap-2 text-emerald-700 border-emerald-200 hover:bg-emerald-50 hover:border-emerald-300">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600"></i>
                    <span>Unduh Excel / CSV</span>
                </a>
                <a href="{{ route('owner.financial.export.pdf', $exportParams) }}" target="_blank" class="adm-btn adm-btn-primary adm-btn-sm inline-flex items-center gap-2">
                    <i data-lucide="file-down" class="w-4 h-4"></i>
                    <span>Unduh Laporan PDF</span>
                </a>
            </div>
        </x-slot:actions>
    </x-admin.page-header>

    <!-- 2. Filter Bar: Preset Periods & Custom Date Range -->
    <div class="adm-card p-4 sm:p-5 flex flex-col xl:flex-row xl:items-center justify-between gap-4">
        <!-- Preset pills -->
        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
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
                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $activePeriod === $key ? 'bg-[#0071E3] text-white shadow-sm shadow-[#0071E3]/25' : 'bg-slate-100 text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 border border-transparent' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <!-- Custom date range form -->
        <form action="{{ route('owner.financial.report') }}" method="GET" class="flex flex-wrap items-center gap-2.5 text-xs">
            <input type="hidden" name="period" value="custom">
            <div class="flex items-center gap-2">
                <label for="start_date" class="text-slate-500 font-medium">Dari:</label>
                <input type="date" id="start_date" name="start_date" value="{{ $dateRange['start_input'] ?? '' }}"
                       class="adm-input !py-1.5 !px-3 text-xs w-auto">
            </div>
            <div class="flex items-center gap-2">
                <label for="end_date" class="text-slate-500 font-medium">Sampai:</label>
                <input type="date" id="end_date" name="end_date" value="{{ $dateRange['end_input'] ?? '' }}"
                       class="adm-input !py-1.5 !px-3 text-xs w-auto">
            </div>
            <button type="submit" class="adm-btn adm-btn-primary adm-btn-sm inline-flex items-center gap-1.5">
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
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Margin {{ $summary['profit_margin'] ?? 0 }}%
                </span>
            </x-slot:actions>

            <div class="space-y-2 text-xs">
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Penjualan Kotor Produk ({{ $summary['units_sold'] ?? 0 }} Unit / {{ $summary['orders_count'] ?? 0 }} Nota)</span>
                    <span class="font-semibold text-slate-900">Rp {{ number_format($summary['gross_items_sales'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Potongan Diskon / Kupon Trade-In</span>
                    <span class="font-semibold text-rose-600">&minus; Rp {{ number_format($summary['discount_total'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Pendapatan Ongkos Kirim</span>
                    <span class="font-semibold text-slate-700">+ Rp {{ number_format($summary['shipping_revenue'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2.5 px-3 rounded-xl bg-slate-50 border border-slate-200/80">
                    <span class="font-bold text-slate-800">Total Omzet Penjualan (Grand Total)</span>
                    <span class="font-bold text-slate-900 text-sm">Rp {{ number_format($summary['revenue'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Harga Pokok Penjualan / HPP Unit (<code class="text-amber-700 font-mono text-[11px]">cost_price &times; qty</code>)</span>
                    <span class="font-semibold text-amber-600">&minus; Rp {{ number_format($summary['cogs'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-3.5 px-4 rounded-2xl bg-emerald-50 border border-emerald-200 mt-2">
                    <div>
                        <span class="font-bold text-emerald-800 block text-sm">Laba Bersih Penjualan Otomatis</span>
                        <span class="text-[11px] text-emerald-600">Laba Kotor Produk dikurangi Potongan Diskon</span>
                    </div>
                    <span class="font-black text-emerald-700 text-lg">
                        Rp {{ number_format($summary['net_profit'] ?? 0, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </x-admin.card>

        <!-- Right: Ringkasan Arus Kas Trade-In Masuk vs Penjualan Keluar -->
        <x-admin.card title="Trade-In Masuk vs Penjualan Keluar" subtitle="Rekonsiliasi Arus Kas Likuid &amp; Inventaris">
            <x-slot:actions>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                    {{ $cashFlow['trade_in_units_in'] ?? 0 }} Unit Masuk / {{ $cashFlow['sales_units_out'] ?? 0 }} Unit Keluar
                </span>
            </x-slot:actions>

            <div class="space-y-2 text-xs">
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Arus Kas Masuk dari Penjualan Unit Keluar</span>
                    <span class="font-bold text-emerald-600">+ Rp {{ number_format($cashFlow['sales_cash_in'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Arus Kas Keluar Trade-In (Transfer Bank Pelanggan)</span>
                    <span class="font-semibold text-amber-600">&minus; Rp {{ number_format($cashFlow['trade_in_cash_out'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Nilai Kupon Potongan Trade-In (Voucher Belanja)</span>
                    <span class="font-semibold text-blue-600">Rp {{ number_format($cashFlow['trade_in_voucher_out'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2.5 px-3 rounded-xl bg-slate-50 border border-slate-200/80">
                    <span class="font-bold text-slate-800">Total Nilai Akuisisi Trade-In Masuk ({{ $cashFlow['trade_in_units_in'] ?? 0 }} Unit)</span>
                    <span class="font-bold text-amber-700 text-sm">Rp {{ number_format($cashFlow['trade_in_total_value'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Antrean Trade-In Menunggu Penyelesaian ({{ $cashFlow['trade_in_pending_count'] ?? 0 }} Unit)</span>
                    <span class="font-semibold text-slate-500">Est. Rp {{ number_format($cashFlow['trade_in_pending_estimated'] ?? 0, 0, ',', '.') }}</span>
                </div>
                @php $netFlow = (float) ($cashFlow['net_cash_flow'] ?? 0); @endphp
                <div class="flex items-center justify-between py-3.5 px-4 rounded-2xl {{ $netFlow >= 0 ? 'bg-blue-50 border border-blue-200' : 'bg-rose-50 border border-rose-200' }} mt-2">
                    <div>
                        <span class="font-bold text-slate-900 block text-sm">Surplus Arus Kas Bersih (Net Cash Flow)</span>
                        <span class="text-[11px] text-slate-500">Penjualan Keluar dikurangi Total Akuisisi Trade-In</span>
                    </div>
                    <span class="font-black {{ $netFlow >= 0 ? 'text-blue-700' : 'text-rose-600' }} text-lg">
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
    >
        @if($orders->isEmpty())
            <x-admin.empty-state
                icon="shopping-bag"
                title="Tidak Ada Transaksi Penjualan"
                description="Tidak ada transaksi penjualan lunas yang tercatat pada rentang tanggal ini."
            />
        @else
            <div class="overflow-x-auto -mx-5 -mb-5 sm:-mx-6 sm:-mb-6">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>No. Nota</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Rincian Unit</th>
                            <th class="text-center">Status</th>
                            <th class="text-right">Omzet (Rp)</th>
                            <th class="text-right">HPP Unit (Rp)</th>
                            <th class="text-right">Diskon (Rp)</th>
                            <th class="text-right">Laba Bersih (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td class="font-mono font-bold text-slate-900 whitespace-nowrap">
                                    <a href="{{ route('admin.orders.show', $order->order_number) }}" class="text-[#0071E3] hover:underline">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="text-slate-500 whitespace-nowrap">{{ $order->created_at?->format('d M Y H:i') }}</td>
                                <td class="font-medium text-slate-900">{{ $order->recipient_name ?: ($order->user?->name ?? 'Pelanggan') }}</td>
                                <td class="max-w-xs truncate text-slate-600" title="{{ $order->items_summary }}">
                                    {{ $order->items_summary }}
                                </td>
                                <td class="text-center">
                                    <x-admin.status-badge :status="$order->status" />
                                </td>
                                <td class="text-right font-semibold text-slate-900 whitespace-nowrap">
                                    Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}
                                </td>
                                <td class="text-right text-amber-600 whitespace-nowrap">
                                    Rp {{ number_format((float) $order->calculated_cogs, 0, ',', '.') }}
                                </td>
                                <td class="text-right text-slate-500 whitespace-nowrap">
                                    Rp {{ number_format((float) $order->discount_amount, 0, ',', '.') }}
                                </td>
                                <td class="text-right font-bold text-emerald-600 whitespace-nowrap">
                                    Rp {{ number_format((float) $order->calculated_net_profit, 0, ',', '.') }}
                                    <span class="block text-[10px] font-normal text-slate-400">({{ $order->calculated_margin }}%)</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50 border-t-2 border-slate-200 font-bold text-slate-900">
                            <td colspan="5" class="py-3.5 px-4 text-right uppercase tracking-wider text-xs text-slate-500">Total Periode Ini:</td>
                            <td class="py-3.5 px-4 text-right">Rp {{ number_format($summary['revenue'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right text-amber-600">Rp {{ number_format($summary['cogs'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right text-slate-500">Rp {{ number_format($summary['discount_total'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right text-emerald-600">Rp {{ number_format($summary['net_profit'] ?? 0, 0, ',', '.') }}</td>
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
    >
        @if($tradeIns->isEmpty())
            <x-admin.empty-state
                icon="refresh-cw"
                title="Tidak Ada Unit Trade-In"
                description="Tidak ada unit Trade-In berstatus disetujui atau lunas pada rentang tanggal ini."
            />
        @else
            <div class="overflow-x-auto -mx-5 -mb-5 sm:-mx-6 sm:-mb-6">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Kode Trade-In</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Unit iPhone Masuk</th>
                            <th>Kondisi &amp; BH</th>
                            <th>Metode Pencairan</th>
                            <th class="text-right">Nilai Deal / Akuisisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tradeIns as $ti)
                            <tr>
                                <td class="font-mono font-bold text-slate-900 whitespace-nowrap">
                                    <a href="{{ route('admin.trade-ins.show', $ti->trade_in_code) }}" class="text-[#0071E3] hover:underline">
                                        {{ $ti->trade_in_code }}
                                    </a>
                                </td>
                                <td class="text-slate-500 whitespace-nowrap">{{ $ti->created_at?->format('d M Y') }}</td>
                                <td class="text-slate-900 font-medium">{{ $ti->user?->name ?? $ti->customer_account_holder ?? 'Pelanggan' }}</td>
                                <td class="font-semibold text-slate-900">{{ $ti->phone_model }} ({{ $ti->storage_capacity }})</td>
                                <td class="text-slate-600">{{ $ti->physical_condition }} &bull; BH {{ $ti->battery_health }}%</td>
                                <td>
                                    @if($ti->isVoucherPayout())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                            Voucher Potongan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            Transfer {{ $ti->customer_bank_name }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-right font-bold text-amber-700 whitespace-nowrap">
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
