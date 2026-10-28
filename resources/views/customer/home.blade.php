@extends('layouts.customer')

@section('title', 'Rap-iPhone — Authorized Style Partner & Program Tukar Tambah iPhone')
@section('meta_description', 'Belanja iPhone Baru Garansi Resmi iBox/Digimap dan iPhone Second Like New Teruji Grade A+. Layanan tukar tambah cepat 15 menit, cicilan 0% Midtrans, dan Click & Collect.')

@section('content')
<div class="space-y-14 sm:space-y-16 pb-16">

    <!-- =========================================================================
         1. VISUAL SERIES ICON RIBBON (Digimap Category Navigation Bar)
         ========================================================================= -->
    <section class="border-b border-[#E5E5EA] bg-[#FFFFFF]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-start lg:justify-between gap-4 sm:gap-6 overflow-x-auto no-scrollbar pb-1">
                
                <a href="{{ url('/katalog?q=iPhone+16+Pro') }}" class="group flex flex-col items-center gap-2 min-w-[78px] text-center shrink-0">
                    <div class="w-13 h-13 rounded-2xl bg-[#F5F5F7] border border-[#E5E5EA] group-hover:border-[#0066CC] group-hover:bg-[#E8F2FF]/50 flex items-center justify-center transition-all relative">
                        <i data-lucide="smartphone" class="w-6 h-6 text-[#1D1D1F] group-hover:text-[#0066CC] transition-colors"></i>
                        <span class="absolute -top-1.5 px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-[#FFF2EB] text-[#BF4800] border border-[#BF4800]/20">Baru</span>
                    </div>
                    <span class="text-[11px] font-semibold text-[#1D1D1F] group-hover:text-[#0066CC] transition-colors">iPhone 16 Pro</span>
                </a>

                <a href="{{ url('/katalog?series=iPhone+16+Series') }}" class="group flex flex-col items-center gap-2 min-w-[78px] text-center shrink-0">
                    <div class="w-13 h-13 rounded-2xl bg-[#F5F5F7] border border-[#E5E5EA] group-hover:border-[#0066CC] group-hover:bg-[#E8F2FF]/50 flex items-center justify-center transition-all">
                        <i data-lucide="smartphone" class="w-6 h-6 text-[#496495] group-hover:text-[#0066CC] transition-colors"></i>
                    </div>
                    <span class="text-[11px] font-semibold text-[#1D1D1F] group-hover:text-[#0066CC] transition-colors">iPhone 16</span>
                </a>

                <a href="{{ url('/katalog?q=iPhone+15+Pro') }}" class="group flex flex-col items-center gap-2 min-w-[78px] text-center shrink-0">
                    <div class="w-13 h-13 rounded-2xl bg-[#F5F5F7] border border-[#E5E5EA] group-hover:border-[#0066CC] group-hover:bg-[#E8F2FF]/50 flex items-center justify-center transition-all">
                        <i data-lucide="smartphone" class="w-6 h-6 text-[#9A958E] group-hover:text-[#0066CC] transition-colors"></i>
                    </div>
                    <span class="text-[11px] font-semibold text-[#1D1D1F] group-hover:text-[#0066CC] transition-colors">iPhone 15 Pro</span>
                </a>

                <a href="{{ url('/katalog?series=iPhone+15+Series') }}" class="group flex flex-col items-center gap-2 min-w-[78px] text-center shrink-0">
                    <div class="w-13 h-13 rounded-2xl bg-[#F5F5F7] border border-[#E5E5EA] group-hover:border-[#0066CC] group-hover:bg-[#E8F2FF]/50 flex items-center justify-center transition-all">
                        <i data-lucide="smartphone" class="w-6 h-6 text-[#1D1D1F] group-hover:text-[#0066CC] transition-colors"></i>
                    </div>
                    <span class="text-[11px] font-semibold text-[#1D1D1F] group-hover:text-[#0066CC] transition-colors">iPhone 15</span>
                </a>

                <a href="{{ url('/katalog?series=iPhone+14+Series') }}" class="group flex flex-col items-center gap-2 min-w-[78px] text-center shrink-0">
                    <div class="w-13 h-13 rounded-2xl bg-[#F5F5F7] border border-[#E5E5EA] group-hover:border-[#0066CC] group-hover:bg-[#E8F2FF]/50 flex items-center justify-center transition-all">
                        <i data-lucide="smartphone" class="w-6 h-6 text-[#6E6E73] group-hover:text-[#0066CC] transition-colors"></i>
                    </div>
                    <span class="text-[11px] font-semibold text-[#1D1D1F] group-hover:text-[#0066CC] transition-colors">iPhone 14</span>
                </a>

                <a href="{{ url('/katalog?series=iPhone+13+Series') }}" class="group flex flex-col items-center gap-2 min-w-[78px] text-center shrink-0">
                    <div class="w-13 h-13 rounded-2xl bg-[#F5F5F7] border border-[#E5E5EA] group-hover:border-[#0066CC] group-hover:bg-[#E8F2FF]/50 flex items-center justify-center transition-all">
                        <i data-lucide="smartphone" class="w-6 h-6 text-[#6E6E73] group-hover:text-[#0066CC] transition-colors"></i>
                    </div>
                    <span class="text-[11px] font-semibold text-[#1D1D1F] group-hover:text-[#0066CC] transition-colors">iPhone 13</span>
                </a>

                <a href="{{ url('/katalog?condition=second') }}" class="group flex flex-col items-center gap-2 min-w-[88px] text-center shrink-0">
                    <div class="w-13 h-13 rounded-2xl bg-[#E8F8EE] border border-[#008040]/20 group-hover:border-[#008040] flex items-center justify-center transition-all">
                        <i data-lucide="badge-check" class="w-6 h-6 text-[#008040]"></i>
                    </div>
                    <span class="text-[11px] font-semibold text-[#008040]">Second Grade A+</span>
                </a>

                <a href="{{ url('/trade-in') }}" class="group flex flex-col items-center gap-2 min-w-[82px] text-center shrink-0">
                    <div class="w-13 h-13 rounded-2xl bg-[#FFF2EB] border border-[#BF4800]/20 group-hover:border-[#BF4800] flex items-center justify-center transition-all">
                        <i data-lucide="refresh-cw" class="w-6 h-6 text-[#BF4800]"></i>
                    </div>
                    <span class="text-[11px] font-semibold text-[#BF4800]">Trade-In 15m</span>
                </a>

            </div>
        </div>
    </section>

    <!-- =========================================================================
         2. HERO CAMPAIGN BANNER & BENTO SERVICE GRID (Digimap Launch Style)
         ========================================================================= -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- Wide Rounded Hero Container (#F5F5F7) -->
        <div class="rounded-[28px] bg-[#F5F5F7] border border-[#E5E5EA] p-6 sm:p-10 lg:p-14 relative overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                
                <!-- Hero Left: Editorial Campaign Typography -->
                <div class="lg:col-span-7 space-y-5 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#FFF2EB] border border-[#BF4800]/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#BF4800]"></span>
                        <span class="text-[11px] font-bold tracking-wider text-[#BF4800] uppercase">
                            Apple Flagship Showcase 2026 &bull; GARANSI RESMI INDONESIA
                        </span>
                    </div>

                    <div class="space-y-2">
                        <h1 class="text-4xl sm:text-[52px] font-bold tracking-tight text-[#1D1D1F] leading-[1.08]">
                            iPhone 16 Pro
                        </h1>
                        <p class="text-xl sm:text-2xl font-semibold text-[#6E6E73] tracking-tight">
                            Inovasi Terdepan. Garansi Terpercaya. A18 Pro.
                        </p>
                    </div>

                    <p class="text-sm sm:text-base text-[#6E6E73] max-w-xl mx-auto lg:mx-0 leading-relaxed">
                        Mulai dari <strong class="text-[#1D1D1F] font-semibold">Rp {{ number_format($featuredProduct->base_price ?? 18499000, 0, ',', '.') }}</strong> atau cicilan 0% mulai <strong class="text-[#0066CC] font-semibold">Rp {{ number_format(round(($featuredProduct->base_price ?? 18499000) / 24), 0, ',', '.') }}/bln</strong>. Tukar tambah iPhone lama Anda hemat hingga Rp 15 Juta.
                    </p>

                    <!-- Dual Pill CTA (Apple / Digimap Retail Standard) -->
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3.5 pt-2">
                        <a href="{{ url('/katalog/' . ($featuredProduct->slug ?? 'iphone-16-pro-max')) }}" class="glass-btn glass-btn-primary glass-btn-lg w-full sm:w-auto">
                            <span>Beli iPhone Sekarang</span>
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </a>

                        <a href="{{ url('/trade-in') }}" class="py-3.5 px-7 rounded-full border border-[#1D1D1F] text-[#1D1D1F] hover:bg-[#1D1D1F] hover:text-white font-semibold text-sm sm:text-base transition-all w-full sm:w-auto text-center inline-flex items-center justify-center gap-2">
                            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                            <span>Tukar Tambah 15 Menit</span>
                        </a>
                    </div>

                    <!-- Omnichannel Quick Info -->
                    <div class="pt-3 flex flex-wrap items-center justify-center lg:justify-start gap-5 text-xs text-[#6E6E73] font-medium">
                        <span class="inline-flex items-center gap-1.5">
                            <i data-lucide="shield-check" class="w-4 h-4 text-[#0066CC]"></i>
                            <span>Segel Resmi iBox / Digimap</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <i data-lucide="store" class="w-4 h-4 text-[#008040]"></i>
                            <span>Tersedia Click &amp; Collect 15 Menit</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <i data-lucide="truck" class="w-4 h-4 text-[#1D1D1F]"></i>
                            <span>Gratis Ongkir Berasuransi</span>
                        </span>
                    </div>
                </div>

                <!-- Hero Right: Studio Flagship Showcase Card -->
                <div class="lg:col-span-5 flex justify-center">
                    <div class="w-full max-w-sm bg-[#FFFFFF] rounded-[24px] p-6 sm:p-8 border border-[#E5E5EA] shadow-[0_12px_36px_rgba(0,0,0,0.05)]">
                        <div class="flex items-center justify-between gap-2 mb-4">
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#E8F2FF] text-[#0066CC]">
                                Flagship Pilihan
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#6E6E73]">
                                <span class="w-3 h-3 rounded-full bg-[#C5B49F] border border-black/15"></span>
                                <span>Desert Titanium</span>
                            </span>
                        </div>

                        <!-- Studio Device Display -->
                        <div class="relative w-full aspect-square rounded-2xl bg-[#F5F5F7] flex items-center justify-center p-6 my-3">
                            @php
                                $heroImg = $featuredProduct->primaryImage->image_path ?? ($featuredProduct->images->first()->image_path ?? null);
                            @endphp
                            @if($heroImg && file_exists(public_path($heroImg)))
                                <img src="{{ asset($heroImg) }}" alt="{{ $featuredProduct->name ?? 'iPhone 16 Pro Max' }}" width="300" height="300" class="max-h-full max-w-full object-contain">
                            @else
                                <div class="w-44 h-60 rounded-[38px] bg-gradient-to-b from-[#3A352F] to-[#1D1D1F] border-[4px] border-[#C5B49F] shadow-xl flex flex-col items-center justify-between p-3">
                                    <div class="w-14 h-3.5 bg-black rounded-full flex items-center justify-end px-1.5">
                                        <div class="w-1.5 h-1.5 rounded-full bg-[#0071E3]"></div>
                                    </div>
                                    <div class="w-full flex-grow my-2 rounded-[26px] bg-gradient-to-b from-[#FFFFFF] via-[#F5F5F7] to-[#E8F2FF] flex flex-col items-center justify-center p-3 text-center">
                                        <i data-lucide="smartphone" class="w-9 h-9 text-[#1D1D1F] mb-1.5"></i>
                                        <span class="text-xs font-bold text-[#1D1D1F]">{{ $featuredProduct->name ?? 'iPhone 16 Pro Max' }}</span>
                                        <span class="text-[10px] text-[#6E6E73] mt-0.5">A18 Pro &bull; Titanium</span>
                                        <span class="mt-2.5 px-2.5 py-0.5 rounded-full bg-[#E8F8EE] text-[#008040] text-[10px] font-bold">
                                            Ready Stock Resmi
                                        </span>
                                    </div>
                                    <div class="w-14 h-1 bg-white/50 rounded-full"></div>
                                </div>
                            @endif
                        </div>

                        <div class="pt-3 border-t border-[#E5E5EA] flex items-center justify-between gap-3">
                            <div>
                                <span class="text-[11px] text-[#6E6E73] block font-medium">Harga Resmi Mulai</span>
                                <span class="text-lg font-bold text-[#1D1D1F]">
                                    Rp {{ number_format($featuredProduct->base_price ?? 24999000, 0, ',', '.') }}
                                </span>
                            </div>
                            <a href="{{ url('/katalog') }}" class="px-4 py-2 rounded-full bg-[#1D1D1F] text-white text-xs font-semibold hover:bg-[#0071E3] transition-colors inline-flex items-center gap-1">
                                <span>Lihat Semua</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Bento Service & Promo Highlights (4 Cards Below Hero) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            
            <!-- Bento 1 -->
            <div class="p-6 rounded-[20px] bg-[#F5F5F7] border border-[#E5E5EA] space-y-2.5 hover:bg-[#EFEFF4] transition-colors">
                <div class="w-10 h-10 rounded-xl bg-[#E8F2FF] text-[#0066CC] flex items-center justify-center">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                </div>
                <h3 class="text-sm font-bold text-[#1D1D1F]">Garansi Resmi Indonesia</h3>
                <p class="text-xs text-[#6E6E73] leading-relaxed">
                    Unit BNIB bersegel resmi (iBox / Digimap / TAM) dengan jaminan servis resmi Apple Authorized 1 tahun.
                </p>
            </div>

            <!-- Bento 2 -->
            <div class="p-6 rounded-[20px] bg-[#F5F5F7] border border-[#E5E5EA] space-y-2.5 hover:bg-[#EFEFF4] transition-colors">
                <div class="w-10 h-10 rounded-xl bg-[#E8F8EE] text-[#008040] flex items-center justify-center">
                    <i data-lucide="badge-check" class="w-5 h-5"></i>
                </div>
                <h3 class="text-sm font-bold text-[#1D1D1F]">Certified Second Like New</h3>
                <p class="text-xs text-[#6E6E73] leading-relaxed">
                    Lulus inspeksi 32 titik, Battery Health aktual &gt; 85%, IMEI terdaftar Kemenperin, &amp; garansi toko.
                </p>
            </div>

            <!-- Bento 3 -->
            <div class="p-6 rounded-[20px] bg-[#F5F5F7] border border-[#E5E5EA] space-y-2.5 hover:bg-[#EFEFF4] transition-colors">
                <div class="w-10 h-10 rounded-xl bg-[#FFF2EB] text-[#BF4800] flex items-center justify-center">
                    <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                </div>
                <h3 class="text-sm font-bold text-[#1D1D1F]">Tukar Tambah 15 Menit</h3>
                <p class="text-xs text-[#6E6E73] leading-relaxed">
                    Kalkulator taksiran instan online, langsung memotong harga pembelian unit baru atau cair tunai.
                </p>
            </div>

            <!-- Bento 4 -->
            <div class="p-6 rounded-[20px] bg-[#F5F5F7] border border-[#E5E5EA] space-y-2.5 hover:bg-[#EFEFF4] transition-colors">
                <div class="w-10 h-10 rounded-xl bg-[#FFFFFF] border border-[#D2D2D7] text-[#1D1D1F] flex items-center justify-center">
                    <i data-lucide="credit-card" class="w-5 h-5"></i>
                </div>
                <h3 class="text-sm font-bold text-[#1D1D1F]">Midtrans &amp; Click &amp; Collect</h3>
                <p class="text-xs text-[#6E6E73] leading-relaxed">
                    Bayar aman via QRIS, Virtual Account, &amp; Kartu Kredit atau ambil langsung di gerai Store Rap-iPhone.
                </p>
            </div>

        </div>
    </section>

    <!-- =========================================================================
         3. DUAL-CONDITION SEGMENTED CONTROL & SERIES PILLS
         ========================================================================= -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-5 border-b border-[#E5E5EA]">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-[#1D1D1F] tracking-tight">Jelajahi Lini iPhone</h2>
                <p class="text-xs sm:text-sm text-[#6E6E73] mt-0.5">Pilih antara unit Baru Bersegel Resmi atau Second Like New Teruji</p>
            </div>

            <!-- Dual-Condition Apple Segmented Switcher -->
            <div class="inline-flex p-1 rounded-full bg-[#F5F5F7] border border-[#E5E5EA] self-start md:self-auto">
                <a href="{{ url('/katalog') }}" class="px-4 py-1.5 rounded-full text-xs font-semibold bg-[#1D1D1F] text-white shadow-sm">
                    Semua Kondisi
                </a>
                <a href="{{ url('/katalog?condition=new') }}" class="px-4 py-1.5 rounded-full text-xs font-semibold text-[#6E6E73] hover:text-[#1D1D1F] transition-colors">
                    Unit Baru Resmi
                </a>
                <a href="{{ url('/katalog?condition=second') }}" class="px-4 py-1.5 rounded-full text-xs font-semibold text-[#6E6E73] hover:text-[#008040] transition-colors">
                    Second Like New
                </a>
            </div>
        </div>

        <!-- Series Filter Pills -->
        <div class="flex flex-wrap items-center gap-2 pt-4">
            <a href="{{ url('/katalog') }}" class="px-4 py-2 rounded-full text-xs font-semibold bg-[#1D1D1F] text-white">
                Semua Seri
            </a>
            @foreach($seriesList as $series)
                <a href="{{ url('/katalog?series=' . urlencode($series)) }}" class="px-4 py-2 rounded-full text-xs font-semibold bg-[#F5F5F7] text-[#1D1D1F] border border-[#E5E5EA] hover:border-[#0066CC] hover:text-[#0066CC] transition-all">
                    {{ $series }}
                </a>
            @endforeach
        </div>
    </section>

    <!-- =========================================================================
         4. ETALASE 1: UNIT BARU GARANSI RESMI (BNIB iBox / Digimap)
         ========================================================================= -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <span class="text-[11px] font-bold text-[#0066CC] uppercase tracking-wider block">Apple Authorized BNIB</span>
                <div class="flex items-center gap-2.5 mt-1">
                    <h2 class="text-2xl sm:text-[28px] font-bold text-[#1D1D1F] tracking-tight">iPhone Baru Garansi Resmi</h2>
                    <span class="hidden sm:inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#E8F2FF] text-[#0066CC]">
                        Segel Resmi iBox / Digimap
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-[#6E6E73] mt-0.5">100% Baru, garansi resmi Apple Indonesia 1 tahun, IMEI terdaftar Kemenperin</p>
            </div>

            <a href="{{ url('/katalog?condition=new') }}" class="text-xs sm:text-sm font-semibold text-[#0066CC] hover:underline flex items-center gap-1 shrink-0">
                <span>Lihat Semua Baru</span>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($newProducts as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="col-span-full py-12 text-center text-[#6E6E73] bg-[#F5F5F7] rounded-[20px] border border-[#E5E5EA]">
                    <i data-lucide="package" class="w-10 h-10 mx-auto mb-2 text-[#86868B]"></i>
                    <p class="text-sm font-medium">Unit baru sedang dalam proses restock.</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- =========================================================================
         5. BANNER KAMPANYE TRADE-IN (Digimap Trade-In Editorial Card)
         ========================================================================= -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="rounded-[28px] bg-[#F5F5F7] border border-[#E5E5EA] p-6 sm:p-10 lg:p-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                
                <div class="lg:col-span-8 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-[#E8F8EE] text-[#008040]">
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                        <span>Program Trade-In Resmi Rap-iPhone</span>
                    </div>

                    <h2 class="text-2xl sm:text-4xl font-bold text-[#1D1D1F] tracking-tight leading-tight">
                        Tukar Tambah iPhone Lama Anda.<br>
                        Hemat Hingga <span class="text-[#0066CC]">Rp 15.000.000</span> untuk Unit Baru.
                    </h2>

                    <p class="text-xs sm:text-sm text-[#6E6E73] max-w-xl leading-relaxed">
                        Dapatkan estimasi harga instan secara online dalam 4 langkah mudah. Pilih potongan harga langsung untuk pembelian iPhone baru atau pencairan tunai ke rekening bank dalam 15 menit.
                    </p>

                    <div class="pt-2 flex flex-wrap gap-3">
                        <a href="{{ url('/trade-in') }}" class="glass-btn glass-btn-primary glass-btn-md">
                            <span>Hitung Estimasi Trade-In</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                        <a href="{{ url('/katalog?condition=second') }}" class="glass-btn glass-btn-secondary glass-btn-md">
                            <span>Lihat Standar QC 32 Titik</span>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-4">
                    <div class="bg-[#FFFFFF] p-6 rounded-2xl border border-[#E5E5EA] shadow-sm space-y-3.5">
                        <div class="flex items-center justify-between border-b border-[#E5E5EA] pb-2.5">
                            <span class="text-xs font-bold text-[#1D1D1F] uppercase tracking-wider">Nilai Taksiran Populer</span>
                            <span class="text-[11px] font-semibold text-[#008040]">Real-Time</span>
                        </div>
                        
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between items-center p-2.5 rounded-xl bg-[#F5F5F7]">
                                <span class="text-[#1D1D1F] font-medium">iPhone 13 128GB</span>
                                <span class="font-bold text-[#008040]">s/d Rp 7.500.000</span>
                            </div>
                            <div class="flex justify-between items-center p-2.5 rounded-xl bg-[#F5F5F7]">
                                <span class="text-[#1D1D1F] font-medium">iPhone 14 Pro 256GB</span>
                                <span class="font-bold text-[#008040]">s/d Rp 12.800.000</span>
                            </div>
                            <div class="flex justify-between items-center p-2.5 rounded-xl bg-[#F5F5F7]">
                                <span class="text-[#1D1D1F] font-medium">iPhone 15 Pro Max</span>
                                <span class="font-bold text-[#008040]">s/d Rp 17.200.000</span>
                            </div>
                        </div>

                        <p class="text-[11px] text-[#86868B] text-center">
                            *Tergantung kondisi fisik, fungsi &amp; Battery Health unit.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- =========================================================================
         6. ETALASE 2: SECOND LIKE NEW TERUJI (Grade A+ with Battery Health)
         ========================================================================= -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <span class="text-[11px] font-bold text-[#008040] uppercase tracking-wider block">Certified Pre-Owned &bull; Lulus QC 32 Titik</span>
                <div class="flex items-center gap-2.5 mt-1">
                    <h2 class="text-2xl sm:text-[28px] font-bold text-[#1D1D1F] tracking-tight">iPhone Second Like New Teruji</h2>
                    <span class="hidden sm:inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#E8F8EE] text-[#008040]">
                        Grade A+ &bull; BH &gt; 85%
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-[#6E6E73] mt-0.5">Kondisi fisik mulus 98–99%, Battery Health aktual transparan, sinyal IMEI permanen</p>
            </div>

            <a href="{{ url('/katalog?condition=second') }}" class="text-xs sm:text-sm font-semibold text-[#008040] hover:underline flex items-center gap-1 shrink-0">
                <span>Lihat Semua Second</span>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($secondProducts as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="col-span-full py-12 text-center text-[#6E6E73] bg-[#F5F5F7] rounded-[20px] border border-[#E5E5EA]">
                    <i data-lucide="package" class="w-10 h-10 mx-auto mb-2 text-[#86868B]"></i>
                    <p class="text-sm font-medium">Unit second sedang diinspeksi oleh teknisi kami.</p>
                </div>
            @endforelse
        </div>
    </section>

</div>
@endsection
