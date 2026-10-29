@extends('layouts.customer')

@section('title', 'Rap-iPhone — Authorized Style Partner & Program Tukar Tambah iPhone')
@section('meta_description', 'Belanja iPhone Baru Garansi Resmi iBox/Digimap dan iPhone Second Like New Teruji Grade A+. Layanan tukar tambah cepat 15 menit, cicilan 0% Midtrans, dan Click & Collect.')

@section('content')
<div class="cust-page space-y-16 py-6 pb-20">

    <!-- =========================================================================
         1. VISUAL SERIES ICON RIBBON (Horizontal Visual Nav)
         ========================================================================= -->
    <section class="border-b border-[var(--cust-rule-border)] pb-5">
        <div class="flex items-center justify-start lg:justify-between gap-3 sm:gap-4 overflow-x-auto pb-2 scrollbar-none">
            
            <a href="{{ url('/katalog?q=iPhone+16+Pro') }}" class="group flex flex-col items-center gap-1.5 min-w-[74px] text-center shrink-0">
                <div class="w-12 h-12 rounded bg-[var(--cust-surface)] border border-[var(--cust-rule-border)] group-hover:border-[var(--cust-signal)] flex items-center justify-center transition-colors relative">
                    <i data-lucide="smartphone" class="w-5 h-5 text-[var(--cust-ink)] group-hover:text-[var(--cust-signal)] transition-colors"></i>
                    <span class="absolute -top-1.5 px-1 py-0.2 rounded text-[8px] font-medium bg-[var(--cust-tone-promo-bg)] text-[var(--cust-tone-promo-ink)]">Baru</span>
                </div>
                <span class="text-[11px] font-medium text-[var(--cust-ink)] group-hover:text-[var(--cust-signal)] transition-colors">16 Pro</span>
            </a>

            <a href="{{ url('/katalog?series=iPhone+16+Series') }}" class="group flex flex-col items-center gap-1.5 min-w-[74px] text-center shrink-0">
                <div class="w-12 h-12 rounded bg-[var(--cust-surface)] border border-[var(--cust-rule-border)] group-hover:border-[var(--cust-signal)] flex items-center justify-center transition-colors">
                    <i data-lucide="smartphone" class="w-5 h-5 text-[var(--cust-ink)] group-hover:text-[var(--cust-signal)] transition-colors"></i>
                </div>
                <span class="text-[11px] font-medium text-[var(--cust-ink)] group-hover:text-[var(--cust-signal)] transition-colors">iPhone 16</span>
            </a>

            <a href="{{ url('/katalog?q=iPhone+15+Pro') }}" class="group flex flex-col items-center gap-1.5 min-w-[74px] text-center shrink-0">
                <div class="w-12 h-12 rounded bg-[var(--cust-surface)] border border-[var(--cust-rule-border)] group-hover:border-[var(--cust-signal)] flex items-center justify-center transition-colors">
                    <i data-lucide="smartphone" class="w-5 h-5 text-[var(--cust-accent)] group-hover:text-[var(--cust-signal)] transition-colors"></i>
                </div>
                <span class="text-[11px] font-medium text-[var(--cust-ink)] group-hover:text-[var(--cust-signal)] transition-colors">15 Pro</span>
            </a>

            <a href="{{ url('/katalog?series=iPhone+15+Series') }}" class="group flex flex-col items-center gap-1.5 min-w-[74px] text-center shrink-0">
                <div class="w-12 h-12 rounded bg-[var(--cust-surface)] border border-[var(--cust-rule-border)] group-hover:border-[var(--cust-signal)] flex items-center justify-center transition-colors">
                    <i data-lucide="smartphone" class="w-5 h-5 text-[var(--cust-ink)] group-hover:text-[var(--cust-signal)] transition-colors"></i>
                </div>
                <span class="text-[11px] font-medium text-[var(--cust-ink)] group-hover:text-[var(--cust-signal)] transition-colors">iPhone 15</span>
            </a>

            <a href="{{ url('/katalog?series=iPhone+14+Series') }}" class="group flex flex-col items-center gap-1.5 min-w-[74px] text-center shrink-0">
                <div class="w-12 h-12 rounded bg-[var(--cust-surface)] border border-[var(--cust-rule-border)] group-hover:border-[var(--cust-signal)] flex items-center justify-center transition-colors">
                    <i data-lucide="smartphone" class="w-5 h-5 text-[var(--cust-ink-muted)] group-hover:text-[var(--cust-signal)] transition-colors"></i>
                </div>
                <span class="text-[11px] font-medium text-[var(--cust-ink)] group-hover:text-[var(--cust-signal)] transition-colors">iPhone 14</span>
            </a>

            <a href="{{ url('/katalog?series=iPhone+13+Series') }}" class="group flex flex-col items-center gap-1.5 min-w-[74px] text-center shrink-0">
                <div class="w-12 h-12 rounded bg-[var(--cust-surface)] border border-[var(--cust-rule-border)] group-hover:border-[var(--cust-signal)] flex items-center justify-center transition-colors">
                    <i data-lucide="smartphone" class="w-5 h-5 text-[var(--cust-ink-muted)] group-hover:text-[var(--cust-signal)] transition-colors"></i>
                </div>
                <span class="text-[11px] font-medium text-[var(--cust-ink)] group-hover:text-[var(--cust-signal)] transition-colors">iPhone 13</span>
            </a>

            <a href="{{ url('/katalog?condition=second') }}" class="group flex flex-col items-center gap-1.5 min-w-[80px] text-center shrink-0">
                <div class="w-12 h-12 rounded bg-[var(--cust-tone-healthy-bg)] border border-[var(--cust-rule-border)] group-hover:border-[var(--cust-tone-healthy-ink)] flex items-center justify-center transition-colors">
                    <i data-lucide="badge-check" class="w-5 h-5 text-[var(--cust-tone-healthy-ink)]"></i>
                </div>
                <span class="text-[11px] font-medium text-[var(--cust-tone-healthy-ink)]">Second Grade A+</span>
            </a>

            <a href="{{ url('/trade-in') }}" class="group flex flex-col items-center gap-1.5 min-w-[76px] text-center shrink-0">
                <div class="w-12 h-12 rounded bg-[var(--cust-tone-promo-bg)] border border-[var(--cust-rule-border)] group-hover:border-[var(--cust-tone-promo-ink)] flex items-center justify-center transition-colors">
                    <i data-lucide="refresh-cw" class="w-5 h-5 text-[var(--cust-tone-promo-ink)]"></i>
                </div>
                <span class="text-[11px] font-medium text-[var(--cust-tone-promo-ink)]">Tukar Tambah</span>
            </a>

        </div>
    </section>

    <!-- =========================================================================
         2. HERO FLAGSHIP SHOWCASE (Job: buy — Offer as Hero)
         ========================================================================= -->
    <section class="cust-card glass-card p-6 sm:p-10 lg:p-12 bg-[var(--cust-surface)] border border-[var(--cust-rule-border)]">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
            
            <!-- Hero Left: Headline (< 7 words), Sentence (< 12 words), Actions -->
            <div class="lg:col-span-7 space-y-4 text-center lg:text-left">
                <x-customer.spec-badge variant="promo" icon="sparkles">
                    Apple Flagship Showcase 2026 &bull; Inovasi Terdepan &bull; Garansi Terpercaya
                </x-customer.spec-badge>

                <div class="space-y-1">
                    <h1 class="font-serif text-3xl sm:text-5xl font-light tracking-tight text-[var(--cust-ink)] leading-[1.08]">
                        iPhone 16 Pro. Kekuatan Titanium A18 Pro.
                    </h1>
                    <p class="text-base sm:text-lg text-[var(--cust-ink-muted)]">
                        Garansi resmi Indonesia dengan opsi tukar tambah instan 15 menit.
                    </p>
                </div>

                <p class="text-xs sm:text-sm text-[var(--cust-ink-muted)] max-w-lg mx-auto lg:mx-0">
                    Mulai dari <strong class="text-[var(--cust-ink)] font-semibold cust-tabular">Rp {{ number_format($featuredProduct->base_price ?? 18499000, 0, ',', '.') }}</strong> atau cicilan 0% mulai <strong class="text-[var(--cust-signal)] font-semibold cust-tabular">Rp {{ number_format(round(($featuredProduct->base_price ?? 18499000) / 24), 0, ',', '.') }}/bln</strong>.
                </p>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3 pt-2">
                    <x-customer.button 
                        :href="url('/katalog/' . ($featuredProduct->slug ?? 'iphone-16-pro-max'))" 
                        variant="primary" 
                        size="lg" 
                        pill="true"
                        icon="shopping-bag" 
                        class="w-full sm:w-auto">
                        Beli iPhone Sekarang
                    </x-customer.button>

                    <x-customer.button 
                        :href="url('/trade-in')" 
                        variant="outline" 
                        size="lg" 
                        pill="true"
                        icon="refresh-cw" 
                        class="w-full sm:w-auto">
                        Tukar Tambah 15 Menit
                    </x-customer.button>
                </div>

                <!-- Trust Strip -->
                <div class="pt-3 flex flex-wrap items-center justify-center lg:justify-start gap-4 text-xs text-[var(--cust-ink-muted)]">
                    <span class="inline-flex items-center gap-1.5">
                        <i data-lucide="shield-check" class="w-4 h-4 text-[var(--cust-signal)]"></i>
                        <span>Segel Resmi iBox / Digimap</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <i data-lucide="store" class="w-4 h-4 text-[var(--cust-tone-healthy-ink)]"></i>
                        <span>Click &amp; Collect Siap</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <i data-lucide="truck" class="w-4 h-4 text-[var(--cust-ink)]"></i>
                        <span>Gratis Ongkir Berasuransi</span>
                    </span>
                </div>
            </div>

            <!-- Hero Right: Studio Device Display Card -->
            <div class="lg:col-span-5 flex justify-center">
                <div class="w-full max-w-sm bg-[var(--cust-paper-subtle)] rounded border border-[var(--cust-rule-border)] p-6 space-y-4">
                    <div class="flex items-center justify-between text-xs">
                        <x-customer.spec-badge variant="official">
                            Unit Pilihan
                        </x-customer.spec-badge>
                        <span class="text-[11px] text-[var(--cust-ink-muted)]">Desert Titanium</span>
                    </div>

                    <!-- Studio Media -->
                    <div class="aspect-square bg-white rounded border border-[var(--cust-rule-border)] flex items-center justify-center p-4">
                        @php
                            $heroImg = $featuredProduct->primaryImage->image_path ?? ($featuredProduct->images->first()->image_path ?? null);
                        @endphp
                        @if($heroImg && file_exists(public_path($heroImg)))
                            <img src="{{ asset($heroImg) }}" alt="{{ $featuredProduct->name ?? 'iPhone 16 Pro Max' }}" width="280" height="280" class="max-h-full max-w-full object-contain">
                        @else
                            <div class="w-36 h-52 rounded-[32px] bg-[var(--cust-ink)] border-2 border-[var(--cust-accent)] flex flex-col items-center justify-between p-2.5 shadow-sm">
                                <div class="w-12 h-3 bg-black rounded-full flex items-center justify-end px-1">
                                    <div class="w-1.5 h-1.5 rounded-full bg-[var(--cust-signal)]"></div>
                                </div>
                                <div class="w-full flex-grow my-1.5 rounded-[22px] bg-[var(--cust-paper)] flex flex-col items-center justify-center p-2 text-center">
                                    <i data-lucide="smartphone" class="w-7 h-7 text-[var(--cust-ink)] mb-1"></i>
                                    <span class="text-xs font-semibold text-[var(--cust-ink)]">{{ $featuredProduct->name ?? 'iPhone 16 Pro' }}</span>
                                </div>
                                <div class="w-10 h-1 bg-white/40 rounded-full"></div>
                            </div>
                        @endif
                    </div>

                    <!-- Bottom Price -->
                    <div class="pt-2 border-t border-[var(--cust-rule-border)] flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-[var(--cust-ink-muted)] block">Harga Resmi</span>
                            <span class="text-base font-bold text-[var(--cust-ink)] cust-tabular">
                                Rp {{ number_format($featuredProduct->base_price ?? 24999000, 0, ',', '.') }}
                            </span>
                        </div>
                        <a href="{{ url('/katalog') }}" class="cust-btn cust-btn-secondary text-xs py-1.5 px-3">
                            Semua Varian
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- =========================================================================
         3. BENTO SERVICE HIGHLIGHTS (4 Cards)
         ========================================================================= -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="cust-card p-5 space-y-2">
            <div class="w-8 h-8 rounded bg-[var(--cust-tone-official-bg)] text-[var(--cust-tone-official-ink)] flex items-center justify-center">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
            </div>
            <p class="text-xs font-semibold text-[var(--cust-ink)]">Garansi Resmi 1 Tahun</p>
            <p class="text-xs text-[var(--cust-ink-muted)] leading-relaxed">
                Unit BNIB bergaransi resmi Apple Authorized Distributor (iBox / TAM / Digimap).
            </p>
        </div>

        <div class="cust-card p-5 space-y-2">
            <div class="w-8 h-8 rounded bg-[var(--cust-tone-healthy-bg)] text-[var(--cust-tone-healthy-ink)] flex items-center justify-center">
                <i data-lucide="badge-check" class="w-4 h-4"></i>
            </div>
            <p class="text-xs font-semibold text-[var(--cust-ink)]">Certified Second QC 32</p>
            <p class="text-xs text-[var(--cust-ink-muted)] leading-relaxed">
                Inspeksi fungsional 32-titik, Battery Health terukur &gt; 85%, IMEI terdaftar resmi.
            </p>
        </div>

        <div class="cust-card p-5 space-y-2">
            <div class="w-8 h-8 rounded bg-[var(--cust-tone-promo-bg)] text-[var(--cust-tone-promo-ink)] flex items-center justify-center">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
            </div>
            <p class="text-xs font-semibold text-[var(--cust-ink)]">Tukar Tambah 15 Menit</p>
            <p class="text-xs text-[var(--cust-ink-muted)] leading-relaxed">
                Kalkulator taksiran instan online memotong harga unit baru atau cair tunai.
            </p>
        </div>

        <div class="cust-card p-5 space-y-2">
            <div class="w-8 h-8 rounded bg-[var(--cust-paper-subtle)] text-[var(--cust-ink)] flex items-center justify-center border border-[var(--cust-rule-border)]">
                <i data-lucide="credit-card" class="w-4 h-4"></i>
            </div>
            <p class="text-xs font-semibold text-[var(--cust-ink)]">Midtrans &amp; Click &amp; Collect</p>
            <p class="text-xs text-[var(--cust-ink-muted)] leading-relaxed">
                Bayar instan via QRIS/VA atau ambil langsung di gerai store Rap-iPhone.
            </p>
        </div>

    </section>

    <!-- =========================================================================
         4. ETALASE 1: UNIT BARU GARANSI RESMI
         ========================================================================= -->
    <section class="space-y-6">
        <x-customer.section-header 
            tag="Apple Authorized BNIB"
            title="iPhone Baru Garansi Resmi"
            subtitle="Unit 100% baru bersegel resmi TAM / iBox / Digimap dengan IMEI terdaftar Kemenperin"
            actionHref="{{ url('/katalog?condition=new') }}"
            actionLabel="Lihat Semua Baru" />

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @forelse($newProducts as $product)
                <x-customer.product-card :product="$product" />
            @empty
                <div class="col-span-full py-12 text-center text-[var(--cust-ink-muted)] cust-card p-6">
                    <i data-lucide="package" class="w-8 h-8 mx-auto mb-2 text-[var(--cust-ink-faint)]"></i>
                    <p class="text-xs font-medium">Unit baru sedang dalam proses restock.</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- =========================================================================
         5. TRADE-IN EDITORIAL BANNER CARD
         ========================================================================= -->
    <section class="cust-card p-6 sm:p-10 bg-[var(--cust-paper-subtle)] border border-[var(--cust-rule-border)]">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            <div class="lg:col-span-8 space-y-3">
                <x-customer.spec-badge variant="healthy" icon="refresh-cw">
                    Program Trade-In Rap-iPhone
                </x-customer.spec-badge>

                <h2 class="font-serif text-2xl sm:text-3xl font-light text-[var(--cust-ink)] tracking-tight">
                    Tukar Tambah iPhone Lama. Potongan Hingga Rp 15 Juta.
                </h2>

                <p class="text-xs sm:text-sm text-[var(--cust-ink-muted)] max-w-xl leading-relaxed">
                    Dapatkan penaksiran nilai instan online dalam 4 langkah. Gunakan sebagai potongan langsung pembelian unit baru atau pencairan transfer ke rekening bank Anda.
                </p>

                <div class="pt-2 flex flex-wrap gap-2.5">
                    <x-customer.button :href="url('/trade-in')" variant="primary" icon="arrow-right">
                        Hitung Nilai Tukar Tambah
                    </x-customer.button>
                    <x-customer.button :href="url('/katalog?condition=second')" variant="outline">
                        Standar QC 32 Titik
                    </x-customer.button>
                </div>
            </div>

            <div class="lg:col-span-4">
                <div class="bg-white p-5 rounded border border-[var(--cust-rule-border)] space-y-3">
                    <p class="text-xs font-semibold text-[var(--cust-ink)] pb-2 border-b border-[var(--cust-rule-border)]">
                        Contoh Nilai Taksiran
                    </p>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between items-center py-1 border-b border-[var(--cust-rule-border)]">
                            <span class="text-[var(--cust-ink-muted)]">iPhone 13 128GB</span>
                            <span class="font-semibold text-[var(--cust-tone-healthy-ink)] cust-tabular">s/d Rp 7.500.000</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-[var(--cust-rule-border)]">
                            <span class="text-[var(--cust-ink-muted)]">iPhone 14 Pro 256GB</span>
                            <span class="font-semibold text-[var(--cust-tone-healthy-ink)] cust-tabular">s/d Rp 12.800.000</span>
                        </div>
                        <div class="flex justify-between items-center py-1">
                            <span class="text-[var(--cust-ink-muted)]">iPhone 15 Pro Max</span>
                            <span class="font-semibold text-[var(--cust-tone-healthy-ink)] cust-tabular">s/d Rp 17.200.000</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- =========================================================================
         6. ETALASE 2: SECOND LIKE NEW TERUJI
         ========================================================================= -->
    <section class="space-y-6">
        <x-customer.section-header 
            tag="Certified Pre-Owned"
            title="iPhone Second Like New Teruji"
            subtitle="Unit lulus inspeksi 32 titik dengan kesehatan baterai transparan dan sinyal permanen"
            actionHref="{{ url('/katalog?condition=second') }}"
            actionLabel="Lihat Semua Second" />

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @forelse($secondProducts as $product)
                <x-customer.product-card :product="$product" />
            @empty
                <div class="col-span-full py-12 text-center text-[var(--cust-ink-muted)] cust-card p-6">
                    <i data-lucide="package" class="w-8 h-8 mx-auto mb-2 text-[var(--cust-ink-faint)]"></i>
                    <p class="text-xs font-medium">Unit second sedang diinspeksi oleh teknisi kami.</p>
                </div>
            @endforelse
        </div>
    </section>

</div>
@endsection
