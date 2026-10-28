<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Rap-iPhone — Authorized Style Partner & Program Tukar Tambah iPhone')</title>
    <meta name="description" content="@yield('meta_description', 'Pusat belanja iPhone Baru Garansi Resmi Indonesia (iBox & Digimap) dan iPhone Second Like New Teruji. Layanan tukar tambah cepat dalam 15 menit & Click & Collect.')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Vite Assets / Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-[#FFFFFF] text-[#1D1D1F] min-h-screen flex flex-col relative selection:bg-[#0071E3] selection:text-white antialiased">

    <!-- =====================================================================
         1. TOP PROMO ANNOUNCEMENT BAR (Digimap Style Ticker)
         ===================================================================== -->
    <div class="bg-[#1D1D1F] text-white text-[11px] sm:text-xs py-2 px-4 relative z-50">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2 mx-auto sm:mx-0 text-center sm:text-left">
                <span class="inline-block w-1.5 h-1.5 rounded-full bg-[#34C759]"></span>
                <p class="font-medium tracking-tight text-white/95">
                    Cicilan 0% hingga 24 Bulan &bull; Gratis Ongkir Berasuransi Seluruh Indonesia &bull; Tukar Tambah Instan 15 Menit
                </p>
            </div>
            <div class="hidden md:flex items-center gap-4 text-[11px] text-white/80 font-medium">
                <a href="{{ url('/trade-in') }}" class="hover:text-white underline-offset-2 hover:underline flex items-center gap-1">
                    <i data-lucide="refresh-cw" class="w-3 h-3 text-[#34C759]"></i>
                    <span>Program Trade-In</span>
                </a>
                <span class="text-white/30">|</span>
                <a href="{{ url('/lacak-pesanan') }}" class="hover:text-white underline-offset-2 hover:underline flex items-center gap-1">
                    <i data-lucide="truck" class="w-3 h-3 text-[#5AC8FA]"></i>
                    <span>Lacak Pesanan</span>
                </a>
            </div>
        </div>
    </div>

    <!-- =====================================================================
         2. STICKY DAYLIGHT FROSTED HEADER (Digimap Clean Navigation)
         ===================================================================== -->
    <header class="glass-navbar sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-[72px] gap-4">
                
                <!-- Left: Wordmark Rap-iPhone & Authorized Style Partner Badge -->
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                        <div class="w-9 h-9 rounded-xl bg-[#1D1D1F] text-white flex items-center justify-center shadow-sm group-hover:bg-[#0071E3] transition-colors">
                            <i data-lucide="smartphone" class="w-4 h-4"></i>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-lg tracking-tight text-[#1D1D1F]">
                                    Rap-iPhone
                                </span>
                                <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-[#F5F5F7] text-[#1D1D1F] border border-[#D2D2D7]">
                                    Authorized Style Partner
                                </span>
                            </div>
                            <span class="text-[10px] tracking-wider text-[#6E6E73] font-medium">
                                Official Retail &amp; Trade-In Center
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Center: Clean Category & Condition Navigation (Desktop) -->
                <nav class="hidden lg:flex items-center gap-1 xl:gap-2">
                    <a href="{{ url('/katalog') }}" class="px-3 py-1.5 rounded-full text-xs xl:text-[13px] font-medium transition-colors {{ request()->is('katalog') && !request('condition') ? 'text-[#1D1D1F] font-semibold bg-[#F5F5F7]' : 'text-[#6E6E73] hover:text-[#1D1D1F] hover:bg-[#F5F5F7]/70' }}">
                        Katalog iPhone
                    </a>
                    <a href="{{ url('/katalog?condition=new') }}" class="px-3 py-1.5 rounded-full text-xs xl:text-[13px] font-medium transition-colors {{ request('condition') === 'new' ? 'text-[#0066CC] font-semibold bg-[#E8F2FF]' : 'text-[#6E6E73] hover:text-[#1D1D1F] hover:bg-[#F5F5F7]/70' }}">
                        Unit Baru (BNIB)
                    </a>
                    <a href="{{ url('/katalog?condition=second') }}" class="px-3 py-1.5 rounded-full text-xs xl:text-[13px] font-medium transition-colors {{ request('condition') === 'second' ? 'text-[#008040] font-semibold bg-[#E8F8EE]' : 'text-[#6E6E73] hover:text-[#1D1D1F] hover:bg-[#F5F5F7]/70' }}">
                        Second Like New
                    </a>
                    <a href="{{ url('/trade-in') }}" class="px-3 py-1.5 rounded-full text-xs xl:text-[13px] font-medium transition-colors {{ request()->is('trade-in*') ? 'text-[#1D1D1F] font-semibold bg-[#F5F5F7]' : 'text-[#6E6E73] hover:text-[#1D1D1F] hover:bg-[#F5F5F7]/70' }}">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#008040]"></span>
                            <span>Tukar Tambah</span>
                        </span>
                    </a>
                    <a href="{{ url('/cek-garansi') }}" class="px-3 py-1.5 rounded-full text-xs xl:text-[13px] font-medium transition-colors {{ request()->is('cek-garansi*') ? 'text-[#1D1D1F] font-semibold bg-[#F5F5F7]' : 'text-[#6E6E73] hover:text-[#1D1D1F] hover:bg-[#F5F5F7]/70' }}">
                        Cek Garansi &amp; IMEI
                    </a>
                </nav>

                <!-- Right: Search Pill, Store Locator (Click & Collect), Shopping Bag, Account -->
                <div class="flex items-center gap-2 sm:gap-2.5">
                    
                    <!-- Search Trigger Pill Button -->
                    <button type="button" data-modal-target="search-modal" class="hidden sm:flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-[#F5F5F7] border border-[#D2D2D7]/80 text-[#6E6E73] hover:text-[#1D1D1F] hover:border-[#86868B] transition-all text-xs" title="Cari Seri iPhone (Ctrl+K)">
                        <i data-lucide="search" class="w-3.5 h-3.5 text-[#1D1D1F]"></i>
                        <span class="max-w-[140px] xl:max-w-[180px] truncate">Cari iPhone 16 Pro, 256GB...</span>
                        <kbd class="px-1.5 py-0.5 text-[10px] rounded bg-white border border-[#D2D2D7] text-[#6E6E73] font-mono">⌘K</kbd>
                    </button>

                    <!-- Mobile Search Icon Button -->
                    <button type="button" data-modal-target="search-modal" class="sm:hidden p-2 rounded-full bg-[#F5F5F7] border border-[#E5E5EA] text-[#1D1D1F]" aria-label="Cari">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </button>

                    <!-- Store Location / Click & Collect Indicator -->
                    <a href="{{ url('/katalog') }}" class="hidden xl:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-[#F5F5F7] border border-[#E5E5EA] hover:border-[#D2D2D7] text-[11px] font-medium text-[#1D1D1F] transition-colors" title="Tersedia Layanan Click & Collect (Ambil di Gerai Toko)">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-[#0066CC]"></i>
                        <span>Click &amp; Collect</span>
                    </a>

                    <!-- Shopping Bag Button with Badge -->
                    <a href="{{ url('/cart') }}" class="relative p-2.5 rounded-full bg-[#F5F5F7] border border-[#E5E5EA] hover:border-[#1D1D1F] transition-all group" aria-label="Keranjang Belanja">
                        <i data-lucide="shopping-bag" class="w-4 h-4 sm:w-5 sm:h-5 text-[#1D1D1F] group-hover:text-[#0066CC] transition-colors"></i>
                        <span id="cart-badge-count" class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-[#0071E3] text-white text-[10px] font-bold flex items-center justify-center border-2 border-white shadow-sm">
                            {{ session('cart_count', 0) }}
                        </span>
                    </a>

                    <!-- User Account / Auth Action -->
                    @auth
                        <div class="relative group">
                            <button type="button" class="flex items-center gap-2 p-1 pl-2.5 rounded-full bg-[#F5F5F7] border border-[#D2D2D7] hover:border-[#1D1D1F] transition-all">
                                <span class="text-xs font-semibold text-[#1D1D1F] max-w-[90px] truncate hidden md:inline">
                                    {{ Auth::user()->name }}
                                </span>
                                <div class="w-7 h-7 rounded-full bg-[#1D1D1F] flex items-center justify-center text-xs font-bold text-white">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            </button>
                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 mt-2 w-52 rounded-2xl glass-card-elevated py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 border border-[#E5E5EA]">
                                <div class="px-4 py-2.5 border-b border-[#E5E5EA]">
                                    <p class="text-[11px] text-[#6E6E73]">Masuk sebagai</p>
                                    <p class="text-xs font-semibold text-[#1D1D1F] truncate">{{ Auth::user()->email }}</p>
                                </div>
                                @if(in_array(Auth::user()->role, ['admin', 'owner']))
                                    <a href="{{ url('/admin/dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-xs text-[#0066CC] hover:bg-[#F5F5F7] font-semibold">
                                        <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                                        <span>Panel Admin</span>
                                    </a>
                                @endif
                                <a href="{{ url('/customer/orders') }}" class="flex items-center gap-2 px-4 py-2 text-xs text-[#1D1D1F] hover:bg-[#F5F5F7] font-medium">
                                    <i data-lucide="package" class="w-3.5 h-3.5 text-[#6E6E73]"></i>
                                    <span>Pesanan Saya</span>
                                </a>
                                <form action="{{ url('/logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-xs text-[#D70015] hover:bg-[#FFEBEA]/60 text-left font-medium">
                                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                                        <span>Keluar</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ url('/login') }}" class="glass-btn glass-btn-dark glass-btn-sm">
                            <i data-lucide="user" class="w-3.5 h-3.5"></i>
                            <span>Masuk</span>
                        </a>
                    @endauth

                    <!-- Mobile Navigation Hamburger Toggle -->
                    <button type="button" id="mobile-nav-toggle" class="lg:hidden p-2 rounded-xl bg-[#F5F5F7] border border-[#E5E5EA] text-[#1D1D1F] hover:bg-[#EBEBED] transition-colors" aria-label="Buka Menu Navigasi" aria-expanded="false">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>

                </div>
            </div>

            <!-- Responsive Daylight Mobile Navigation Drawer -->
            <div id="mobile-nav-drawer" class="hidden lg:hidden pb-4 pt-2 border-t border-[#E5E5EA] space-y-1.5">
                <a href="{{ url('/katalog') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->is('katalog') && !request('condition') ? 'bg-[#1D1D1F] text-white' : 'text-[#1D1D1F] hover:bg-[#F5F5F7]' }}">
                    <span>Katalog Lengkap iPhone</span>
                    <i data-lucide="smartphone" class="w-4 h-4"></i>
                </a>
                <a href="{{ url('/katalog?condition=new') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request('condition') === 'new' ? 'bg-[#E8F2FF] text-[#0066CC]' : 'text-[#1D1D1F] hover:bg-[#F5F5F7]' }}">
                    <span>iPhone Baru Garansi Resmi (BNIB)</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-[#E8F2FF] text-[#0066CC]">Resmi</span>
                </a>
                <a href="{{ url('/katalog?condition=second') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request('condition') === 'second' ? 'bg-[#E8F8EE] text-[#008040]' : 'text-[#1D1D1F] hover:bg-[#F5F5F7]' }}">
                    <span>Second Like New Teruji (Grade A+)</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-[#E8F8EE] text-[#008040]">QC 32 Titik</span>
                </a>
                <a href="{{ url('/trade-in') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->is('trade-in*') ? 'bg-[#1D1D1F] text-white' : 'text-[#1D1D1F] hover:bg-[#F5F5F7]' }}">
                    <span>Tukar Tambah (Trade-In 15 Menit)</span>
                    <i data-lucide="refresh-cw" class="w-4 h-4 text-[#008040]"></i>
                </a>
                <a href="{{ url('/cek-garansi') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->is('cek-garansi*') ? 'bg-[#1D1D1F] text-white' : 'text-[#6E6E73] hover:text-[#1D1D1F] hover:bg-[#F5F5F7]' }}">
                    <span>Cek IMEI &amp; Garansi</span>
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                </a>
                <a href="{{ url('/lacak-pesanan') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->is('lacak-pesanan*') ? 'bg-[#1D1D1F] text-white' : 'text-[#6E6E73] hover:text-[#1D1D1F] hover:bg-[#F5F5F7]' }}">
                    <span>Lacak Pesanan</span>
                    <i data-lucide="truck" class="w-4 h-4"></i>
                </a>
                @auth
                    <a href="{{ url('/customer/orders') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-[#0066CC] hover:bg-[#F5F5F7]">
                        <span>Pesanan &amp; Akun Saya</span>
                        <i data-lucide="package" class="w-4 h-4"></i>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- =====================================================================
         3. GLOBAL INSTANT SEARCH MODAL (Ctrl+K / Cmd+K)
         ===================================================================== -->
    <div id="search-modal" class="glass-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="search-modal-title">
        <div class="glass-modal p-6">
            <div class="flex items-center justify-between pb-4 border-b border-[#E5E5EA]">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#E8F2FF] flex items-center justify-center text-[#0066CC]">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 id="search-modal-title" class="font-bold text-[#1D1D1F] text-base">Cari Produk di Rap-iPhone</h3>
                        <p class="text-xs text-[#6E6E73]">Temukan seri iPhone, kapasitas, atau warna Titanium</p>
                    </div>
                </div>
                <button type="button" data-modal-close class="p-1.5 rounded-lg text-[#6E6E73] hover:text-[#1D1D1F] hover:bg-[#F5F5F7] transition-colors" aria-label="Tutup Modal">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ url('/katalog') }}" method="GET" class="mt-4">
                <div class="relative">
                    <input type="text" name="q" placeholder="Ketik contoh: iPhone 16 Pro, Desert Titanium, 256GB..." class="glass-input pl-10 pr-4 py-3 text-sm w-full" autofocus>
                    <i data-lucide="search" class="w-4 h-4 text-[#6E6E73] absolute left-3.5 top-3.5"></i>
                </div>
            </form>

            <!-- Popular Suggestions Tags -->
            <div class="mt-5">
                <p class="text-[11px] font-bold text-[#86868B] uppercase tracking-wider mb-2.5">Pencarian Populer</p>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ url('/katalog?q=iPhone+16+Pro') }}" class="px-3 py-1.5 rounded-full text-xs font-medium bg-[#F5F5F7] border border-[#E5E5EA] text-[#1D1D1F] hover:border-[#0066CC] hover:text-[#0066CC] transition-colors">
                        iPhone 16 Pro Max
                    </a>
                    <a href="{{ url('/katalog?condition=new') }}" class="px-3 py-1.5 rounded-full text-xs font-medium bg-[#E8F2FF] text-[#0066CC] hover:bg-[#0066CC] hover:text-white transition-colors">
                        BNIB Garansi Resmi iBox / Digimap
                    </a>
                    <a href="{{ url('/katalog?condition=second') }}" class="px-3 py-1.5 rounded-full text-xs font-medium bg-[#E8F8EE] text-[#008040] hover:bg-[#008040] hover:text-white transition-colors">
                        Second Like New Grade A+
                    </a>
                    <a href="{{ url('/trade-in') }}" class="px-3 py-1.5 rounded-full text-xs font-medium bg-[#FFF2EB] text-[#BF4800] hover:bg-[#BF4800] hover:text-white transition-colors">
                        Tukar Tambah 15 Menit
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- =====================================================================
         4. MAIN DYNAMIC CONTENT WRAPPER
         ===================================================================== -->
    <main class="relative z-10 flex-grow">
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="p-4 border border-[#008040]/30 bg-[#E8F8EE] rounded-2xl flex items-center justify-between text-[#008040]">
                    <div class="flex items-center gap-3">
                        <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
                        <span class="text-sm font-semibold">{{ session('success') }}</span>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="p-4 border border-[#D70015]/30 bg-[#FFEBEA] rounded-2xl flex items-center justify-between text-[#D70015]">
                    <div class="flex items-center gap-3">
                        <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0"></i>
                        <span class="text-sm font-semibold">{{ session('error') }}</span>
                    </div>
                </div>
            </div>
        @endif

        @yield('content')
        {{ $slot ?? '' }}
    </main>

    <!-- =====================================================================
         5. DIGIMAP / APPLE AUTHORIZED PARTNER STYLE FOOTER
         ===================================================================== -->
    <footer class="relative z-10 border-t border-[#D2D2D7] bg-[#F5F5F7] text-[#1D1D1F] mt-20 pt-14 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Top Footnote Disclaimer (Apple / Digimap Retail Editorial Note) -->
            <div class="pb-8 mb-10 border-b border-[#D2D2D7] text-[11px] text-[#86868B] leading-relaxed space-y-1.5">
                <p>
                    1. Estimasi nilai tukar tambah (*trade-in*) bervariasi berdasarkan kondisi fisik, kesehatan baterai (*Battery Health*), kapasitas penyimpanan, dan model iPhone yang ditukarkan. Verifikasi akhir dilakukan dalam 15 menit oleh teknisi resmi Rap-iPhone.
                </p>
                <p>
                    2. Seluruh unit Baru (*Brand New In Box*) dijamin 100% original bersegel resmi Apple Authorized Distributor Indonesia (TAM / iBox / Digimap / GDN) dengan IMEI terdaftar resmi di Kementerian Perindustrian RI.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 pb-12 border-b border-[#D2D2D7]">
                
                <!-- Col 1: Store Identity & Omnichannel Trust -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-[#1D1D1F] flex items-center justify-center text-white font-bold">
                            <i data-lucide="smartphone" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <span class="font-bold text-base text-[#1D1D1F] tracking-tight block leading-none">Rap-iPhone Indonesia</span>
                            <span class="text-[10px] font-semibold text-[#6E6E73] uppercase tracking-wider">Apple Premium Style Retail &amp; Trade-In</span>
                        </div>
                    </div>
                    <p class="text-xs text-[#6E6E73] leading-relaxed max-w-sm">
                        Destinasi belanja iPhone Baru Garansi Resmi dan Second Like New Teruji 32 Titik dengan pengalaman omnichannel terbaik: pengiriman berasuransi gratis atau ambil langsung di gerai (*Click &amp; Collect*).
                    </p>
                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-white border border-[#D2D2D7] text-[#1D1D1F]">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5 text-[#0066CC]"></i>
                            <span>IMEI Terdaftar Kemenperin</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-white border border-[#D2D2D7] text-[#1D1D1F]">
                            <i data-lucide="store" class="w-3.5 h-3.5 text-[#008040]"></i>
                            <span>Click &amp; Collect Ready</span>
                        </span>
                    </div>
                </div>

                <!-- Col 2: Belanja Seri iPhone -->
                <div>
                    <h4 class="text-xs font-bold text-[#1D1D1F] uppercase tracking-wider mb-4">Belanja iPhone</h4>
                    <ul class="space-y-2.5 text-xs text-[#6E6E73]">
                        <li><a href="{{ url('/katalog?series=iPhone+16+Series') }}" class="hover:text-[#0066CC] transition-colors">iPhone 16 Pro &amp; 16 Pro Max</a></li>
                        <li><a href="{{ url('/katalog?series=iPhone+16+Series') }}" class="hover:text-[#0066CC] transition-colors">iPhone 16 &amp; 16 Plus</a></li>
                        <li><a href="{{ url('/katalog?series=iPhone+15+Series') }}" class="hover:text-[#0066CC] transition-colors">iPhone 15 Series</a></li>
                        <li><a href="{{ url('/katalog?series=iPhone+14+Series') }}" class="hover:text-[#0066CC] transition-colors">iPhone 14 &amp; 13 Series</a></li>
                        <li><a href="{{ url('/katalog?condition=second') }}" class="text-[#008040] hover:underline font-semibold">Certified Second Like New</a></li>
                    </ul>
                </div>

                <!-- Col 3: Program & Layanan Omnichannel -->
                <div>
                    <h4 class="text-xs font-bold text-[#1D1D1F] uppercase tracking-wider mb-4">Layanan &amp; Program</h4>
                    <ul class="space-y-2.5 text-xs text-[#6E6E73]">
                        <li><a href="{{ url('/trade-in') }}" class="hover:text-[#0066CC] transition-colors font-medium text-[#1D1D1F]">Tukar Tambah (Trade-In 15 Menit)</a></li>
                        <li><a href="{{ url('/cek-garansi') }}" class="hover:text-[#0066CC] transition-colors">Cek Status Garansi &amp; IMEI</a></li>
                        <li><a href="{{ url('/lacak-pesanan') }}" class="hover:text-[#0066CC] transition-colors">Lacak Status &amp; Resi Pesanan</a></li>
                        <li><a href="{{ url('/syarat-garansi') }}" class="hover:text-[#0066CC] transition-colors">Kebijakan Garansi Resmi &amp; Toko</a></li>
                        <li><a href="{{ url('/design-system') }}" class="hover:text-[#0066CC] transition-colors font-mono text-[11px]">Design System v2.0</a></li>
                    </ul>
                </div>

                <!-- Col 4: Hubungi Kami & Pembayaran -->
                <div>
                    <h4 class="text-xs font-bold text-[#1D1D1F] uppercase tracking-wider mb-4">Bantuan &amp; Pembayaran</h4>
                    <ul class="space-y-3 text-xs text-[#6E6E73]">
                        <li class="flex items-start gap-2.5">
                            <i data-lucide="message-circle" class="w-4 h-4 text-[#008040] shrink-0 mt-0.5"></i>
                            <div>
                                <p class="text-[#1D1D1F] font-semibold">Concierge WhatsApp</p>
                                <p class="text-[11px]">0812-3456-7890 ( Setiap Hari 09.00–21.00 )</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <i data-lucide="credit-card" class="w-4 h-4 text-[#0066CC] shrink-0 mt-0.5"></i>
                            <div>
                                <p class="text-[#1D1D1F] font-semibold">Midtrans Official Partner</p>
                                <p class="text-[11px]">QRIS, BCA/Mandiri/BNI/BRI VA, Kartu Kredit Cicilan 0%</p>
                            </div>
                        </li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Copyright -->
            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-[#6E6E73] gap-4">
                <p>Hak Cipta &copy; {{ date('Y') }} Rap-iPhone Indonesia. Seluruh hak cipta dilindungi undang-undang.</p>
                <div class="flex flex-wrap items-center gap-5">
                    <a href="{{ url('/syarat-garansi') }}" class="hover:text-[#1D1D1F] transition-colors">Kebijakan Privasi</a>
                    <a href="{{ url('/syarat-garansi') }}" class="hover:text-[#1D1D1F] transition-colors">Syarat &amp; Ketentuan</a>
                    <a href="{{ url('/cek-garansi') }}" class="hover:text-[#1D1D1F] transition-colors">Verifikasi Nota Resmi</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Initialize Lucide Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
