@extends('layouts.admin')

@section('title', 'Portal Finansial & Analitik Laba — Owner Rap-iPhone')
@section('page_title', 'Eksekutif Finansial & Omzet (Owner Only)')

@section('content')
<div class="adm-page">
    <!-- 1. Owner Executive Page Header -->
    <x-admin.page-header
        eyebrow="Tingkat Akses Tertinggi: Pemilik Usaha (Owner)"
        icon="crown"
        title="Portal Finansial &amp; Analitik Laba"
        subtitle="Pantau total omzet penjualan (Harian, Mingguan, Bulanan), kalkulasi otomatis laba bersih terhadap HPP unit iPhone, arus kas Trade-In masuk vs penjualan keluar, serta tren bisnis 30 hari terakhir.">
        <x-slot:actions>
            <a href="{{ route('owner.financial.report') }}" class="adm-btn adm-btn-primary">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                <span>Laporan &amp; Laba Rugi</span>
            </a>
            <a href="{{ route('owner.financial.export.csv') }}" class="adm-btn adm-btn-secondary">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Ekspor CSV / Excel</span>
            </a>
            <a href="{{ route('owner.financial.export.pdf') }}" target="_blank" rel="noopener" class="adm-btn adm-btn-secondary">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                <span>Ekspor PDF</span>
            </a>
            <a href="{{ route('owner.staff.index') }}" class="adm-btn adm-btn-secondary">
                <i data-lucide="users" class="w-4 h-4"></i>
                <span>Kelola Staf ({{ ($totalAdmins ?? 0) + ($totalOwners ?? 1) }})</span>
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <!-- 2. Metrik Keuangan Eksekutif (Partial) -->
    @include('owner.partials._kpi-cards')

    <!-- 3. Ringkasan Arus Kas: Trade-In Masuk vs Penjualan Keluar (Partial) -->
    @include('owner.partials._cashflow')

    <!-- 4. Grafik Tren Transaksi Penjualan & Laba Bersih (Partial) -->
    @include('owner.partials._sales-trend')

    <!-- 5. Analisis Produk Terlaris & Konversi Pesanan -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left 2 Columns: Top Selling iPhone Models (Partial) -->
        <div class="lg:col-span-2">
            @include('owner.partials._top-products')
        </div>

        <!-- Right 1 Column: Konversi Status & Staf -->
        <div class="space-y-6">
            <!-- Order Conversion Rate Card -->
            <x-admin.card title="Status Penyelesaian Transaksi" icon="pie-chart">
                <x-slot:actions>
                    <x-admin.status-badge tone="green" :label="($conversion['conversion_rate'] ?? 0) . '% Lunas'" :dot="false" />
                </x-slot:actions>

                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="p-3 rounded" style="background:var(--adm-paper);border:1px solid var(--adm-hairline)">
                        <span class="adm-muted block text-[11px]">Lunas / Selesai</span>
                        <span class="text-lg font-bold tabular-nums" style="color:var(--adm-moss)">{{ $conversion['realized_orders'] ?? 0 }}</span>
                    </div>
                    <div class="p-3 rounded" style="background:var(--adm-paper);border:1px solid var(--adm-hairline)">
                        <span class="adm-muted block text-[11px]">Menunggu Bayar</span>
                        <span class="text-lg font-bold tabular-nums" style="color:var(--adm-ochre)">{{ $conversion['pending'] ?? 0 }}</span>
                    </div>
                    <div class="p-3 rounded" style="background:var(--adm-paper);border:1px solid var(--adm-hairline)">
                        <span class="adm-muted block text-[11px]">Dalam Pengiriman</span>
                        <span class="text-lg font-bold tabular-nums" style="color:var(--adm-accent)">{{ $conversion['shipped'] ?? 0 }}</span>
                    </div>
                    <div class="p-3 rounded" style="background:var(--adm-paper);border:1px solid var(--adm-hairline)">
                        <span class="adm-muted block text-[11px]">Dibatalkan</span>
                        <span class="text-lg font-bold tabular-nums" style="color:var(--adm-rose)">{{ $conversion['cancelled'] ?? 0 }}</span>
                    </div>
                </div>
            </x-admin.card>

            <!-- Staff Summary Card -->
            <x-admin.card title="Manajemen Akun &amp; Staf" icon="users">
                <x-slot:actions>
                    <a href="{{ route('owner.staff.index') }}" class="text-xs font-medium hover:underline" style="color:var(--adm-accent)">
                        Kelola &rarr;
                    </a>
                </x-slot:actions>

                <div class="grid grid-cols-2 gap-3 text-xs mb-3">
                    <div class="p-3.5 rounded" style="background:var(--adm-paper);border:1px solid var(--adm-hairline)">
                        <span class="adm-muted block text-[11px]">Staf Admin</span>
                        <p class="text-xl font-bold mt-1" style="color:var(--adm-ink)">
                            {{ $totalAdmins ?? 1 }} <span class="text-xs font-normal adm-muted">Akun</span>
                        </p>
                    </div>
                    <div class="p-3.5 rounded" style="background:var(--adm-paper);border:1px solid var(--adm-hairline)">
                        <span class="adm-muted block text-[11px]">Pelanggan</span>
                        <p class="text-xl font-bold mt-1" style="color:var(--adm-ink)">
                            {{ $totalCustomers ?? 1 }} <span class="text-xs font-normal adm-muted">Akun</span>
                        </p>
                    </div>
                </div>

                <a href="{{ route('owner.staff.index') }}" class="adm-btn adm-btn-secondary w-full justify-center">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    <span>Kelola Akun Staf Admin</span>
                </a>
            </x-admin.card>
        </div>
    </div>

    <!-- 6. Transaksi Penjualan Lunas Terbaru & Audit Margin Laba -->
    <x-admin.card title="Transaksi Penjualan Lunas Terbaru &amp; Audit Margin Laba" icon="receipt"
        description="Rincian HPP dan laba bersih per nota pesanan yang telah terverifikasi lunas." flush>
        <x-slot:actions>
            <a href="{{ route('owner.financial.report') }}" class="adm-btn adm-btn-secondary" style="padding:.25rem .625rem;font-size:.75rem">
                <span>Buka Laporan &amp; Ekspor</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </x-slot:actions>

        @if($recentRealizedOrders->isEmpty())
            <div class="p-8 text-center" style="color:var(--adm-muted)">
                <p class="text-sm">Belum Ada Transaksi Lunas</p>
                <p class="text-xs mt-1">Transaksi lunas akan tampil di sini secara otomatis.</p>
            </div>
        @else
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th style="padding-left:1.5rem">No. Nota</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th style="text-align:center">Status</th>
                            <th class="adm-num">Omzet (Grand Total)</th>
                            <th class="adm-num">Total HPP</th>
                            <th class="adm-num" style="padding-right:1.5rem">Laba Bersih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentRealizedOrders as $order)
                            <tr>
                                <td style="padding-left:1.5rem">
                                    <a href="{{ route('admin.orders.show', $order->order_number) }}" class="adm-mono font-medium hover:underline" style="color:var(--adm-accent)">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="adm-cell-sub">{{ $order->created_at?->format('d M Y, H:i') }}</td>
                                <td class="adm-cell-title">{{ $order->recipient_name ?: ($order->user?->name ?? 'Pelanggan') }}</td>
                                <td style="text-align:center"><x-order-badge :status="$order->status" /></td>
                                <td class="adm-num font-medium tabular-nums" style="color:var(--adm-ink)">
                                    Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}
                                </td>
                                <td class="adm-num tabular-nums" style="color:var(--adm-ochre)">
                                    Rp {{ number_format((float) $order->calculated_cogs, 0, ',', '.') }}
                                </td>
                                <td class="adm-num font-medium tabular-nums" style="color:var(--adm-moss);padding-right:1.5rem">
                                    + Rp {{ number_format((float) $order->calculated_net_profit, 0, ',', '.') }}
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
                    readout.innerHTML = `<strong style="color:var(--adm-ink)">${date}</strong> &mdash; Omzet: <strong style="color:var(--adm-accent)">Rp ${rev}</strong> | Laba Bersih: <strong style="color:var(--adm-moss)">Rp ${profit}</strong> (${orders} pesanan / ${units} unit)`;
                }
            });
        });
    });
</script>
@endpush
