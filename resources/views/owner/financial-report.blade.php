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

<div class="space-y-8">

    <!-- 1. Top Header & Export Actions -->
    <div class="relative overflow-hidden rounded-3xl p-6 sm:p-8 glass-card-elevated border border-white/15">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-[#34C759]/20 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#0071E3]/20 border border-[#0071E3]/40 text-[#5AC8FA] text-xs font-semibold mb-3">
                    <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5"></i>
                    <span>Periode Aktif: {{ $dateRange['label'] ?? 'Seluruh Periode' }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                    Laporan Penjualan &amp; Laba Rugi Eksekutif
                </h1>
                <p class="text-xs sm:text-sm text-[#8E8E93] mt-1 max-w-2xl">
                    Audit rincian omzet penjualan, Harga Pokok Penjualan (HPP), laba bersih per transaksi, dan arus kas akuisisi Trade-In. Unduh laporan resmi dalam format Excel/CSV atau PDF A4.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <a href="{{ route('owner.dashboard') }}" class="glass-btn glass-btn-secondary glass-btn-sm flex items-center gap-2">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Dashboard Owner</span>
                </a>
                <a href="{{ route('owner.financial.export.csv', $exportParams) }}" class="glass-btn glass-btn-secondary glass-btn-sm flex items-center gap-2 border-[#34C759]/40">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 text-[#34C759]"></i>
                    <span>Unduh Excel / CSV</span>
                </a>
                <a href="{{ route('owner.financial.export.pdf', $exportParams) }}" target="_blank" class="glass-btn glass-btn-primary glass-btn-sm flex items-center gap-2">
                    <i data-lucide="file-down" class="w-4 h-4"></i>
                    <span>Unduh Laporan PDF</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. Filter Bar: Preset Periods & Custom Date Range -->
    <div class="glass-card rounded-2xl p-5 border border-white/10 flex flex-col xl:flex-row xl:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2">
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
                   class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all {{ $activePeriod === $key ? 'bg-[#0071E3] text-white shadow-lg shadow-[#0071E3]/30' : 'bg-white/5 text-[#8E8E93] hover:text-white hover:bg-white/10 border border-white/10' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <form action="{{ route('owner.financial.report') }}" method="GET" class="flex flex-wrap items-center gap-2.5 text-xs">
            <input type="hidden" name="period" value="custom">
            <div class="flex items-center gap-2">
                <label for="start_date" class="text-[#8E8E93] font-medium">Dari:</label>
                <input type="date" id="start_date" name="start_date" value="{{ $dateRange['start_input'] ?? '' }}"
                       class="glass-input !py-1.5 !px-3 text-xs rounded-xl bg-white/5 border border-white/15 text-white">
            </div>
            <div class="flex items-center gap-2">
                <label for="end_date" class="text-[#8E8E93] font-medium">Sampai:</label>
                <input type="date" id="end_date" name="end_date" value="{{ $dateRange['end_input'] ?? '' }}"
                       class="glass-input !py-1.5 !px-3 text-xs rounded-xl bg-white/5 border border-white/15 text-white">
            </div>
            <button type="submit" class="glass-btn glass-btn-primary glass-btn-sm flex items-center gap-1.5">
                <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                <span>Terapkan Rentang</span>
            </button>
        </form>
    </div>

    <!-- 3. Ringkasan Laporan Laba Rugi (P&L) & Arus Kas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Left: Ikhtisar Laba Rugi Penjualan (Profit & Loss Statement) -->
        <div class="glass-card rounded-3xl p-6 border border-white/10 space-y-4">
            <div class="flex items-center justify-between border-b border-white/10 pb-3">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#0071E3] block">Ikhtisar Akuntansi</span>
                    <h2 class="text-base font-bold text-white">Laporan Laba Rugi Penjualan (P&amp;L)</h2>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#34C759]/15 text-[#34C759] border border-[#34C759]/30">
                    Margin {{ $summary['profit_margin'] ?? 0 }}%
                </span>
            </div>

            <div class="space-y-2.5 text-xs">
                <div class="flex items-center justify-between py-2 border-b border-white/5">
                    <span class="text-[#8E8E93]">Penjualan Kotor Produk ({{ $summary['units_sold'] ?? 0 }} Unit / {{ $summary['orders_count'] ?? 0 }} Nota)</span>
                    <span class="font-semibold text-white">Rp {{ number_format($summary['gross_items_sales'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-white/5">
                    <span class="text-[#8E8E93]">Potongan Diskon / Kupon Trade-In</span>
                    <span class="font-semibold text-[#FF453A]">&minus; Rp {{ number_format($summary['discount_total'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-white/5">
                    <span class="text-[#8E8E93]">Pendapatan Ongkos Kirim</span>
                    <span class="font-semibold text-[#E5E5EA]">+ Rp {{ number_format($summary['shipping_revenue'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2.5 px-3 rounded-xl bg-white/5 border border-white/10">
                    <span class="font-bold text-white">Total Omzet Penjualan (Grand Total)</span>
                    <span class="font-extrabold text-white text-sm">Rp {{ number_format($summary['revenue'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-white/5">
                    <span class="text-[#8E8E93]">Harga Pokok Penjualan / HPP Unit (<code class="text-[#FF9F0A]">cost_price &times; qty</code>)</span>
                    <span class="font-semibold text-[#FF9F0A]">&minus; Rp {{ number_format($summary['cogs'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-3 px-4 rounded-2xl bg-[#34C759]/15 border border-[#34C759]/35">
                    <div>
                        <span class="font-extrabold text-[#34C759] block text-sm">Laba Bersih Penjualan Otomatis</span>
                        <span class="text-[11px] text-[#E5E5EA]">Laba Kotor Produk dikurangi Potongan Diskon</span>
                    </div>
                    <span class="font-extrabold text-[#34C759] text-lg">
                        Rp {{ number_format($summary['net_profit'] ?? 0, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Right: Ringkasan Arus Kas Trade-In Masuk vs Penjualan Keluar -->
        <div class="glass-card rounded-3xl p-6 border border-white/10 space-y-4">
            <div class="flex items-center justify-between border-b border-white/10 pb-3">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#FF9F0A] block">Rekonsiliasi Arus Kas</span>
                    <h2 class="text-base font-bold text-white">Trade-In Masuk vs Penjualan Keluar</h2>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#0071E3]/15 text-[#5AC8FA] border border-[#0071E3]/30">
                    {{ $cashFlow['trade_in_units_in'] ?? 0 }} Unit Masuk / {{ $cashFlow['sales_units_out'] ?? 0 }} Unit Keluar
                </span>
            </div>

            <div class="space-y-2.5 text-xs">
                <div class="flex items-center justify-between py-2 border-b border-white/5">
                    <span class="text-[#8E8E93]">Arus Kas Masuk dari Penjualan Unit Keluar</span>
                    <span class="font-bold text-[#34C759]">+ Rp {{ number_format($cashFlow['sales_cash_in'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-white/5">
                    <span class="text-[#8E8E93]">Arus Kas Keluar Trade-In (Transfer Bank Pelanggan)</span>
                    <span class="font-semibold text-[#FF9F0A]">&minus; Rp {{ number_format($cashFlow['trade_in_cash_out'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-white/5">
                    <span class="text-[#8E8E93]">Nilai Kupon Potongan Trade-In (Voucher Belanja)</span>
                    <span class="font-semibold text-[#5AC8FA]">Rp {{ number_format($cashFlow['trade_in_voucher_out'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2.5 px-3 rounded-xl bg-white/5 border border-white/10">
                    <span class="font-bold text-white">Total Nilai Akuisisi Trade-In Masuk ({{ $cashFlow['trade_in_units_in'] ?? 0 }} Unit)</span>
                    <span class="font-extrabold text-[#FF9F0A] text-sm">Rp {{ number_format($cashFlow['trade_in_total_value'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-white/5">
                    <span class="text-[#8E8E93]">Antrean Trade-In Menunggu Penyelesaian ({{ $cashFlow['trade_in_pending_count'] ?? 0 }} Unit)</span>
                    <span class="font-semibold text-[#8E8E93]">Est. Rp {{ number_format($cashFlow['trade_in_pending_estimated'] ?? 0, 0, ',', '.') }}</span>
                </div>
                @php $netFlow = (float) ($cashFlow['net_cash_flow'] ?? 0); @endphp
                <div class="flex items-center justify-between py-3 px-4 rounded-2xl {{ $netFlow >= 0 ? 'bg-[#0071E3]/15 border border-[#0071E3]/35' : 'bg-[#FF453A]/15 border border-[#FF453A]/35' }}">
                    <div>
                        <span class="font-extrabold text-white block text-sm">Surplus Arus Kas Bersih (Net Cash Flow)</span>
                        <span class="text-[11px] text-[#8E8E93]">Penjualan Keluar dikurangi Total Akuisisi Trade-In</span>
                    </div>
                    <span class="font-extrabold {{ $netFlow >= 0 ? 'text-[#5AC8FA]' : 'text-[#FF453A]' }} text-lg">
                        {{ $netFlow >= 0 ? '+' : '-' }} Rp {{ number_format(abs($netFlow), 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

    </div>

    <!-- 4. Tabel Rincian Transaksi Penjualan & Kalkulasi Laba Bersih per Nota -->
    <div class="glass-card rounded-3xl p-6 border border-white/10 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-white/10 pb-4">
            <div>
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <i data-lucide="shopping-bag" class="w-4 h-4 text-[#0071E3]"></i>
                    <span>Tabel Rincian Transaksi Penjualan &amp; Laba Bersih ({{ $orders->count() }} Nota Lunas)</span>
                </h2>
                <p class="text-xs text-[#8E8E93]">Menampilkan rincian harga jual, diskon, HPP, dan laba bersih setiap pesanan pada periode terpilih.</p>
            </div>
        </div>

        @if($orders->isEmpty())
            <div class="p-8 text-center rounded-2xl bg-white/[0.02] border border-white/5 text-xs text-[#8E8E93]">
                Tidak ada transaksi penjualan lunas pada rentang tanggal ini.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-white/10 text-[#8E8E93] uppercase tracking-wider font-semibold">
                            <th class="py-3 px-3">No. Nota</th>
                            <th class="py-3 px-3">Tanggal</th>
                            <th class="py-3 px-3">Pelanggan</th>
                            <th class="py-3 px-3">Rincian Unit</th>
                            <th class="py-3 px-3 text-center">Status</th>
                            <th class="py-3 px-3 text-right">Omzet (Rp)</th>
                            <th class="py-3 px-3 text-right">HPP Unit (Rp)</th>
                            <th class="py-3 px-3 text-right">Diskon (Rp)</th>
                            <th class="py-3 px-3 text-right">Laba Bersih (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-[#E5E5EA]">
                        @foreach($orders as $order)
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="py-3.5 px-3 font-mono font-bold text-white">
                                    <a href="{{ route('admin.orders.show', $order->order_number) }}" class="hover:text-[#5AC8FA] underline decoration-white/20">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-3 text-[#8E8E93] whitespace-nowrap">{{ $order->created_at?->format('d M Y H:i') }}</td>
                                <td class="py-3.5 px-3 font-medium text-white">{{ $order->recipient_name ?: ($order->user?->name ?? 'Pelanggan') }}</td>
                                <td class="py-3.5 px-3 max-w-xs truncate" title="{{ $order->items_summary }}">
                                    {{ $order->items_summary }}
                                </td>
                                <td class="py-3.5 px-3 text-center"><x-order-badge :status="$order->status" /></td>
                                <td class="py-3.5 px-3 text-right font-semibold text-white whitespace-nowrap">
                                    Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3 text-right text-[#FF9F0A] whitespace-nowrap">
                                    Rp {{ number_format((float) $order->calculated_cogs, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3 text-right text-[#8E8E93] whitespace-nowrap">
                                    Rp {{ number_format((float) $order->discount_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3 text-right font-bold text-[#34C759] whitespace-nowrap">
                                    Rp {{ number_format((float) $order->calculated_net_profit, 0, ',', '.') }}
                                    <span class="block text-[10px] font-normal text-[#8E8E93]">({{ $order->calculated_margin }}%)</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-white/15 bg-white/[0.03] font-bold text-white">
                            <td colspan="5" class="py-3.5 px-3 text-right uppercase tracking-wider text-xs text-[#8E8E93]">Total Periode Ini:</td>
                            <td class="py-3.5 px-3 text-right">Rp {{ number_format($summary['revenue'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-3 text-right text-[#FF9F0A]">Rp {{ number_format($summary['cogs'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-3 text-right text-[#8E8E93]">Rp {{ number_format($summary['discount_total'] ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-3 text-right text-[#34C759]">Rp {{ number_format($summary['net_profit'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

    <!-- 5. Tabel Rincian Akuisisi Unit Trade-In Masuk -->
    <div class="glass-card rounded-3xl p-6 border border-white/10 space-y-4">
        <div class="flex items-center justify-between border-b border-white/10 pb-4">
            <div>
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <i data-lucide="refresh-cw" class="w-4 h-4 text-[#FF9F0A]"></i>
                    <span>Rincian Akuisisi Unit Trade-In Masuk ({{ $tradeIns->count() }} Unit Disetujui / Dicairkan)</span>
                </h2>
                <p class="text-xs text-[#8E8E93]">Daftar unit iPhone pelanggan yang masuk melalui program Jual / Tukar Tambah.</p>
            </div>
        </div>

        @if($tradeIns->isEmpty())
            <div class="p-6 text-center text-xs text-[#8E8E93]">
                Tidak ada unit Trade-In berstatus disetujui/lunas pada rentang tanggal ini.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-white/10 text-[#8E8E93] uppercase tracking-wider font-semibold">
                            <th class="py-3 px-3">Kode Trade-In</th>
                            <th class="py-3 px-3">Tanggal</th>
                            <th class="py-3 px-3">Pelanggan</th>
                            <th class="py-3 px-3">Unit iPhone Masuk</th>
                            <th class="py-3 px-3">Kondisi &amp; BH</th>
                            <th class="py-3 px-3">Metode Pencairan</th>
                            <th class="py-3 px-3 text-right">Nilai Deal / Akuisisi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-[#E5E5EA]">
                        @foreach($tradeIns as $ti)
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="py-3 px-3 font-mono font-bold text-white">
                                    <a href="{{ route('admin.trade-ins.show', $ti->trade_in_code) }}" class="hover:text-[#5AC8FA] underline decoration-white/20">
                                        {{ $ti->trade_in_code }}
                                    </a>
                                </td>
                                <td class="py-3 px-3 text-[#8E8E93]">{{ $ti->created_at?->format('d M Y') }}</td>
                                <td class="py-3 px-3 text-white">{{ $ti->user?->name ?? $ti->customer_account_holder ?? 'Pelanggan' }}</td>
                                <td class="py-3 px-3 font-semibold text-white">{{ $ti->phone_model }} ({{ $ti->storage_capacity }})</td>
                                <td class="py-3 px-3 text-[#8E8E93]">{{ $ti->physical_condition }} &bull; BH {{ $ti->battery_health }}%</td>
                                <td class="py-3 px-3">
                                    @if($ti->isVoucherPayout())
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#0071E3]/15 text-[#5AC8FA]">Voucher Potongan</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#FF9F0A]/15 text-[#FF9F0A]">Transfer {{ $ti->customer_bank_name }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-right font-bold text-[#FF9F0A]">
                                    Rp {{ number_format($ti->effective_price, 0, ',', '.') }}
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

