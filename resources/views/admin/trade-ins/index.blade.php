@extends('layouts.admin')

@section('title', 'Antrean & Inspeksi Tukar Tambah (Trade-In) — Rap-iPhone Admin')
@section('page_title', 'Antrean Tukar Tambah')

@section('content')
<x-admin.page-header
    meta="Modul Verifikasi & Inspeksi Teknisi"
    icon="refresh-cw"
    title="Antrean Pengajuan Trade-In"
    subtitle="Buku besar inspeksi tukar tambah iPhone pelanggan: verifikasi fisik teknisi, evaluasi kesehatan baterai, dan penetapan nilai deal final.">
    <x-slot:actions>
        <a href="{{ route('trade-in.index') }}" target="_blank" rel="noopener" class="adm-btn adm-btn-sm">
            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
            <span>Buka Wizard Pelanggan</span>
        </a>
    </x-slot:actions>
</x-admin.page-header>

<!-- 4 Metrik Status Trade-In Statement Figures -->
<div class="adm-stat-grid cols-4">
    <x-admin.stat-card
        label="Menunggu unit fisik"
        :value="$metrics['submitted'] ?? 0"
        icon="clock"
        tone="amber"
        hint="Pengajuan baru dari form online"
        :href="route('admin.trade-ins.index', ['status' => 'submitted', 'q' => $search])"
        :active="$statusFilter === 'submitted'" />

    <x-admin.stat-card
        label="Dalam tahap inspeksi"
        :value="$metrics['inspecting'] ?? 0"
        icon="search-check"
        tone="blue"
        hint="Unit diterima & diuji teknisi toko"
        :href="route('admin.trade-ins.index', ['status' => 'unit_received', 'q' => $search])"
        :active="in_array($statusFilter, ['unit_received', 'inspected'])" />

    <x-admin.stat-card
        label="Disetujui & dicairkan"
        :value="$metrics['approved'] ?? 0"
        icon="check-circle-2"
        tone="green"
        hint="Penawaran final disetujui / selesai"
        :href="route('admin.trade-ins.index', ['status' => 'approved', 'q' => $search])"
        :active="in_array($statusFilter, ['approved', 'paid_out'])" />

    <x-admin.stat-card
        label="Pengajuan ditolak"
        :value="$metrics['rejected'] ?? 0"
        icon="x-circle"
        tone="red"
        hint="Tidak lolos verifikasi fisik / dibatalkan"
        :href="route('admin.trade-ins.index', ['status' => 'rejected', 'q' => $search])"
        :active="$statusFilter === 'rejected'" />
</div>

<x-admin.card flush>
    <!-- Filter Tabs & Search Toolbar -->
    <div class="adm-card-body" style="padding-bottom: 1rem;">
        @php
            $statusTabs = [
                'all' => 'Semua (' . ($metrics['total'] ?? 0) . ')',
                'submitted' => 'Submitted',
                'unit_received' => 'Diterima Toko',
                'inspected' => 'Diinspeksi',
                'approved' => 'Approved',
                'paid_out' => 'Paid Out',
                'rejected' => 'Rejected',
            ];
        @endphp

        <div class="adm-toolbar">
            <div class="adm-tabs">
                @foreach($statusTabs as $tabKey => $tabLabel)
                    <a href="{{ route('admin.trade-ins.index', array_filter(['status' => $tabKey !== 'all' ? $tabKey : null, 'q' => $search])) }}"
                       class="adm-tab {{ $statusFilter === $tabKey ? 'is-active' : '' }}">
                        {{ $tabLabel }}
                    </a>
                @endforeach
            </div>

            <form action="{{ route('admin.trade-ins.index') }}" method="GET" class="adm-filters">
                @if($statusFilter !== 'all')
                    <input type="hidden" name="status" value="{{ $statusFilter }}">
                @endif
                <div class="adm-search">
                    <i data-lucide="search"></i>
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari kode TI, model, IMEI, customer..." class="adm-input" aria-label="Cari pengajuan trade-in">
                </div>
                <button type="submit" class="adm-btn adm-btn-sm">Cari</button>
                @if($search !== '' || $statusFilter !== 'all')
                    <a href="{{ route('admin.trade-ins.index') }}" class="adm-btn adm-btn-sm text-[var(--adm-tone-red-ink)]">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Trade-In Queue Table (Master Trade Ledger) -->
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th scope="col" style="padding-left:1.25rem">Kode &amp; Waktu</th>
                    <th scope="col">Pelanggan</th>
                    <th scope="col">Unit &amp; Kondisi</th>
                    <th scope="col">Metode Pencairan</th>
                    <th scope="col" class="adm-num">Estimasi vs Deal</th>
                    <th scope="col" style="text-align:center">Status</th>
                    <th scope="col" class="adm-num" style="padding-right:1.25rem">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tradeIns as $tradeIn)
                    <tr>
                        <td style="padding-left:1.25rem">
                            <a href="{{ route('admin.trade-ins.show', $tradeIn->trade_in_code) }}" class="adm-mono font-medium hover:underline block">
                                {{ $tradeIn->trade_in_code }}
                            </a>
                            <span class="adm-cell-sub">{{ $tradeIn->created_at->format('d M Y, H:i') }}</span>
                        </td>
                        <td>
                            <p class="adm-cell-title">{{ $tradeIn->user->name ?? 'Customer' }}</p>
                            <p class="adm-cell-sub adm-mono">{{ $tradeIn->user->phone_number ?? '-' }}</p>
                            <p class="adm-cell-sub">{{ $tradeIn->user->email ?? '-' }}</p>
                        </td>
                        <td>
                            <p class="adm-cell-title font-medium">{{ $tradeIn->phone_model }} &middot; {{ $tradeIn->storage_capacity }}</p>
                            <div class="flex items-center gap-1.5 mt-1">
                                <span class="adm-badge adm-tone-gray" style="padding:.0625rem .375rem">BH: {{ $tradeIn->battery_health }}%</span>
                                <span class="adm-cell-sub">{{ $tradeIn->physical_condition }}</span>
                            </div>
                            @if($tradeIn->imei)
                                <p class="adm-cell-sub adm-mono text-[var(--adm-ink-muted)] mt-0.5">IMEI: {{ $tradeIn->imei }}</p>
                            @endif
                        </td>
                        <td>
                            @if($tradeIn->isVoucherPayout())
                                <x-admin.status-badge tone="green" label="Kupon Belanja" :dot="false" />
                            @else
                                <x-admin.status-badge tone="blue" :label="'Transfer ' . ($tradeIn->customer_bank_name ?: 'Bank')" :dot="false" />
                                @if($tradeIn->customer_account_number)
                                    <p class="adm-cell-sub adm-mono mt-0.5">{{ $tradeIn->customer_account_number }}</p>
                                @endif
                            @endif
                        </td>
                        <td class="adm-num">
                            <span class="adm-cell-sub block">Est: Rp {{ number_format((float) $tradeIn->estimated_price, 0, ',', '.') }}</span>
                            @if($tradeIn->final_price)
                                <span class="adm-cell-title text-[var(--adm-tone-green-ink)]">Deal: Rp {{ number_format((float) $tradeIn->final_price, 0, ',', '.') }}</span>
                            @else
                                <span class="adm-cell-sub text-[var(--adm-ink-faint)]">Belum diinspeksi</span>
                            @endif
                        </td>
                        <td style="text-align:center">
                            <x-admin.status-badge :status="$tradeIn->status" />
                        </td>
                        <td class="adm-num" style="padding-right:1.25rem">
                            <a href="{{ route('admin.trade-ins.show', $tradeIn->trade_in_code) }}"
                               class="adm-btn adm-btn-sm text-xs inline-flex items-center gap-1.5"
                               title="Inspeksi & Tetapkan Harga">
                                <i data-lucide="wrench" class="w-3.5 h-3.5"></i>
                                <span>Inspeksi</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <x-admin.empty-state icon="refresh-cw" title="Tidak ada antrean trade-in" text="Ubah filter status atau sesuaikan kata kunci pencarian pada toolbar." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tradeIns->hasPages())
        <div class="adm-pagination">
            {{ $tradeIns->links() }}
        </div>
    @endif
</x-admin.card>
@endsection
