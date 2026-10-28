@extends('layouts.customer')

@section('title', 'Design System & Template Showroom — Rap-iPhone')
@section('meta_description', 'Spesifikasi komponen Blade Glassmorphism dan panduan desain sistem retail Apple untuk Rap-iPhone.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-16">

    <!-- Hero Showcase Header -->
    <div class="text-center space-y-4 max-w-3xl mx-auto">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/5 border border-white/10 text-xs font-semibold text-[#5AC8FA] backdrop-blur-md">
            <span class="w-2 h-2 rounded-full bg-[#0071E3] animate-ping"></span>
            <span>Fase 3: Desain Sistem & Template Blade Glassmorphism</span>
        </div>
        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white">
            Apple Retail Elegance. <br class="hidden sm:inline">
            <span class="bg-gradient-to-r from-white via-[#E5E5EA] to-[#0071E3] bg-clip-text text-transparent">
                Frosted Glass Experience.
            </span>
        </h1>
        <p class="text-sm sm:text-base text-[#8E8E93] leading-relaxed">
            Koleksi komponen UI reusable dan template Blade dengan standar retail resmi Apple (iBox & Digimap). Menampilkan kedalaman ruang 3D, filter blur 25px, aksen titanium, dan efek pencahayaan dinamis.
        </p>

        <!-- Fast Navigation Links -->
        <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
            <x-glass-button href="#buttons" variant="secondary" size="sm" icon="mouse-pointer-click">
                Buttons
            </x-glass-button>
            <x-glass-button href="#badges" variant="secondary" size="sm" icon="badge-check">
                Order Badges
            </x-glass-button>
            <x-glass-button href="#battery" variant="secondary" size="sm" icon="battery-charging">
                Battery Meter
            </x-glass-button>
            <x-glass-button href="#products" variant="secondary" size="sm" icon="smartphone">
                Product Cards
            </x-glass-button>
            <x-glass-button href="{{ url('/admin-preview') }}" variant="primary" size="sm" icon="shield">
                Preview Admin Layout
            </x-glass-button>
        </div>
    </div>

    <!-- Section 1: Glass Buttons Gallery -->
    <section id="buttons" class="space-y-6">
        <div class="flex items-center justify-between border-b border-white/10 pb-4">
            <div>
                <h2 class="text-xl font-bold text-white flex items-center gap-2.5">
                    <i data-lucide="mouse-pointer-click" class="w-5 h-5 text-[#0071E3]"></i>
                    <span>1. Komponen Tombol Kaca (&lt;x-glass-button&gt;)</span>
                </h2>
                <p class="text-xs text-[#8E8E93] mt-1">4 varian estetika (Primary Blue, Secondary Outline, Danger Crimson, Ghost) dan 3 skala ukuran.</p>
            </div>
            <span class="text-xs font-mono text-[#8E8E93]">components/glass-button.blade.php</span>
        </div>

        <x-glass-card class="space-y-6">
            <!-- Varian Buttons -->
            <div>
                <p class="text-xs font-semibold text-[#8E8E93] uppercase tracking-wider mb-3">Varian Tombol</p>
                <div class="flex flex-wrap items-center gap-4">
                    <x-glass-button variant="primary" icon="shopping-bag">
                        Beli Sekarang (Primary)
                    </x-glass-button>

                    <x-glass-button variant="secondary" icon="refresh-cw">
                        Tukar Tambah Unit (Secondary)
                    </x-glass-button>

                    <x-glass-button variant="danger" icon="trash-2">
                        Batalkan Pesanan (Danger)
                    </x-glass-button>

                    <x-glass-button variant="ghost" icon="info">
                        Pelajari Selengkapnya (Ghost)
                    </x-glass-button>
                </div>
            </div>

            <!-- Ukuran Buttons -->
            <div class="pt-4 border-t border-white/10">
                <p class="text-xs font-semibold text-[#8E8E93] uppercase tracking-wider mb-3">Skala Ukuran (sm, md, lg)</p>
                <div class="flex flex-wrap items-center gap-4">
                    <x-glass-button variant="primary" size="sm" icon="check">
                        Ukuran Kecil (sm)
                    </x-glass-button>

                    <x-glass-button variant="primary" size="md" icon="check">
                        Ukuran Standar (md)
                    </x-glass-button>

                    <x-glass-button variant="primary" size="lg" icon="sparkles">
                        Ukuran Besar CTA (lg)
                    </x-glass-button>
                </div>
            </div>
        </x-glass-card>
    </section>

    <!-- Section 2: Order Badges Matrix -->
    <section id="badges" class="space-y-6">
        <div class="flex items-center justify-between border-b border-white/10 pb-4">
            <div>
                <h2 class="text-xl font-bold text-white flex items-center gap-2.5">
                    <i data-lucide="badge-check" class="w-5 h-5 text-[#34C759]"></i>
                    <span>2. Indikator Status Transaksi (&lt;x-order-badge&gt;)</span>
                </h2>
                <p class="text-xs text-[#8E8E93] mt-1">Status mesin pesanan (State Machine) lengkap dengan efek dot berkedip dan warna semantik Apple.</p>
            </div>
            <span class="text-xs font-mono text-[#8E8E93]">components/order-badge.blade.php</span>
        </div>

        <x-glass-card>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 text-center">
                <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/5 flex flex-col items-center justify-center gap-2">
                    <span class="text-[10px] text-[#8E8E93] uppercase">Status: Pending</span>
                    <x-order-badge status="pending" />
                </div>

                <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/5 flex flex-col items-center justify-center gap-2">
                    <span class="text-[10px] text-[#8E8E93] uppercase">Status: Paid</span>
                    <x-order-badge status="paid" />
                </div>

                <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/5 flex flex-col items-center justify-center gap-2">
                    <span class="text-[10px] text-[#8E8E93] uppercase">Status: Processing</span>
                    <x-order-badge status="processing" />
                </div>

                <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/5 flex flex-col items-center justify-center gap-2">
                    <span class="text-[10px] text-[#8E8E93] uppercase">Status: Shipped</span>
                    <x-order-badge status="shipped" />
                </div>

                <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/5 flex flex-col items-center justify-center gap-2">
                    <span class="text-[10px] text-[#8E8E93] uppercase">Status: Completed</span>
                    <x-order-badge status="completed" />
                </div>

                <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/5 flex flex-col items-center justify-center gap-2">
                    <span class="text-[10px] text-[#8E8E93] uppercase">Status: Cancelled</span>
                    <x-order-badge status="cancelled" />
                </div>
            </div>
        </x-glass-card>
    </section>

    <!-- Section 3: Battery Health Meter Tiers -->
    <section id="battery" class="space-y-6">
        <div class="flex items-center justify-between border-b border-white/10 pb-4">
            <div>
                <h2 class="text-xl font-bold text-white flex items-center gap-2.5">
                    <i data-lucide="battery-charging" class="w-5 h-5 text-[#FF9F0A]"></i>
                    <span>3. Indikator Kesehatan Baterai (&lt;x-battery-meter&gt;)</span>
                </h2>
                <p class="text-xs text-[#8E8E93] mt-1">Transparansi kondisi fisik unit second iPhone dengan ikon baterai dan 3 tingkatan kesehatan Apple.</p>
            </div>
            <span class="text-xs font-mono text-[#8E8E93]">components/battery-meter.blade.php</span>
        </div>

        <x-glass-card>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Tier 1: Optimal (>= 90%) -->
                <div class="p-5 rounded-2xl bg-white/[0.03] border border-white/5 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-white">Tingkat Optimal (Prima)</span>
                        <span class="text-[10px] font-bold text-[#34C759]">&ge; 90%</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-battery-meter percentage="100" />
                        <x-battery-meter percentage="95" />
                    </div>
                    <p class="text-xs text-[#8E8E93]">Kondisi baterai sangat prima seperti unit baru.</p>
                </div>

                <!-- Tier 2: Normal (80% - 89%) -->
                <div class="p-5 rounded-2xl bg-white/[0.03] border border-white/5 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-white">Tingkat Normal</span>
                        <span class="text-[10px] font-bold text-[#FF9F0A]">80% - 89%</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-battery-meter percentage="88" />
                        <x-battery-meter percentage="83" />
                    </div>
                    <p class="text-xs text-[#8E8E93]">Daya tahan normal pemakaian wajar harian.</p>
                </div>

                <!-- Tier 3: Service (< 80%) -->
                <div class="p-5 rounded-2xl bg-white/[0.03] border border-white/5 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-white">Tingkat Servis</span>
                        <span class="text-[10px] font-bold text-[#FF453A]">&lt; 80%</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-battery-meter percentage="76" />
                    </div>
                    <p class="text-xs text-[#8E8E93]">Perlu penggantian baterai di pusat servis resmi.</p>
                </div>
            </div>
        </x-glass-card>
    </section>

    <!-- Section 4: Live Product Cards Showcase -->
    <section id="products" class="space-y-6">
        <div class="flex items-center justify-between border-b border-white/10 pb-4">
            <div>
                <h2 class="text-xl font-bold text-white flex items-center gap-2.5">
                    <i data-lucide="smartphone" class="w-5 h-5 text-[#5AC8FA]"></i>
                    <span>4. Kartu Produk Interaktif (&lt;x-product-card&gt;)</span>
                </h2>
                <p class="text-xs text-[#8E8E93] mt-1">Terhubung langsung dengan model Database Eloquent Produk & Varian (Klik dot warna untuk preview interaktif!).</p>
            </div>
            <span class="text-xs font-mono text-[#8E8E93]">components/product-card.blade.php</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($products as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="col-span-full p-8 text-center glass-card">
                    <i data-lucide="package-open" class="w-10 h-10 text-[#8E8E93] mx-auto mb-2"></i>
                    <p class="text-sm text-white font-semibold">Belum ada produk yang dimuat.</p>
                    <p class="text-xs text-[#8E8E93]">Pastikan database seeder sudah dijalankan.</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Section 5: Glass Modal & Interactive Controller -->
    <section class="space-y-6">
        <div class="flex items-center justify-between border-b border-white/10 pb-4">
            <div>
                <h2 class="text-xl font-bold text-white flex items-center gap-2.5">
                    <i data-lucide="maximize-2" class="w-5 h-5 text-[#AF52DE]"></i>
                    <span>5. Modal Pop-Up Kaca (&lt;x-glass-modal&gt;)</span>
                </h2>
                <p class="text-xs text-[#8E8E93] mt-1">Dialog interaktif dengan efek backdrop blur 16px dan transisi taktil.</p>
            </div>
            <span class="text-xs font-mono text-[#8E8E93]">components/glass-modal.blade.php</span>
        </div>

        <x-glass-card class="flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="space-y-1">
                <h3 class="text-base font-semibold text-white">Uji Coba Dialog Modal Kaca</h3>
                <p class="text-xs text-[#8E8E93]">Tekan tombol untuk membuka dialog verifikasi atau tekan <kbd class="px-1.5 py-0.5 rounded bg-white/10 text-white font-mono text-[10px]">Ctrl+K</kbd> untuk modal pencarian.</p>
            </div>
            <div class="flex items-center gap-3">
                <x-glass-button variant="secondary" data-modal-target="demo-modal" icon="eye">
                    Buka Demo Modal
                </x-glass-button>
                <x-glass-button variant="primary" data-modal-target="search-modal" icon="search">
                    Buka Search Modal (Ctrl+K)
                </x-glass-button>
            </div>
        </x-glass-card>
    </section>

</div>

<!-- Demo Modal Instance -->
<x-glass-modal id="demo-modal" title="Verifikasi Keaslian IMEI iPhone" subtitle="Sistem Verifikasi Transparansi Rap-iPhone" icon="shield-check">
    <div class="space-y-4">
        <p class="text-xs text-[#8E8E93] leading-relaxed">
            Seluruh unit iPhone yang dijual di Rap-iPhone telah melewati 25 titik inspeksi teknis menyeluruh, termasuk validasi keaslian modul kamera TrueDepth, layar Super Retina, dan status IMEI resmi terdaftar di Bea Cukai / Kemenperin.
        </p>

        <div class="p-3.5 rounded-xl bg-white/[0.04] border border-white/10 flex items-center justify-between">
            <span class="text-xs text-[#8E8E93]">Status IMEI Database:</span>
            <span class="text-xs font-bold text-[#34C759] flex items-center gap-1.5">
                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                Terdaftar Resmi
            </span>
        </div>

        <div class="flex justify-end gap-2 pt-2">
            <x-glass-button variant="secondary" size="sm" data-modal-close>
                Tutup
            </x-glass-button>
            <x-glass-button variant="primary" size="sm" data-modal-close>
                Mengerti
            </x-glass-button>
        </div>
    </div>
</x-glass-modal>
@endsection
