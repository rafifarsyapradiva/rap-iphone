@extends('layouts.customer')

@section('title', 'Rap-iPhone — Authorized Style Store & Program Tukar Tambah iPhone')
@section('meta_description', 'Belanja iPhone Baru Garansi Resmi iBox/Digimap dan iPhone Second Like New Teruji Grade A+. Layanan tukar tambah cepat 15 menit dan gratis ongkir berasuransi.')

@section('content')
<div class="space-y-20 pb-20">

    <!-- =========================================================================
         1. HERO SHOWCASE APPLE FLAGSHIP (Floating Unit & Ambient Glow)
         ========================================================================= -->
    <section class="relative pt-6 sm:pt-12 overflow-hidden">
        
        <!-- Ambient Local Radial Glow -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[650px] h-[650px] bg-gradient-to-tr from-[#0071E3]/25 via-[#AF52DE]/15 to-transparent rounded-full blur-3xl pointer-events-none -z-10 animate-pulse duration-1000"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- Hero Left: Typography & Dual CTA -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/5 border border-white/10 backdrop-blur-md">
                        <span class="w-2 h-2 rounded-full bg-[#0071E3] animate-ping"></span>
                        <span class="text-xs font-semibold tracking-wide text-[#5AC8FA] uppercase">
                            Apple Flagship Showcase 2026
                        </span>
                    </div>

                    <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-white leading-[1.1]">
                        Inovasi Terdepan. <br>
                        <span class="bg-gradient-to-r from-white via-[#E5E5EA] to-[#0071E3] bg-clip-text text-transparent">
                            Garansi Terpercaya.
                        </span>
                    </h1>

                    <p class="text-base sm:text-lg text-[#8E8E93] max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                        Pusat retail iPhone resmi dan pre-owned teruji standar Apple. Dapatkan unit baru bergaransi resmi iBox/Digimap atau unit second Grade A+ dengan transparansi Battery Health dan garansi toko terpercaya.
                    </p>

                    <!-- Dual Action CTA -->
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                        <a href="{{ url('/katalog') }}" class="glass-btn glass-btn-primary glass-btn-lg w-full sm:w-auto shadow-xl group">
                            <i data-lucide="shopping-bag" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                            <span>Beli iPhone Sekarang</span>
                        </a>

                        <a href="{{ url('/katalog?condition=second') }}" class="glass-btn glass-btn-secondary glass-btn-lg w-full sm:w-auto group">
                            <i data-lucide="refresh-cw" class="w-5 h-5 text-[#34C759] transition-transform group-hover:rotate-180 duration-500"></i>
                            <span>Jelajah Second Teruji</span>
                        </a>
                    </div>

                    <!-- Highlight Badges -->
                    <div class="pt-4 flex flex-wrap items-center justify-center lg:justify-start gap-6 text-xs text-[#8E8E93]">
                        <div class="flex items-center gap-2">
                            <i data-lucide="shield-check" class="w-4 h-4 text-[#0071E3]"></i>
                            <span>100% Produk Original</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="truck" class="w-4 h-4 text-[#34C759]"></i>
                            <span>Gratis Ongkir & Asuransi</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="zap" class="w-4 h-4 text-[#FF9F0A]"></i>
                            <span>Tukar Tambah 15 Menit</span>
                        </div>
                    </div>
                </div>

                <!-- Hero Right: 3D Floating Flagship Device Card -->
                <div class="lg:col-span-5 flex justify-center">
                    <div class="relative w-full max-w-sm">
                        <!-- Floating Background Glow Aura -->
                        <div class="absolute inset-0 bg-gradient-to-b from-[#C5B49F]/30 via-[#0071E3]/20 to-transparent blur-2xl rounded-full transform scale-95 animate-pulse"></div>

                        <!-- Flagship Elevated Card -->
                        <div class="glass-card-elevated p-6 sm:p-8 relative rounded-[32px] border border-white/20 shadow-2xl backdrop-blur-2xl transition-all duration-700 hover:scale-[1.02]">
                            
                            <!-- Header Info -->
                            <div class="flex items-center justify-between gap-3 mb-6">
                                <div class="flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-[#0071E3]/15 text-[#5AC8FA] border border-[#0071E3]/30">
                                    <span class="w-2 h-2 rounded-full bg-[#0071E3]"></span>
                                    <span>Apple Flagship</span>
                                </div>
                                <span class="text-xs text-[#C5B49F] font-semibold tracking-wide">Desert Titanium</span>
                            </div>

                            <!-- Device Render Simulation -->
                            <div class="relative w-full aspect-square flex items-center justify-center my-4 group">
                                <div class="w-48 h-64 sm:w-52 sm:h-72 rounded-[40px] bg-gradient-to-tr from-[#1E1B18] via-[#2A2622] to-[#3C352E] border-[4px] border-[#C5B49F]/50 shadow-2xl relative flex flex-col items-center justify-between p-3 transform transition-transform duration-700 group-hover:-translate-y-3">
                                    
                                    <!-- Dynamic Island -->
                                    <div class="w-16 h-4 bg-black rounded-full border border-white/10 flex items-center justify-end px-2">
                                        <div class="w-2 h-2 rounded-full bg-[#0071E3]"></div>
                                    </div>

                                    <!-- Screen Interior -->
                                    <div class="w-full flex-grow my-2 rounded-[28px] bg-gradient-to-b from-[#0B0C10] to-[#161B22] flex flex-col items-center justify-center p-3 text-center border border-white/5 relative overflow-hidden">
                                        <div class="absolute -top-10 -right-10 w-24 h-24 bg-[#C5B49F]/20 rounded-full blur-xl"></div>
                                        <i data-lucide="smartphone" class="w-10 h-10 text-[#C5B49F] mb-2 opacity-90"></i>
                                        <span class="text-xs font-bold text-white tracking-wide">iPhone 16 Pro Max</span>
                                        <span class="text-[10px] text-[#8E8E93] mt-0.5">Chip A18 Pro · Titanium</span>
                                        
                                        <div class="mt-3 px-2 py-0.5 rounded-full bg-[#34C759]/20 text-[#34C759] text-[9px] font-bold border border-[#34C759]/30">
                                            Ready Stock iBox
                                        </div>
                                    </div>

                                    <!-- Home Bar Indicator -->
                                    <div class="w-16 h-1 bg-white/40 rounded-full"></div>
                                </div>
                            </div>

                            <!-- Card Bottom Detail -->
                            <div class="space-y-3 pt-3 border-t border-white/10">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-[11px] text-[#8E8E93] block uppercase tracking-wider font-medium">Mulai Dari</span>
                                        <span class="text-xl font-black text-white">
                                            Rp {{ number_format($featuredProduct->base_price ?? 24999000, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <a href="{{ url('/katalog/' . ($featuredProduct->slug ?? 'iphone-16-pro-max')) }}" class="glass-btn glass-btn-primary glass-btn-sm">
                                        <span>Lihat Unit</span>
                                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <!-- =========================================================================
         2. TIGA PILAR LAYANAN RETAIL KACA
         ========================================================================= -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="glass-card p-6 rounded-3xl border border-white/10 flex items-start gap-4 hover:border-[#0071E3]/40 transition-all">
                <div class="w-12 h-12 rounded-2xl bg-[#0071E3]/15 border border-[#0071E3]/30 flex items-center justify-center shrink-0 text-[#0071E3]">
                    <i data-lucide="shield-check" class="w-6 h-6"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="font-bold text-white text-base">100% Garansi Resmi</h3>
                    <p class="text-xs text-[#8E8E93] leading-relaxed">
                        Semua unit baru diimpor resmi oleh TAM / iBox / Digimap dengan jaminan garansi servis resmi Apple Authorized Provider 1 tahun.
                    </p>
                </div>
            </div>

            <div class="glass-card p-6 rounded-3xl border border-white/10 flex items-start gap-4 hover:border-[#34C759]/40 transition-all">
                <div class="w-12 h-12 rounded-2xl bg-[#34C759]/15 border border-[#34C759]/30 flex items-center justify-center shrink-0 text-[#34C759]">
                    <i data-lucide="package-check" class="w-6 h-6"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="font-bold text-white text-base">Unit Second Teruji 32 Titik</h3>
                    <p class="text-xs text-[#8E8E93] leading-relaxed">
                        Transparansi nomor IMEI terdaftar, Battery Health aktual di atas 85%, dan garansi ganti unit toko selama 7 hari tanpa ribet.
                    </p>
                </div>
            </div>

            <div class="glass-card p-6 rounded-3xl border border-white/10 flex items-start gap-4 hover:border-[#FF9F0A]/40 transition-all">
                <div class="w-12 h-12 rounded-2xl bg-[#FF9F0A]/15 border border-[#FF9F0A]/30 flex items-center justify-center shrink-0 text-[#FF9F0A]">
                    <i data-lucide="refresh-cw" class="w-6 h-6"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="font-bold text-white text-base">Tukar Tambah 15 Menit</h3>
                    <p class="text-xs text-[#8E8E93] leading-relaxed">
                        Kalkulasi nilai iPhone lama Anda secara instan. Jadikan potongan harga belanja iPhone baru atau cairkan langsung ke rekening Anda.
                    </p>
                </div>
            </div>

        </div>
    </section>


    <!-- =========================================================================
         3. KATEGORI SERI iPHONE (Series Selector Pills)
         ========================================================================= -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-white/10 pb-5">
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Katalog Seri iPhone</h2>
                <p class="text-xs text-[#8E8E93] mt-1">Pilih lini generasi Apple favorit Anda</p>
            </div>

            <!-- Series Pills -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ url('/katalog') }}" class="px-4 py-2 rounded-full text-xs font-semibold bg-white/10 text-white border border-white/20 hover:bg-white/20 transition-all">
                    Semua Seri
                </a>
                @foreach($seriesList as $series)
                    <a href="{{ url('/katalog?series=' . urlencode($series)) }}" class="px-4 py-2 rounded-full text-xs font-semibold bg-white/5 text-[#8E8E93] border border-white/10 hover:text-white hover:border-white/25 hover:bg-white/10 transition-all">
                        {{ $series }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>


    <!-- =========================================================================
         4. ETALASE 1: UNIT BARU GARANSI RESMI (BNIB iBox / Digimap)
         ========================================================================= -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-3 h-8 rounded-full bg-[#0071E3]"></div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl sm:text-2xl font-black text-white">iPhone Baru Garansi Resmi</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#0071E3]/20 text-[#5AC8FA] border border-[#0071E3]/30">
                            BNIB TAM / iBox
                        </span>
                    </div>
                    <p class="text-xs text-[#8E8E93] mt-0.5">Segel resmi utuh, belum aktif garansi, terdaftar resmi di Kemenperin</p>
                </div>
            </div>

            <a href="{{ url('/katalog?condition=new') }}" class="text-xs font-semibold text-[#5AC8FA] hover:text-white flex items-center gap-1 group">
                <span>Lihat Semua</span>
                <i data-lucide="chevron-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
            </a>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($newProducts as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="col-span-full py-12 text-center text-[#8E8E93] glass-card rounded-3xl">
                    <i data-lucide="package" class="w-10 h-10 mx-auto mb-2 opacity-50"></i>
                    <p class="text-sm">Unit baru sedang dalam proses restock.</p>
                </div>
            @endforelse
        </div>
    </section>


    <!-- =========================================================================
         5. BANNER PROMO TUKAR TAMBAH KACA FROSTED
         ========================================================================= -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative glass-card-elevated rounded-[32px] p-8 sm:p-12 border border-white/20 overflow-hidden">
            <!-- Background Glows -->
            <div class="absolute -right-16 -top-16 w-80 h-80 bg-[#34C759]/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 -bottom-16 w-80 h-80 bg-[#0071E3]/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-8 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-[#34C759]/15 text-[#34C759] border border-[#34C759]/30">
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                        <span>Program Trade-In Rap-iPhone</span>
                    </div>

                    <h2 class="text-2xl sm:text-4xl font-black text-white tracking-tight">
                        Punya iPhone Lama? <br>
                        Tukar Tambah Lebih Hemat Hingga <span class="text-[#34C759]">Rp 15.000.000</span>
                    </h2>

                    <p class="text-sm text-[#8E8E93] max-w-xl leading-relaxed">
                        Cek taksiran nilai pasar iPhone Anda dalam 4 langkah mudah. Dapatkan voucher potongan langsung untuk upgrade ke iPhone 16 Series atau cairkan ke rekening bank.
                    </p>

                    <div class="pt-2 flex flex-wrap gap-4">
                        <a href="{{ url('/trade-in') }}" class="glass-btn glass-btn-primary glass-btn-md">
                            <span>Mulai Cek Taksiran</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                        <a href="{{ url('/katalog?condition=second') }}" class="glass-btn glass-btn-secondary glass-btn-md">
                            <span>Pelajari Standar Grade</span>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-4 flex justify-center">
                    <div class="w-full max-w-xs glass-card p-5 rounded-2xl border border-white/10 space-y-3">
                        <p class="text-xs font-bold text-white uppercase tracking-wider">Simulasi Cepat Trade-In</p>
                        
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between p-2 rounded-lg bg-white/5">
                                <span class="text-[#8E8E93]">iPhone 13 128GB</span>
                                <span class="font-bold text-[#34C759]">s/d Rp 7.500.000</span>
                            </div>
                            <div class="flex justify-between p-2 rounded-lg bg-white/5">
                                <span class="text-[#8E8E93]">iPhone 14 Pro 256GB</span>
                                <span class="font-bold text-[#34C759]">s/d Rp 12.800.000</span>
                            </div>
                            <div class="flex justify-between p-2 rounded-lg bg-white/5">
                                <span class="text-[#8E8E93]">iPhone 15 Pro Max</span>
                                <span class="font-bold text-[#34C759]">s/d Rp 17.200.000</span>
                            </div>
                        </div>

                        <span class="text-[10px] text-[#8E8E93] block text-center italic">*Estimasi tergantung kelengkapan & kondisi fisik unit</span>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- =========================================================================
         6. ETALASE 2: SECOND LIKE NEW TERUJI (Grade A+ with Battery Health)
         ========================================================================= -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-3 h-8 rounded-full bg-[#34C759]"></div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl sm:text-2xl font-black text-white">iPhone Second Like New Teruji</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#34C759]/20 text-[#34C759] border border-[#34C759]/30">
                            Grade A+ Terjamin
                        </span>
                    </div>
                    <p class="text-xs text-[#8E8E93] mt-0.5">Fisik mulus 98-99%, Battery Health prima, TrueTone & Face ID normal 100%</p>
                </div>
            </div>

            <a href="{{ url('/katalog?condition=second') }}" class="text-xs font-semibold text-[#34C759] hover:text-white flex items-center gap-1 group">
                <span>Lihat Semua Second</span>
                <i data-lucide="chevron-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
            </a>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($secondProducts as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="col-span-full py-12 text-center text-[#8E8E93] glass-card rounded-3xl">
                    <i data-lucide="package" class="w-10 h-10 mx-auto mb-2 opacity-50"></i>
                    <p class="text-sm">Unit second sedang diinspeksi oleh teknisi kami.</p>
                </div>
            @endforelse
        </div>
    </section>

</div>
@endsection

