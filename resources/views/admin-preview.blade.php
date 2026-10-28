@extends('layouts.admin')

@section('title', 'Admin Hub Preview — Rap-iPhone Operations')
@section('page_title', 'Ringkasan Dashboard')

@section('content')
<div class="space-y-8">
    
    <!-- Top Welcome Banner -->
    <div class="glass-card p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-white/[0.07] via-white/[0.04] to-transparent border border-white/10 relative overflow-hidden">
        <div class="relative z-10 max-w-2xl space-y-2">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-[#0071E3]/20 text-[#5AC8FA] border border-[#0071E3]/30">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                <span>Preview Master Layout Admin (&lt;layouts.admin&gt;)</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                Selamat Datang di Portal Operasional Rap-iPhone
            </h1>
            <p class="text-sm text-[#8E8E93]">
                Kelola inventaris iPhone, verifikasi transaksi pembayaran Midtrans Snap, pantau antrean tukar tambah, dan unduh faktur nota PDF resmi.
            </p>
        </div>
        <div class="absolute right-6 -bottom-6 opacity-10 hidden sm:block">
            <i data-lucide="smartphone" class="w-48 h-48 text-white"></i>
        </div>
    </div>

    <!-- Metrik Frosted Glass Cards (Key Performance Indicators) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Metric 1: Total Omzet -->
        <x-glass-card class="!p-5 space-y-3">
            <div class="flex items-center justify-between text-[#8E8E93]">
                <span class="text-xs font-semibold uppercase tracking-wider">Omzet Bulan Ini</span>
                <div class="w-8 h-8 rounded-xl bg-[#0071E3]/15 text-[#0071E3] flex items-center justify-center">
                    <i data-lucide="dollar-sign" class="w-4 h-4"></i>
                </div>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-white tracking-tight">Rp 184.500.000</p>
                <p class="text-xs text-[#34C759] flex items-center gap-1 mt-1 font-semibold">
                    <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
                    +18.4% dari bulan lalu
                </p>
            </div>
        </x-glass-card>

        <!-- Metric 2: Pesanan Baru -->
        <x-glass-card class="!p-5 space-y-3">
            <div class="flex items-center justify-between text-[#8E8E93]">
                <span class="text-xs font-semibold uppercase tracking-wider">Perlu Dikirim</span>
                <div class="w-8 h-8 rounded-xl bg-[#FF9F0A]/15 text-[#FF9F0A] flex items-center justify-center">
                    <i data-lucide="package" class="w-4 h-4"></i>
                </div>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-white tracking-tight">12 Pesanan</p>
                <p class="text-xs text-[#FF9F0A] flex items-center gap-1 mt-1 font-semibold">
                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                    Alokasikan IMEI unit
                </p>
            </div>
        </x-glass-card>

        <!-- Metric 3: Tukar Tambah Pending -->
        <x-glass-card class="!p-5 space-y-3">
            <div class="flex items-center justify-between text-[#8E8E93]">
                <span class="text-xs font-semibold uppercase tracking-wider">Antrean Trade-In</span>
                <div class="w-8 h-8 rounded-xl bg-[#34C759]/15 text-[#34C759] flex items-center justify-center">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                </div>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-white tracking-tight">5 Unit Masuk</p>
                <p class="text-xs text-[#34C759] flex items-center gap-1 mt-1 font-semibold">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                    Siap diinspeksi teknisi
                </p>
            </div>
        </x-glass-card>

        <!-- Metric 4: Sisa Stok iPhone -->
        <x-glass-card class="!p-5 space-y-3">
            <div class="flex items-center justify-between text-[#8E8E93]">
                <span class="text-xs font-semibold uppercase tracking-wider">Total Stok Varian</span>
                <div class="w-8 h-8 rounded-xl bg-[#AF52DE]/15 text-[#AF52DE] flex items-center justify-center">
                    <i data-lucide="layers" class="w-4 h-4"></i>
                </div>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-white tracking-tight">41 Varian</p>
                <p class="text-xs text-[#5AC8FA] flex items-center gap-1 mt-1 font-semibold">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                    6 Model Terdaftar
                </p>
            </div>
        </x-glass-card>
    </div>

    <!-- Sample Table: Antrean Pesanan dengan Order Badges -->
    <x-glass-card class="space-y-4">
        <div class="flex items-center justify-between border-b border-white/10 pb-4">
            <div>
                <h3 class="text-base font-bold text-white">Transaksi Pesanan Terbaru</h3>
                <p class="text-xs text-[#8E8E93]">Integrasi status pesanan dengan komponen &lt;x-order-badge&gt;</p>
            </div>
            <x-glass-button href="{{ url('/design-system') }}" variant="secondary" size="sm" icon="arrow-left">
                Kembali ke Showcase
            </x-glass-button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-white/10 text-[#8E8E93] uppercase tracking-wider font-semibold">
                        <th class="py-3 px-4">No. Pesanan</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Unit iPhone</th>
                        <th class="py-3 px-4">Total Tagihan</th>
                        <th class="py-3 px-4">Status Transaksi</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-[#E5E5EA]">
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="py-3.5 px-4 font-mono font-bold text-white">RAP-20261028-001</td>
                        <td class="py-3.5 px-4">Budi Pratama (0812998877)</td>
                        <td class="py-3.5 px-4">iPhone 16 Pro Max 256GB - Desert Titanium</td>
                        <td class="py-3.5 px-4 font-bold text-white">Rp 24.999.000</td>
                        <td class="py-3.5 px-4"><x-order-badge status="paid" /></td>
                        <td class="py-3.5 px-4 text-right">
                            <x-glass-button variant="primary" size="sm">Input Resi</x-glass-button>
                        </td>
                    </tr>
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="py-3.5 px-4 font-mono font-bold text-white">RAP-20261028-002</td>
                        <td class="py-3.5 px-4">Siti Nurhaliza (0813887766)</td>
                        <td class="py-3.5 px-4">iPhone 15 Pro 128GB - Natural Titanium (Second)</td>
                        <td class="py-3.5 px-4 font-bold text-white">Rp 15.800.000</td>
                        <td class="py-3.5 px-4"><x-order-badge status="processing" /></td>
                        <td class="py-3.5 px-4 text-right">
                            <x-glass-button variant="secondary" size="sm">Detail</x-glass-button>
                        </td>
                    </tr>
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="py-3.5 px-4 font-mono font-bold text-white">RAP-20261028-003</td>
                        <td class="py-3.5 px-4">Dimas Arya (0818776655)</td>
                        <td class="py-3.5 px-4">iPhone 14 128GB - Blue</td>
                        <td class="py-3.5 px-4 font-bold text-white">Rp 11.499.000</td>
                        <td class="py-3.5 px-4"><x-order-badge status="pending" /></td>
                        <td class="py-3.5 px-4 text-right">
                            <x-glass-button variant="secondary" size="sm">Hubungi WA</x-glass-button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-glass-card>

</div>
@endsection
