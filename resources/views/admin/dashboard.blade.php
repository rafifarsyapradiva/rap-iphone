@extends('layouts.admin')

@section('title', 'Dashboard Operasional — Rap-iPhone Admin')
@section('page_title', 'Ringkasan Operasional')

@section('content')
@php
    $user = Auth::user();
@endphp

<x-admin.page-header
    :eyebrow="now()->translatedFormat('l, d F Y')"
    icon="calendar"
    :title="'Halo, ' . $user->name"
    subtitle="Ringkasan operasional toko hari ini. Mulai dari item yang perlu tindakan Anda.">
    <x-slot:actions>
        <a href="{{ route('admin.products.create') }}" class="glass-btn glass-btn-primary glass-btn-sm">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i><span>Produk Baru</span>
        </a>
        <a href="{{ route('admin.orders.index') }}" class="glass-btn glass-btn-secondary glass-btn-sm">
            <i data-lucide="printer" class="w-3.5 h-3.5"></i><span>Kelola &amp; Cetak Pesanan</span>
        </a>
        <a href="{{ url('/') }}" target="_blank" rel="noopener" class="glass-btn glass-btn-secondary glass-btn-sm">
            <i data-lucide="external-link" class="w-3.5 h-3.5"></i><span>Lihat Toko</span>
        </a>
        @if($user->isOwner())
            <a href="{{ route('owner.dashboard') }}" class="glass-btn glass-btn-dark glass-btn-sm">
                <i data-lucide="trending-up" class="w-3.5 h-3.5"></i><span>Eksekutif Finansial</span>
            </a>
        @endif
    </x-slot:actions>
</x-admin.page-header>

{{-- Kartu Metrik --}}
<div class="adm-stat-grid cols-4">
    <x-admin.stat-card
        label="Pesanan Perlu Diproses"
        :value="$pendingOrders ?? 0"
        icon="package-check"
        tone="green"
        :href="route('admin.orders.index', ['status' => 'paid'])"
        hint="Sudah lunas, menunggu alokasi IMEI & resi" />

    <x-admin.stat-card
        label="Trade-In Menunggu Inspeksi"
        :value="$pendingTradeIns ?? 0"
        icon="refresh-cw"
        tone="amber"
        :href="route('admin.trade-ins.index')"
        hint="Pengajuan baru dari pelanggan" />

    <x-admin.stat-card
        label="Stok Menipis (≤ 3)"
        :value="$lowStockVariants ?? 0"
        icon="alert-triangle"
        :tone="($lowStockVariants ?? 0) > 0 ? 'red' : 'gray'"
        :href="route('admin.products.index')"
        hint="Varian yang perlu restock" />

    <x-admin.stat-card
        label="Total Produk"
        :value="$totalProducts ?? 0"
        icon="smartphone"
        tone="blue"
        :href="route('admin.products.index')"
        :hint="($totalOrders ?? 0) . ' transaksi tercatat'" />
</div>

{{-- Perlu Tindakan --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

    <x-admin.card title="Pesanan Terbaru" icon="shopping-bag" description="5 transaksi terakhir yang masuk." flush>
        <x-slot:actions>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-[#0066CC] hover:underline">Lihat semua</a>
        </x-slot:actions>

        @if(($recentOrders ?? collect())->isEmpty())
            <x-admin.empty-state icon="shopping-bag" title="Belum ada pesanan" text="Pesanan pelanggan akan tampil di sini setelah checkout." />
        @else
            <div class="adm-list">
                @foreach($recentOrders as $order)
                    <a href="{{ route('admin.orders.show', $order) }}" class="adm-list-item">
                        <div class="min-w-0">
                            <p class="adm-cell-title adm-mono">{{ $order->order_number }}</p>
                            <p class="adm-cell-sub truncate">{{ $order->recipient_name ?: ($order->user->name ?? '-') }} &middot; {{ $order->created_at?->diffForHumans() }}</p>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <span class="adm-num adm-cell-title hidden sm:inline">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                            <x-admin.status-badge :status="$order->status" />
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </x-admin.card>

    <x-admin.card title="Trade-In Menunggu Inspeksi" icon="refresh-cw" description="Pengajuan berstatus Diajukan." flush>
        <x-slot:actions>
            <a href="{{ route('admin.trade-ins.index') }}" class="text-xs font-semibold text-[#0066CC] hover:underline">Lihat semua</a>
        </x-slot:actions>

        @if(($recentTradeIns ?? collect())->isEmpty())
            <x-admin.empty-state icon="check-circle-2" title="Antrean bersih" text="Tidak ada pengajuan tukar tambah yang menunggu inspeksi." />
        @else
            <div class="adm-list">
                @foreach($recentTradeIns as $tradeIn)
                    <a href="{{ route('admin.trade-ins.show', $tradeIn) }}" class="adm-list-item">
                        <div class="min-w-0">
                            <p class="adm-cell-title truncate">{{ $tradeIn->phone_model }} <span class="adm-muted font-normal">{{ $tradeIn->storage_capacity }}</span></p>
                            <p class="adm-cell-sub truncate"><span class="adm-mono">{{ $tradeIn->trade_in_code }}</span> &middot; {{ $tradeIn->user->name ?? '-' }}</p>
                        </div>
                        <span class="adm-num adm-cell-title shrink-0">Rp {{ number_format($tradeIn->estimated_price, 0, ',', '.') }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </x-admin.card>
</div>

{{-- Sesi --}}
<x-admin.card title="Sesi Anda" icon="user-check">
    <dl class="adm-kv adm-kv-inline">
        <div><dt>Pengguna</dt><dd>{{ $user->name }}</dd></div>
        <div><dt>Email</dt><dd>{{ $user->email }}</dd></div>
        <div><dt>Hak Akses</dt><dd><x-admin.status-badge :status="$user->role" /></dd></div>
    </dl>
</x-admin.card>
@endsection
