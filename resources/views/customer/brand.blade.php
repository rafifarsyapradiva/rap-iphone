@extends('layouts.customer')

@section('title', 'Identitas Merek & Logo Minimalis Resmi — Rap-iPhone Studio')
@section('meta_description', 'Aset identitas visual dan logo minimalis resmi Rap-iPhone. Tersedia format vektor SVG, favicon, dan raster resolusi tinggi untuk brand partnership.')

@section('content')
<div class="cust-page py-8 space-y-12 pb-24">

    <!-- 1. Breadcrumbs & Header -->
    <div class="space-y-3 pb-6 border-b border-[var(--cust-rule-border)]">
        <nav class="flex items-center gap-2 text-xs text-[var(--cust-ink-muted)]">
            <a href="{{ url('/') }}" class="hover:text-[var(--cust-ink)] transition-colors">Beranda</a>
            <span class="text-[var(--cust-ink-faint)]">/</span>
            <span class="text-[var(--cust-ink)] font-medium">Brand Identity &amp; Logo</span>
        </nav>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div class="space-y-1">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-[11px] font-semibold bg-[var(--cust-tone-official-bg)] text-[var(--cust-tone-official-ink)] border border-[var(--cust-tone-official-border)]">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                    <span>Official Brand Assets &middot; Studio Precision</span>
                </div>
                <h1 class="font-serif text-3xl sm:text-4xl text-[var(--cust-ink)] tracking-tight leading-tight">
                    Logo Minimalis <span class="italic font-normal text-[var(--cust-ink-muted)]">Rap-iPhone</span>
                </h1>
                <p class="text-xs sm:text-sm text-[var(--cust-ink-muted)] max-w-2xl leading-relaxed">
                    Identitas visual resmi yang dirancang dengan estetika Cupertino Studio &amp; Luxury Retail. Memadukan siluet squircle Apple berakurasi tinggi, monogram arsitektural huruf "R", kapsul Dynamic Island, dan aksen optik Apple Signal Blue.
                </p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ asset('images/logo/logo-mark.svg') }}" download="rap-iphone-logomark.svg" class="cust-btn cust-btn-primary cust-btn-sm text-xs">
                    <i data-lucide="download" class="w-3.5 h-3.5"></i>
                    <span>Unduh Logomark SVG</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. Hero Studio Showcase Render -->
    @if(file_exists(public_path('images/logo/logo-showcase.jpg')))
        <section class="space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold text-[var(--cust-ink-muted)] uppercase tracking-wider">
                    Studio Render &middot; Apple Daylight Atelier
                </span>
                <span class="text-[11px] text-[var(--cust-ink-faint)] font-mono">Precision Titanium Finish</span>
            </div>

            <div class="cust-card overflow-hidden border border-[var(--cust-rule-border)] shadow-sm group">
                <div class="relative aspect-video max-h-[460px] w-full bg-[var(--cust-paper-subtle)] flex items-center justify-center overflow-hidden">
                    <img 
                        src="{{ asset('images/logo/logo-showcase.jpg') }}" 
                        alt="Rap-iPhone Minimalist Logo Studio Showcase" 
                        class="w-full h-full object-cover transform group-hover:scale-[1.01] transition-transform duration-500 ease-out"
                        loading="eager"
                        decoding="async"
                    >
                    <div class="absolute bottom-4 left-4 right-4 sm:bottom-6 sm:left-6 sm:right-6 p-4 rounded-xl bg-[var(--cust-surface)]/90 backdrop-blur-md border border-[var(--cust-rule-border)] flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-md">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('images/logo/logo-mark.svg') }}" alt="Emblem" class="w-9 h-9 rounded-lg shrink-0">
                            <div>
                                <h3 class="text-xs font-bold text-[var(--cust-ink)] leading-tight">The Titanium Monogram &amp; Capsule</h3>
                                <p class="text-[11px] text-[var(--cust-ink-muted)]">Lambang squircle bergradasi titanium dengan optik safir biru.</p>
                            </div>
                        </div>
                        <a href="{{ asset('images/logo/logo-showcase.jpg') }}" target="_blank" class="cust-btn cust-btn-outline cust-btn-sm text-xs self-start sm:self-auto">
                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                            <span>Buka Resolusi Penuh</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- 3. Primary Logo Variations Grid (Hallmark Macrostructure: Ledger) -->
    <section class="space-y-6">
        <div>
            <span class="text-[11px] font-semibold text-[var(--cust-ink-muted)] uppercase tracking-wider">
                Varian Logo &amp; Format Vektor
            </span>
            <h2 class="font-serif text-2xl text-[var(--cust-ink)] tracking-tight mt-0.5">
                Koleksi Format Logo Resmi
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Card A: Primary Horizontal Lockup (Light Background) -->
            <div class="cust-card p-6 space-y-5 bg-[var(--cust-surface)] flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-[var(--cust-rule-border)]">
                        <div>
                            <span class="text-xs font-bold text-[var(--cust-ink)] block">1. Logo Horizontal Utama (Dark on Light)</span>
                            <span class="text-[11px] text-[var(--cust-ink-muted)]">Untuk header storefront, kop nota faktur resmi, dan dokumen putih</span>
                        </div>
                        <span class="cust-badge cust-badge-official text-[10px]">SVG Vector</span>
                    </div>

                    <!-- Canvas Preview -->
                    <div class="p-8 rounded-2xl bg-[var(--cust-paper)] border border-[var(--cust-rule-border)] flex items-center justify-center min-h-[140px]">
                        <img src="{{ asset('images/logo/logo-dark.svg') }}" alt="Rap-iPhone Logo Dark" class="max-h-14 max-w-full">
                    </div>

                    <div class="text-[11px] text-[var(--cust-ink-muted)] space-y-1">
                        <div class="flex justify-between">
                            <span>Format / Skalabilitas:</span>
                            <span class="font-mono text-[var(--cust-ink)] font-semibold">SVG &middot; Skalabilitas Tak Terbatas</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Rekomendasi Latar:</span>
                            <span class="text-[var(--cust-ink)] font-semibold">Putih, Alabaster (#FAF9F6), Kertas Terang</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-[var(--cust-rule-border)] flex items-center justify-end gap-2">
                    <a href="{{ asset('images/logo/logo-dark.svg') }}" target="_blank" class="cust-btn cust-btn-outline cust-btn-sm text-xs">
                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                        <span>Pratinjau</span>
                    </a>
                    <a href="{{ asset('images/logo/logo-dark.svg') }}" download="rap-iphone-logo-dark.svg" class="cust-btn cust-btn-primary cust-btn-sm text-xs">
                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                        <span>Unduh SVG</span>
                    </a>
                </div>
            </div>

            <!-- Card B: Reverse Horizontal Lockup (Dark Background) -->
            <div class="cust-card p-6 space-y-5 bg-[var(--cust-surface)] flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-[var(--cust-rule-border)]">
                        <div>
                            <span class="text-xs font-bold text-[var(--cust-ink)] block">2. Logo Horizontal Reverse (Light on Dark)</span>
                            <span class="text-[11px] text-[var(--cust-ink-muted)]">Untuk announcement topbar hitam, dark mode, dan footer malam</span>
                        </div>
                        <span class="cust-badge cust-badge-promo text-[10px]">SVG Vector</span>
                    </div>

                    <!-- Canvas Preview on Obsidian Slate -->
                    <div class="p-8 rounded-2xl bg-[#161617] border border-[#2C2C2E] flex items-center justify-center min-h-[140px] shadow-inner">
                        <img src="{{ asset('images/logo/logo-light.svg') }}" alt="Rap-iPhone Logo Light" class="max-h-14 max-w-full">
                    </div>

                    <div class="text-[11px] text-[var(--cust-ink-muted)] space-y-1">
                        <div class="flex justify-between">
                            <span>Format / Skalabilitas:</span>
                            <span class="font-mono text-[var(--cust-ink)] font-semibold">SVG &middot; Skalabilitas Tak Terbatas</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Rekomendasi Latar:</span>
                            <span class="text-[var(--cust-ink)] font-semibold">Hitam (#000000), Obsidian (#161617), Titanium Gelap</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-[var(--cust-rule-border)] flex items-center justify-end gap-2">
                    <a href="{{ asset('images/logo/logo-light.svg') }}" target="_blank" class="cust-btn cust-btn-outline cust-btn-sm text-xs">
                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                        <span>Pratinjau</span>
                    </a>
                    <a href="{{ asset('images/logo/logo-light.svg') }}" download="rap-iphone-logo-light.svg" class="cust-btn cust-btn-primary cust-btn-sm text-xs">
                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                        <span>Unduh SVG</span>
                    </a>
                </div>
            </div>

            <!-- Card C: Standalone Logomark Squircle Emblem -->
            <div class="cust-card p-6 space-y-5 bg-[var(--cust-surface)] flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-[var(--cust-rule-border)]">
                        <div>
                            <span class="text-xs font-bold text-[var(--cust-ink)] block">3. Logomark Emblem Squircle</span>
                            <span class="text-[11px] text-[var(--cust-ink-muted)]">Ikon persegi squircle Apple $r=22.5\%$ untuk avatar, profil, &amp; app icon</span>
                        </div>
                        <span class="cust-badge cust-badge-healthy text-[10px]">App Icon</span>
                    </div>

                    <!-- Canvas Preview -->
                    <div class="p-8 rounded-2xl bg-[var(--cust-paper)] border border-[var(--cust-rule-border)] flex items-center justify-center min-h-[140px]">
                        <img src="{{ asset('images/logo/logo-mark.svg') }}" alt="Rap-iPhone Logomark" class="w-20 h-20 shadow-md rounded-2xl">
                    </div>

                    <div class="text-[11px] text-[var(--cust-ink-muted)] space-y-1">
                        <div class="flex justify-between">
                            <span>Rasio / Ukuran:</span>
                            <span class="font-mono text-[var(--cust-ink)] font-semibold">1:1 &middot; 512 &times; 512 px</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Elemen Kunci:</span>
                            <span class="text-[var(--cust-ink)] font-semibold">Monogram "R", Dynamic Capsule, Blue Lens</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-[var(--cust-rule-border)] flex items-center justify-end gap-2">
                    <a href="{{ asset('images/logo/logo-mark.svg') }}" target="_blank" class="cust-btn cust-btn-outline cust-btn-sm text-xs">
                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                        <span>Pratinjau</span>
                    </a>
                    <a href="{{ asset('images/logo/logo-mark.svg') }}" download="rap-iphone-emblem.svg" class="cust-btn cust-btn-primary cust-btn-sm text-xs">
                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                        <span>Unduh Emblem SVG</span>
                    </a>
                </div>
            </div>

            <!-- Card D: Favicon Browser & Apple Touch Icon -->
            <div class="cust-card p-6 space-y-5 bg-[var(--cust-surface)] flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-[var(--cust-rule-border)]">
                        <div>
                            <span class="text-xs font-bold text-[var(--cust-ink)] block">4. Favicon Browser &amp; Apple Touch Icon</span>
                            <span class="text-[11px] text-[var(--cust-ink-muted)]">Ikon tab browser web modern dan bookmark homescreen iOS</span>
                        </div>
                        <span class="cust-badge cust-badge-gray text-[10px]">32x32 &amp; 180x180</span>
                    </div>

                    <!-- Canvas Preview -->
                    <div class="p-8 rounded-2xl bg-[var(--cust-paper)] border border-[var(--cust-rule-border)] flex items-center justify-center gap-6 min-h-[140px]">
                        <div class="text-center space-y-1.5">
                            <img src="{{ asset('images/logo/favicon.svg') }}" alt="Favicon" class="w-8 h-8 mx-auto shadow-xs rounded-md">
                            <span class="text-[10px] text-[var(--cust-ink-muted)] block font-mono">favicon.svg (32px)</span>
                        </div>
                        <div class="text-center space-y-1.5">
                            <img src="{{ asset('images/logo/apple-touch-icon.png') }}" alt="Apple Touch Icon" class="w-14 h-14 mx-auto shadow-md rounded-xl">
                            <span class="text-[10px] text-[var(--cust-ink-muted)] block font-mono">touch-icon (180px)</span>
                        </div>
                    </div>

                    <div class="text-[11px] text-[var(--cust-ink-muted)] space-y-1">
                        <div class="flex justify-between">
                            <span>Implementasi HTML:</span>
                            <span class="font-mono text-[var(--cust-ink)] font-semibold">&lt;link rel="icon" ...&gt;</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Kompatibilitas:</span>
                            <span class="text-[var(--cust-ink)] font-semibold">Safari, Chrome, Firefox, iOS Home</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-[var(--cust-rule-border)] flex items-center justify-end gap-2">
                    <a href="{{ asset('images/logo/favicon.svg') }}" download="favicon.svg" class="cust-btn cust-btn-outline cust-btn-sm text-xs">
                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                        <span>Favicon SVG</span>
                    </a>
                    <a href="{{ asset('images/logo/apple-touch-icon.png') }}" download="apple-touch-icon.png" class="cust-btn cust-btn-primary cust-btn-sm text-xs">
                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                        <span>Touch Icon PNG</span>
                    </a>
                </div>
            </div>

        </div>
    </section>

    <!-- 4. Design Architecture & Geometric Construction -->
    <section class="cust-card p-6 sm:p-8 space-y-6 bg-[var(--cust-surface)]">
        <div class="border-b border-[var(--cust-rule-border)] pb-4">
            <span class="text-[11px] font-semibold text-[var(--cust-accent)] uppercase tracking-wider">
                Anatomi &amp; Prinsip Desain
            </span>
            <h2 class="font-serif text-2xl text-[var(--cust-ink)] tracking-tight mt-0.5">
                Konstruksi Geometri Logo Minimalis
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="p-4 rounded-2xl bg-[var(--cust-paper-subtle)] border border-[var(--cust-rule-border)] space-y-2">
                <div class="w-8 h-8 rounded-xl bg-black text-white flex items-center justify-center font-mono font-bold text-xs">
                    22%
                </div>
                <h3 class="text-xs font-bold text-[var(--cust-ink)]">Curvature Squircle</h3>
                <p class="text-[11px] text-[var(--cust-ink-muted)] leading-relaxed">
                    Radius kelengkungan super-elips matematis standar iOS untuk transisi sudut bodi iPhone yang alami.
                </p>
            </div>

            <div class="p-4 rounded-2xl bg-[var(--cust-paper-subtle)] border border-[var(--cust-rule-border)] space-y-2">
                <div class="w-8 h-8 rounded-xl bg-[var(--cust-ink)] text-white flex items-center justify-center font-bold text-xs">
                    R
                </div>
                <h3 class="text-xs font-bold text-[var(--cust-ink)]">Monogram Arsitektural</h3>
                <p class="text-[11px] text-[var(--cust-ink-muted)] leading-relaxed">
                    Huruf "R" digambar dengan ketebalan garis 8.5px konsisten, terminal membulat, dan busur harmonis.
                </p>
            </div>

            <div class="p-4 rounded-2xl bg-[var(--cust-paper-subtle)] border border-[var(--cust-rule-border)] space-y-2">
                <div class="w-8 h-8 rounded-xl bg-[var(--cust-ink)] text-white flex items-center justify-center">
                    <i data-lucide="minus" class="w-4 h-4"></i>
                </div>
                <h3 class="text-xs font-bold text-[var(--cust-ink)]">Dynamic Capsule</h3>
                <p class="text-[11px] text-[var(--cust-ink-muted)] leading-relaxed">
                    Kapsul horizontal di bagian atas merepresentasikan inovasi Dynamic Island iPhone flagship modern.
                </p>
            </div>

            <div class="p-4 rounded-2xl bg-[var(--cust-paper-subtle)] border border-[var(--cust-rule-border)] space-y-2">
                <div class="w-8 h-8 rounded-xl bg-[#0071E3] text-white flex items-center justify-center">
                    <i data-lucide="circle-dot" class="w-4 h-4"></i>
                </div>
                <h3 class="text-xs font-bold text-[var(--cust-ink)]">Sapphire Signal Blue</h3>
                <p class="text-[11px] text-[var(--cust-ink-muted)] leading-relaxed">
                    Titik aksen safir biru (#0071E3) sebagai lensa kamera optik dan identitas resmi Apple style partner.
                </p>
            </div>
        </div>
    </section>

    <!-- 5. Official Brand Color Palette -->
    <section class="space-y-4">
        <div>
            <span class="text-[11px] font-semibold text-[var(--cust-ink-muted)] uppercase tracking-wider">
                Palet Warna Resmi
            </span>
            <h2 class="font-serif text-2xl text-[var(--cust-ink)] tracking-tight mt-0.5">
                Token Warna Merek
            </h2>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            
            <div class="cust-card overflow-hidden">
                <div class="h-20 bg-[#161617]"></div>
                <div class="p-3.5 space-y-1">
                    <span class="text-xs font-bold text-[var(--cust-ink)] block">Obsidian Titanium</span>
                    <span class="text-[10px] font-mono text-[var(--cust-ink-muted)] block">HEX: #161617</span>
                    <span class="text-[10px] text-[var(--cust-ink-muted)] block">Tinta Utama &middot; Logomark</span>
                </div>
            </div>

            <div class="cust-card overflow-hidden">
                <div class="h-20 bg-[#0071E3]"></div>
                <div class="p-3.5 space-y-1">
                    <span class="text-xs font-bold text-[var(--cust-ink)] block">Apple Signal Blue</span>
                    <span class="text-[10px] font-mono text-[var(--cust-ink-muted)] block">HEX: #0071E3</span>
                    <span class="text-[10px] text-[var(--cust-ink-muted)] block">Aksen Optik &middot; Interaktif</span>
                </div>
            </div>

            <div class="cust-card overflow-hidden">
                <div class="h-20 bg-[#6E6E73]"></div>
                <div class="p-3.5 space-y-1">
                    <span class="text-xs font-bold text-[var(--cust-ink)] block">Cool Slate Titanium</span>
                    <span class="text-[10px] font-mono text-[var(--cust-ink-muted)] block">HEX: #6E6E73</span>
                    <span class="text-[10px] text-[var(--cust-ink-muted)] block">Tagline &middot; Teks Sekunder</span>
                </div>
            </div>

            <div class="cust-card overflow-hidden">
                <div class="h-20 bg-[#FAF9F6] border-b border-[var(--cust-rule-border)]"></div>
                <div class="p-3.5 space-y-1">
                    <span class="text-xs font-bold text-[var(--cust-ink)] block">Alabaster Cotton</span>
                    <span class="text-[10px] font-mono text-[var(--cust-ink-muted)] block">HEX: #FAF9F6</span>
                    <span class="text-[10px] text-[var(--cust-ink-muted)] block">Latar Kertas &middot; Reverse Mark</span>
                </div>
            </div>

        </div>
    </section>

</div>
@endsection

