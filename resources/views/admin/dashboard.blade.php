@extends('layouts.admin')

@section('page_title', 'Ringkasan Operasional')

@section('content')
<div class="space-y-6">

    <!-- Top Greeting Banner Daylight Enterprise -->
    <div class="relative overflow-hidden rounded-3xl p-6 sm:p-8 bg-white border border-[#E5E5EA] shadow-sm">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-[#0066CC] text-xs font-semibold mb-3">
                    <span class="w-2 h-2 rounded-full bg-[#008009]"></span>
                    <span>Role Terverifikasi: {{ strtoupper(Auth::user()->role) }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-[#1D1D1F] tracking-tight">
                    Halo, {{ Auth::user()->name }}!
                </h1>
                <p class="text-xs sm:text-sm text-[#6E6E73] mt-1 max-w-xl">
                    Selamat datang di Panel Manajemen Operasional Rap-iPhone. Anda memiliki akses penuh ke katalog iPhone, manajemen resi kurir, dan verifikasi trade-in.
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <a href="{{ route('admin.products.index') }}" class="glass-btn glass-btn-secondary glass-btn-sm flex items-center gap-2">
                    <i data-lucide="smartphone" class="w-3.5 h-3.5 text-[#0066CC]"></i>
                    <span>Kelola Katalog &amp; Stok</span>
                </a>
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
        <div class="bg-white rounded-2xl p-5 border border-[#E5E5EA] hover:border-[#D2D2D7] shadow-sm transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-[#6E6E73] uppercase tracking-wider">Katalog Produk</p>
                    <p class="text-2xl font-bold text-[#1D1D1F] mt-1">{{ $totalProducts ?? 0 }} <span class="text-xs font-normal text-[#6E6E73]">Model</span></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center text-[#0071E3]">
                    <i data-lucide="smartphone" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-[#E5E5EA] flex items-center justify-between text-xs">
                <span class="text-[#008009] font-medium flex items-center gap-1">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                    Semua unit aktif
                </span>
                <span class="text-[#6E6E73]">Garansi Resmi / Second</span>
            </div>
        </div>

        <!-- Total Orders Card -->
        <div class="bg-white rounded-2xl p-5 border border-[#E5E5EA] hover:border-[#D2D2D7] shadow-sm transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-[#6E6E73] uppercase tracking-wider">Pesanan Masuk</p>
                    <p class="text-2xl font-bold text-[#1D1D1F] mt-1">{{ $totalOrders ?? 0 }} <span class="text-xs font-normal text-[#6E6E73]">Transaksi</span></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-[#008009]">
                    <i data-lucide="shopping-bag" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-[#E5E5EA] flex items-center justify-between text-xs">
                <span class="text-[#0066CC] font-medium">Midtrans Gateway</span>
                <span class="text-[#6E6E73]">Real-time Sync</span>
            </div>
        </div>

        <!-- Pending Trade-In Card -->
        <div class="bg-white rounded-2xl p-5 border border-[#E5E5EA] hover:border-[#D2D2D7] shadow-sm transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-[#6E6E73] uppercase tracking-wider">Antrean Trade-In</p>
                    <p class="text-2xl font-bold text-[#1D1D1F] mt-1">{{ $pendingTradeIns ?? 0 }} <span class="text-xs font-normal text-[#6E6E73]">Pengajuan</span></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-[#BF4800]">
                    <i data-lucide="refresh-cw" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-[#E5E5EA] flex items-center justify-between text-xs">
                <span class="text-[#BF4800] font-medium">Perlu Inspeksi Fisik</span>
                <span class="text-[#6E6E73]">Tukar Tambah</span>
            </div>
        </div>

    </div>

    <!-- System Status & Security Badge -->
    <div class="bg-white rounded-2xl p-6 border border-[#E5E5EA] shadow-sm">
        <h2 class="text-sm font-bold text-[#1D1D1F] uppercase tracking-wider mb-4 flex items-center gap-2">
            <i data-lucide="shield-check" class="w-4 h-4 text-[#008009]"></i>
            <span>Status Keamanan & Hak Akses (Role Guards Active)</span>
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <div class="p-3.5 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA]">
                <span class="text-[#6E6E73] block mb-1">Pengguna Terautentikasi:</span>
                <span class="font-semibold text-[#1D1D1F]">{{ Auth::user()->name }} ({{ Auth::user()->email }})</span>
            </div>
            <div class="p-3.5 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA]">
                <span class="text-[#6E6E73] block mb-1">Tingkat Hak Akses:</span>
                <span class="font-semibold text-[#0066CC]">Role: {{ Auth::user()->role }}</span>
            </div>
            <div class="p-3.5 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA]">
                <span class="text-[#6E6E73] block mb-1">Session Security:</span>
                <span class="font-semibold text-[#008009]">Regenerated & Bcrypt Hashed</span>
            </div>
        </div>
    </div>

</div>
@endsection
