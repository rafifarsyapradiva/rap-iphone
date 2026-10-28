@extends('layouts.admin')

@section('page_title', 'Ringkasan Operasional')

@section('content')
<div class="space-y-6">

    <!-- Top Greeting Banner Frosted Glass -->
    <div class="relative overflow-hidden rounded-3xl p-6 sm:p-8 glass-card-elevated border border-white/15">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-[#0071E3]/20 rounded-full blur-[90px] pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#0071E3]/20 border border-[#0071E3]/30 text-[#5AC8FA] text-xs font-semibold mb-3">
                    <span class="w-2 h-2 rounded-full bg-[#34C759] animate-pulse"></span>
                    <span>Role Terverifikasi: {{ strtoupper(Auth::user()->role) }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">
                    Halo, {{ Auth::user()->name }}!
                </h1>
                <p class="text-xs sm:text-sm text-[#8E8E93] mt-1 max-w-xl">
                    Selamat datang di Panel Manajemen Operasional Rap-iPhone. Anda memiliki akses penuh ke katalog iPhone, manajemen resi kurir, dan verifikasi trade-in.
                </p>
            </div>
            
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('admin.orders.index') }}" class="glass-btn glass-btn-secondary glass-btn-sm flex items-center gap-2">
                    <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                    <span>Kelola &amp; Cetak Pesanan</span>
                </a>
                <a href="{{ url('/') }}" target="_blank" class="glass-btn glass-btn-secondary glass-btn-sm flex items-center gap-2">
                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    <span>Lihat Toko</span>
                </a>
                @if(Auth::user()->isOwner())
                    <a href="{{ route('owner.dashboard') }}" class="glass-btn glass-btn-primary glass-btn-sm flex items-center gap-2">
                        <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
                        <span>Eksekutif Finansial</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        
        <!-- Total Products Card -->
        <div class="glass-card rounded-2xl p-5 border border-white/10 hover:border-white/20 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-[#8E8E93] uppercase tracking-wider">Katalog Produk</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $totalProducts ?? 0 }} <span class="text-xs font-normal text-[#8E8E93]">Model</span></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-[#0071E3]/15 border border-[#0071E3]/30 flex items-center justify-center text-[#0071E3]">
                    <i data-lucide="smartphone" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs">
                <span class="text-[#34C759] flex items-center gap-1">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                    Semua unit aktif
                </span>
                <span class="text-[#8E8E93]">Garansi Resmi / Second</span>
            </div>
        </div>

        <!-- Total Orders Card -->
        <div class="glass-card rounded-2xl p-5 border border-white/10 hover:border-white/20 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-[#8E8E93] uppercase tracking-wider">Pesanan Masuk</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $totalOrders ?? 0 }} <span class="text-xs font-normal text-[#8E8E93]">Transaksi</span></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-[#34C759]/15 border border-[#34C759]/30 flex items-center justify-center text-[#34C759]">
                    <i data-lucide="shopping-bag" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs">
                <span class="text-[#5AC8FA]">Midtrans Gateway</span>
                <span class="text-[#8E8E93]">Real-time Sync</span>
            </div>
        </div>

        <!-- Pending Trade-In Card -->
        <div class="glass-card rounded-2xl p-5 border border-white/10 hover:border-white/20 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-[#8E8E93] uppercase tracking-wider">Antrean Trade-In</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ $pendingTradeIns ?? 0 }} <span class="text-xs font-normal text-[#8E8E93]">Pengajuan</span></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-[#FF9F0A]/15 border border-[#FF9F0A]/30 flex items-center justify-center text-[#FF9F0A]">
                    <i data-lucide="refresh-cw" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs">
                <span class="text-[#FF9F0A]">Perlu Inspeksi Fisik</span>
                <span class="text-[#8E8E93]">Tukar Tambah</span>
            </div>
        </div>

    </div>

    <!-- System Status & Security Badge -->
    <div class="glass-card rounded-2xl p-6 border border-white/10">
        <h2 class="text-sm font-bold text-white uppercase tracking-wider mb-4 flex items-center gap-2">
            <i data-lucide="shield-check" class="w-4 h-4 text-[#34C759]"></i>
            <span>Status Keamanan & Hak Akses (Role Guards Active)</span>
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <div class="p-3.5 rounded-xl bg-white/5 border border-white/10">
                <span class="text-[#8E8E93] block mb-1">Pengguna Terautentikasi:</span>
                <span class="font-semibold text-white">{{ Auth::user()->name }} ({{ Auth::user()->email }})</span>
            </div>
            <div class="p-3.5 rounded-xl bg-white/5 border border-white/10">
                <span class="text-[#8E8E93] block mb-1">Tingkat Hak Akses:</span>
                <span class="font-semibold text-[#5AC8FA]">Role: {{ Auth::user()->role }}</span>
            </div>
            <div class="p-3.5 rounded-xl bg-white/5 border border-white/10">
                <span class="text-[#8E8E93] block mb-1">Session Security:</span>
                <span class="font-semibold text-[#34C759]">Regenerated & Bcrypt Hashed</span>
            </div>
        </div>
    </div>

</div>
@endsection
