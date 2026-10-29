<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Rap-iPhone — Authorized Style Partner & Program Tukar Tambah iPhone')</title>
    <meta name="description" content="@yield('meta_description', 'Pusat belanja iPhone Baru Garansi Resmi Indonesia (iBox & Digimap) dan iPhone Second Like New Teruji. Layanan tukar tambah cepat dalam 15 menit & Click & Collect.')">

    <!-- Fonts: Sentient (Display) & Hanken Grotesk (Body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://api.fontshare.com/v2/css?f[]=sentient@300,400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&family=Geist+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Favicon & Apple Touch Icon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo/favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo/apple-touch-icon.png') }}">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Vite Assets / Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="cust-shell selection:bg-[var(--cust-signal)] selection:text-white antialiased">

    <!-- =====================================================================
         1. TOP PROMO ANNOUNCEMENT BAR (Hallmark 1-Sentence Announcement)
         ===================================================================== -->
    <div class="cust-topbar">
        <div class="cust-page flex flex-wrap items-center justify-between gap-2">
            <p class="font-normal text-white/90">
                Cicilan 0% hingga 24 bulan &bull; Gratis ongkir berasuransi ke seluruh Indonesia &bull; Tukar tambah instan 15 menit
            </p>
            <div class="hidden md:flex items-center gap-4 text-white/80">
                <a href="{{ url('/trade-in') }}" class="hover:text-white transition-colors">Program Tukar Tambah</a>
                <span class="text-white/20">|</span>
                <a href="{{ url('/lacak-pesanan') }}" class="hover:text-white transition-colors">Lacak Pesanan</a>
            </div>
        </div>
    </div>

    <!-- =====================================================================
         2. STICKY DAYLIGHT FROSTED HEADER (Hallmark Studio Navigation)
         ===================================================================== -->
    <header class="cust-navbar glass-navbar">
        <div class="cust-page">
            <div class="cust-navbar-inner">
                
                <!-- Left: Wordmark & Partner Badge -->
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ url('/') }}" class="flex items-center gap-2.5 group text-decoration-none" aria-label="Beranda Rap-iPhone">
                        <img src="{{ asset('images/logo/logo-mark.svg') }}" alt="Rap-iPhone Logomark" class="w-8 h-8 rounded-lg shadow-xs transform group-hover:scale-105 transition-transform duration-150">
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <span class="font-serif text-lg font-light tracking-tight text-[var(--cust-ink)]">
                                    Rap-iPhone
                                </span>
                                <span class="hidden sm:inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-[var(--cust-paper-subtle)] text-[var(--cust-ink-muted)] border border-[var(--cust-rule-border)]">
                                    Authorized Retail
                                </span>
                            </div>
                            <span class="text-[10px] text-[var(--cust-ink-faint)] leading-none">
                                Official &amp; Trade-In Studio
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Center: Category Navigation -->
                <nav class="hidden lg:flex items-center gap-1 xl:gap-2">
                    <a href="{{ url('/katalog') }}" class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ request()->is('katalog') && !request('condition') ? 'text-[var(--cust-ink)] bg-[var(--cust-paper-subtle)]' : 'text-[var(--cust-ink-muted)] hover:text-[var(--cust-ink)]' }}">
                        Katalog iPhone
                    </a>
                    <a href="{{ url('/katalog?condition=new') }}" class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ request('condition') === 'new' ? 'text-[var(--cust-tone-official-ink)] bg-[var(--cust-tone-official-bg)]' : 'text-[var(--cust-ink-muted)] hover:text-[var(--cust-ink)]' }}">
                        Unit Baru (BNIB)
                    </a>
                    <a href="{{ url('/katalog?condition=second') }}" class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ request('condition') === 'second' ? 'text-[var(--cust-tone-healthy-ink)] bg-[var(--cust-tone-healthy-bg)]' : 'text-[var(--cust-ink-muted)] hover:text-[var(--cust-ink)]' }}">
                        Second Like New
                    </a>
                    <a href="{{ url('/trade-in') }}" class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ request()->is('trade-in*') ? 'text-[var(--cust-ink)] bg-[var(--cust-paper-subtle)]' : 'text-[var(--cust-ink-muted)] hover:text-[var(--cust-ink)]' }}">
                        Tukar Tambah
                    </a>
                    <a href="{{ url('/cek-garansi') }}" class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ request()->is('cek-garansi*') ? 'text-[var(--cust-ink)] bg-[var(--cust-paper-subtle)]' : 'text-[var(--cust-ink-muted)] hover:text-[var(--cust-ink)]' }}">
                        Cek Garansi &amp; IMEI
                    </a>
                </nav>

                <!-- Right: Search, Cart, Account -->
                <div class="flex items-center gap-2 sm:gap-2.5">
                    
                    <!-- Search Trigger Pill Button -->
                    <button type="button" data-modal-target="search-modal" class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded border border-[var(--cust-rule-border)] bg-[var(--cust-surface)] text-[var(--cust-ink-muted)] hover:text-[var(--cust-ink)] hover:border-[var(--cust-ink-muted)] transition-colors text-xs" title="Cari Seri iPhone (⌘K)">
                        <i data-lucide="search" class="w-3.5 h-3.5 text-[var(--cust-ink-muted)]"></i>
                        <span class="max-w-[140px] truncate">Cari iPhone...</span>
                        <kbd class="px-1 py-0.5 text-[10px] rounded bg-[var(--cust-paper-subtle)] border border-[var(--cust-rule-border)] font-mono">⌘K</kbd>
                    </button>

                    <!-- Mobile Search Icon Button -->
                    <button type="button" data-modal-target="search-modal" class="sm:hidden p-2 rounded border border-[var(--cust-rule-border)] bg-[var(--cust-surface)] text-[var(--cust-ink)]" aria-label="Cari">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </button>

                    <!-- Click & Collect Indicator -->
                    <a href="{{ url('/katalog') }}" class="hidden xl:inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded border border-[var(--cust-rule-border)] text-[11px] font-medium text-[var(--cust-ink)] bg-[var(--cust-surface)]" title="Layanan Click & Collect Gerai Rap-iPhone">
                        <i data-lucide="store" class="w-3.5 h-3.5 text-[var(--cust-signal)]"></i>
                        <span>Click &amp; Collect</span>
                    </a>

                    <!-- Shopping Bag Button -->
                    <a href="{{ url('/cart') }}" class="relative p-2 rounded border border-[var(--cust-rule-border)] bg-[var(--cust-surface)] hover:border-[var(--cust-ink)] text-[var(--cust-ink)] transition-colors" aria-label="Keranjang Belanja">
                        <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                        <span id="cart-badge-count" class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-[var(--cust-signal)] text-white text-[9px] font-bold flex items-center justify-center cust-tabular">
                            {{ session('cart_count', 0) }}
                        </span>
                    </a>

                    <!-- User Account / Auth Dropdown -->
                    @auth
                        <div class="relative group">
                            <button type="button" class="flex items-center gap-2 p-1 pl-2 rounded border border-[var(--cust-rule-border)] bg-[var(--cust-surface)] hover:border-[var(--cust-ink)] transition-colors">
                                <span class="text-xs font-medium text-[var(--cust-ink)] max-w-[80px] truncate hidden md:inline">
                                    {{ Auth::user()->name }}
                                </span>
                                <div class="w-6 h-6 rounded bg-[var(--cust-ink)] flex items-center justify-center text-[11px] font-medium text-white">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 rounded bg-[var(--cust-surface)] border border-[var(--cust-rule-border)] shadow-lg py-1.5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-50">
                                <div class="px-3 py-2 border-b border-[var(--cust-rule-border)]">
                                    <p class="text-[10px] text-[var(--cust-ink-faint)]">Akun Masuk</p>
                                    <p class="text-xs font-medium text-[var(--cust-ink)] truncate">{{ Auth::user()->email }}</p>
                                </div>
                                @if(Auth::user()->isOwner())
                                    <a href="{{ route('owner.dashboard') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-[var(--cust-tone-promo-ink)] hover:bg-[var(--cust-paper-subtle)] font-medium">
                                        <i data-lucide="crown" class="w-3.5 h-3.5"></i>
                                        <span>Portal Eksekutif</span>
                                    </a>
                                @elseif(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-[var(--cust-signal)] hover:bg-[var(--cust-paper-subtle)] font-medium">
                                        <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                                        <span>Panel Admin</span>
                                    </a>
                                @endif
                                <a href="{{ url('/customer/orders') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-[var(--cust-ink)] hover:bg-[var(--cust-paper-subtle)]">
                                    <i data-lucide="package" class="w-3.5 h-3.5 text-[var(--cust-ink-muted)]"></i>
                                    <span>Pesanan Saya</span>
                                </a>
                                @php
                                    $logoutRoute = route('logout');
                                    if (Auth::user()->isOwner() && Route::has('owner.logout')) {
                                        $logoutRoute = route('owner.logout');
                                    } elseif (Auth::user()->isAdmin() && Route::has('admin.logout')) {
                                        $logoutRoute = route('admin.logout');
                                    }
                                @endphp
                                <form action="{{ $logoutRoute }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-[var(--cust-tone-alert-ink)] hover:bg-[var(--cust-paper-subtle)] text-left">
                                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                                        <span>Keluar</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('login') }}" class="cust-btn cust-btn-secondary text-xs py-1.5 px-3">
                                <span>Masuk</span>
                            </a>
                            <a href="{{ route('register') }}" class="hidden sm:inline-flex cust-btn cust-btn-outline text-xs py-1.5 px-3">
                                <span>Daftar</span>
                            </a>
                        </div>
                    @endauth

                    <!-- Mobile Drawer Toggle -->
                    <button type="button" id="mobile-nav-toggle" class="lg:hidden p-2 rounded border border-[var(--cust-rule-border)] bg-[var(--cust-surface)] text-[var(--cust-ink)]" aria-label="Buka Menu">
                        <i data-lucide="menu" class="w-4 h-4"></i>
                    </button>

                </div>
            </div>

            <!-- Mobile Navigation Drawer -->
            <div id="mobile-nav-drawer" class="hidden lg:hidden pb-4 pt-2 border-t border-[var(--cust-rule-border)] space-y-1">
                <a href="{{ url('/katalog') }}" class="flex items-center justify-between px-3 py-2 rounded text-xs font-medium {{ request()->is('katalog') && !request('condition') ? 'bg-[var(--cust-ink)] text-white' : 'text-[var(--cust-ink)] hover:bg-[var(--cust-paper-subtle)]' }}">
                    <span>Katalog Lengkap iPhone</span>
                    <i data-lucide="smartphone" class="w-3.5 h-3.5"></i>
                </a>
                <a href="{{ url('/katalog?condition=new') }}" class="flex items-center justify-between px-3 py-2 rounded text-xs font-medium {{ request('condition') === 'new' ? 'bg-[var(--cust-tone-official-bg)] text-[var(--cust-tone-official-ink)]' : 'text-[var(--cust-ink)] hover:bg-[var(--cust-paper-subtle)]' }}">
                    <span>iPhone Baru Garansi Resmi (BNIB)</span>
                    <span class="text-[10px] text-[var(--cust-tone-official-ink)]">Resmi</span>
                </a>
                <a href="{{ url('/katalog?condition=second') }}" class="flex items-center justify-between px-3 py-2 rounded text-xs font-medium {{ request('condition') === 'second' ? 'bg-[var(--cust-tone-healthy-bg)] text-[var(--cust-tone-healthy-ink)]' : 'text-[var(--cust-ink)] hover:bg-[var(--cust-paper-subtle)]' }}">
                    <span>Second Like New Teruji (Grade A+)</span>
                    <span class="text-[10px] text-[var(--cust-tone-healthy-ink)]">QC 32 Titik</span>
                </a>
                <a href="{{ url('/trade-in') }}" class="flex items-center justify-between px-3 py-2 rounded text-xs font-medium {{ request()->is('trade-in*') ? 'bg-[var(--cust-ink)] text-white' : 'text-[var(--cust-ink)] hover:bg-[var(--cust-paper-subtle)]' }}">
                    <span>Tukar Tambah 15 Menit</span>
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                </a>
                <a href="{{ url('/cek-garansi') }}" class="flex items-center justify-between px-3 py-2 rounded text-xs font-medium {{ request()->is('cek-garansi*') ? 'bg-[var(--cust-ink)] text-white' : 'text-[var(--cust-ink-muted)] hover:bg-[var(--cust-paper-subtle)]' }}">
                    <span>Cek IMEI &amp; Garansi</span>
                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                </a>
                <a href="{{ url('/lacak-pesanan') }}" class="flex items-center justify-between px-3 py-2 rounded text-xs font-medium {{ request()->is('lacak-pesanan*') ? 'bg-[var(--cust-ink)] text-white' : 'text-[var(--cust-ink-muted)] hover:bg-[var(--cust-paper-subtle)]' }}">
                    <span>Lacak Pesanan</span>
                    <i data-lucide="truck" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        </div>
    </header>

    <!-- =====================================================================
         3. INSTANT SEARCH MODAL (⌘K)
         ===================================================================== -->
    <div id="search-modal" class="glass-modal-backdrop hidden fixed inset-0 z-50 bg-black/40 flex items-start justify-center pt-20 px-4" role="dialog" aria-modal="true" aria-labelledby="search-modal-title">
        <div class="bg-[var(--cust-surface)] border border-[var(--cust-rule-border)] rounded max-w-lg w-full p-5 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-[var(--cust-rule-border)]">
                <div>
                    <h3 id="search-modal-title" class="font-serif text-base font-light text-[var(--cust-ink)]">Pencarian Seri iPhone</h3>
                    <p class="text-xs text-[var(--cust-ink-muted)]">Cari berdasarkan seri, warna titanium, atau kapasitas</p>
                </div>
                <button type="button" data-modal-close class="p-1 rounded text-[var(--cust-ink-muted)] hover:text-[var(--cust-ink)]" aria-label="Tutup">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form action="{{ url('/katalog') }}" method="GET" class="mt-4">
                <div class="relative">
                    <input type="text" name="q" placeholder="Contoh: iPhone 16 Pro Desert Titanium 256GB..." class="cust-input pl-9" autofocus>
                    <i data-lucide="search" class="w-4 h-4 text-[var(--cust-ink-muted)] absolute left-3 top-3"></i>
                </div>
            </form>

            <div class="mt-4 pt-3 border-t border-[var(--cust-rule-border)]">
                <p class="text-[11px] font-medium text-[var(--cust-ink-muted)] mb-2">Pencarian Populer</p>
                <div class="flex flex-wrap gap-1.5">
                    <a href="{{ url('/katalog?q=iPhone+16+Pro') }}" class="px-2.5 py-1 rounded border border-[var(--cust-rule-border)] text-xs text-[var(--cust-ink)] hover:border-[var(--cust-ink)]">
                        iPhone 16 Pro Max
                    </a>
                    <a href="{{ url('/katalog?condition=new') }}" class="px-2.5 py-1 rounded border border-[var(--cust-rule-border)] text-xs text-[var(--cust-ink)] hover:border-[var(--cust-ink)]">
                        Baru Resmi iBox
                    </a>
                    <a href="{{ url('/katalog?condition=second') }}" class="px-2.5 py-1 rounded border border-[var(--cust-rule-border)] text-xs text-[var(--cust-ink)] hover:border-[var(--cust-ink)]">
                        Second Grade A+
                    </a>
                    <a href="{{ url('/trade-in') }}" class="px-2.5 py-1 rounded border border-[var(--cust-rule-border)] text-xs text-[var(--cust-ink)] hover:border-[var(--cust-ink)]">
                        Tukar Tambah 15 Menit
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- =====================================================================
         4. MAIN CONTENT
         ===================================================================== -->
    <main class="flex-grow">
        @if(session('success'))
            <div class="cust-page mt-4">
                <div class="p-3 border border-[var(--cust-tone-healthy-ink)]/20 bg-[var(--cust-tone-healthy-bg)] rounded text-[var(--cust-tone-healthy-ink)] text-xs font-medium flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="cust-page mt-4">
                <div class="p-3 border border-[var(--cust-tone-alert-ink)]/20 bg-[var(--cust-tone-alert-bg)] rounded text-[var(--cust-tone-alert-ink)] text-xs font-medium flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @yield('content')
        {{ $slot ?? '' }}
    </main>

    <!-- =====================================================================
         5. EDITORIAL FOOTER (Hallmark Job: Buy Anatomy)
         ===================================================================== -->
    <footer class="border-t border-[var(--cust-rule-border)] bg-[var(--cust-paper-subtle)] text-[var(--cust-ink)] mt-24 pt-12 pb-10">
        <div class="cust-page">
            
            <!-- Footnote Terms -->
            <div class="pb-6 mb-8 border-b border-[var(--cust-rule-border)] text-[11px] text-[var(--cust-ink-muted)] leading-relaxed space-y-1">
                <p>1. Penaksiran harga tukar tambah (*trade-in*) berlaku berdasarkan hasil inspeksi fisik dan fungsional 32-titik oleh teknisi toko Rap-iPhone.</p>
                <p>2. Seluruh unit Baru (BNIB) bergaransi resmi Apple Authorized Distributor Indonesia (TAM / iBox / Digimap / GDN) dengan IMEI terdaftar Kemenperin.</p>
            </div>

            <!-- Grid Columns -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 pb-10 border-b border-[var(--cust-rule-border)]">
                
                <!-- Col 1: Store Credentials -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('images/logo/logo-mark.svg') }}" alt="Rap-iPhone Logomark" class="w-7 h-7 rounded-md">
                        <span class="font-serif text-base font-light text-[var(--cust-ink)]">Rap-iPhone Indonesia</span>
                    </div>
                    <p class="text-xs text-[var(--cust-ink-muted)] leading-relaxed">
                        Etalase resmi belanja iPhone Baru Garansi Resmi dan Pre-Owned Teruji dengan opsi pengiriman berasuransi serta *Click &amp; Collect* di gerai fisik.
                    </p>
                    <div class="flex items-center gap-2 pt-1 text-[11px] text-[var(--cust-ink-muted)]">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-[var(--cust-signal)]"></i>
                        <span>IMEI Terdaftar Kemenperin &bull; QC 32 Titik</span>
                    </div>
                </div>

                <!-- Col 2: Seri iPhone -->
                <div>
                    <p class="text-xs font-semibold text-[var(--cust-ink)] mb-3">Koleksi iPhone</p>
                    <ul class="space-y-2 text-xs text-[var(--cust-ink-muted)]">
                        <li><a href="{{ url('/katalog?series=iPhone+16+Series') }}" class="hover:text-[var(--cust-ink)]">iPhone 16 Pro &amp; 16 Pro Max</a></li>
                        <li><a href="{{ url('/katalog?series=iPhone+16+Series') }}" class="hover:text-[var(--cust-ink)]">iPhone 16 &amp; 16 Plus</a></li>
                        <li><a href="{{ url('/katalog?series=iPhone+15+Series') }}" class="hover:text-[var(--cust-ink)]">iPhone 15 Series</a></li>
                        <li><a href="{{ url('/katalog?condition=second') }}" class="hover:text-[var(--cust-ink)]">Certified Pre-Owned (Grade A+)</a></li>
                    </ul>
                </div>

                <!-- Col 3: Layanan Toko -->
                <div>
                    <p class="text-xs font-semibold text-[var(--cust-ink)] mb-3">Layanan Toko</p>
                    <ul class="space-y-2 text-xs text-[var(--cust-ink-muted)]">
                        <li><a href="{{ url('/trade-in') }}" class="hover:text-[var(--cust-ink)]">Tukar Tambah 15 Menit</a></li>
                        <li><a href="{{ url('/cek-garansi') }}" class="hover:text-[var(--cust-ink)]">Cek Status IMEI &amp; Garansi</a></li>
                        <li><a href="{{ url('/lacak-pesanan') }}" class="hover:text-[var(--cust-ink)]">Lacak Resi Pengiriman</a></li>
                        <li><a href="{{ url('/syarat-garansi') }}" class="hover:text-[var(--cust-ink)]">Ketentuan Garansi Toko</a></li>
                        <li><a href="{{ url('/brand') }}" class="hover:text-[var(--cust-ink)]">Identitas Merek &amp; Logo</a></li>
                    </ul>
                </div>

                <!-- Col 4: Pembayaran & Kontak -->
                <div>
                    <p class="text-xs font-semibold text-[var(--cust-ink)] mb-3">Pembayaran &amp; Bantuan</p>
                    <ul class="space-y-2 text-xs text-[var(--cust-ink-muted)]">
                        <li class="text-[var(--cust-ink)]">Midtrans Payment Gateway (QRIS, VA, CC 0%)</li>
                        <li class="text-[var(--cust-ink-muted)]">Concierge WA: 0812-3456-7890</li>
                        <li class="text-[var(--cust-ink-muted)]">Jam Operasional: 09.00 – 21.00 WIB</li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Copyright -->
            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-[var(--cust-ink-muted)] gap-3">
                <p>&copy; {{ date('Y') }} Rap-iPhone Indonesia. Hak cipta dilindungi undang-undang.</p>
                <div class="flex items-center gap-4">
                    <a href="{{ url('/syarat-garansi') }}" class="hover:text-[var(--cust-ink)]">Kebijakan Privasi</a>
                    <a href="{{ url('/syarat-garansi') }}" class="hover:text-[var(--cust-ink)]">Syarat &amp; Ketentuan</a>
                    <a href="{{ url('/cek-garansi') }}" class="hover:text-[var(--cust-ink)]">Validasi Nota</a>
                </div>
            </div>

        </div>
    </footer>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide) {
                window.lucide.createIcons();
            }

            // Mobile nav toggle
            const navToggle = document.getElementById('mobile-nav-toggle');
            const navDrawer = document.getElementById('mobile-nav-drawer');
            if (navToggle && navDrawer) {
                navToggle.addEventListener('click', function () {
                    navDrawer.classList.toggle('hidden');
                });
            }

            // Search modal
            const searchModal = document.getElementById('search-modal');
            const searchTriggers = document.querySelectorAll('[data-modal-target="search-modal"]');
            const searchCloses = document.querySelectorAll('[data-modal-close]');

            function openSearch() {
                if (searchModal) {
                    searchModal.classList.remove('hidden');
                    const input = searchModal.querySelector('input');
                    if (input) input.focus();
                }
            }

            function closeSearch() {
                if (searchModal) {
                    searchModal.classList.add('hidden');
                }
            }

            searchTriggers.forEach(btn => btn.addEventListener('click', openSearch));
            searchCloses.forEach(btn => btn.addEventListener('click', closeSearch));

            // Keyboard shortcut (Cmd+K / Ctrl+K)
            document.addEventListener('keydown', function (e) {
                if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                    e.preventDefault();
                    if (searchModal && !searchModal.classList.contains('hidden')) {
                        closeSearch();
                    } else {
                        openSearch();
                    }
                }
                if (e.key === 'Escape' && searchModal && !searchModal.classList.contains('hidden')) {
                    closeSearch();
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
