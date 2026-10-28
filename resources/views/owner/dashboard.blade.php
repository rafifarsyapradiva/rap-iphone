@extends('layouts.admin')

@section('page_title', 'Eksekutif Finansial & Omzet (Owner Only)')

@section('content')
<div class="space-y-6">

    <!-- Owner Executive Banner -->
    <div class="relative overflow-hidden rounded-3xl p-6 sm:p-8 glass-card-elevated border border-[#0071E3]/30">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-[#0071E3]/25 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#34C759]/20 border border-[#34C759]/40 text-[#34C759] text-xs font-semibold mb-3">
                    <i data-lucide="crown" class="w-3.5 h-3.5"></i>
                    <span>Tingkat Akses Tertinggi: Pemilik Usaha (Owner)</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">
                    Portal Finansial & Analitik Laba
                </h1>
                <p class="text-xs sm:text-sm text-[#8E8E93] mt-1 max-w-xl">
                    Halaman khusus Pemilik Usaha untuk memantau omzet transaksi kotor, margin laba bersih iPhone, dan ringkasan arus kas toko secara real-time.
                </p>
            </div>
            
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="glass-btn glass-btn-secondary glass-btn-sm flex items-center gap-2">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Kembali ke Operasional</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Financial Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        
        <!-- Total Omzet -->
        <div class="glass-card rounded-2xl p-6 border border-white/10">
            <span class="text-xs font-semibold text-[#8E8E93] uppercase tracking-wider block">Total Omzet Penjualan (Lunas)</span>
            <p class="text-3xl font-bold text-white mt-2">
                Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}
            </p>
            <p class="text-xs text-[#34C759] mt-2 flex items-center gap-1">
                <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
                <span>Transaksi Midtrans Terverifikasi</span>
            </p>
        </div>

        <!-- Total Admins -->
        <div class="glass-card rounded-2xl p-6 border border-white/10">
            <span class="text-xs font-semibold text-[#8E8E93] uppercase tracking-wider block">Staf Admin Operasional</span>
            <p class="text-3xl font-bold text-white mt-2">
                {{ $totalAdmins ?? 1 }} <span class="text-sm font-normal text-[#8E8E93]">Akun Aktif</span>
            </p>
            <p class="text-xs text-[#5AC8FA] mt-2 flex items-center gap-1">
                <i data-lucide="users" class="w-3.5 h-3.5"></i>
                <span>Role: admin terdaftar</span>
            </p>
        </div>

        <!-- Total Customers -->
        <div class="glass-card rounded-2xl p-6 border border-white/10">
            <span class="text-xs font-semibold text-[#8E8E93] uppercase tracking-wider block">Pelanggan Terdaftar</span>
            <p class="text-3xl font-bold text-white mt-2">
                {{ $totalCustomers ?? 1 }} <span class="text-sm font-normal text-[#8E8E93]">Pelanggan</span>
            </p>
            <p class="text-xs text-[#FF9F0A] mt-2 flex items-center gap-1">
                <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
                <span>Email & Google OAuth</span>
            </p>
        </div>

    </div>

</div>
@endsection
