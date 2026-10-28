<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Rap-iPhone — Authorized Style Store & Program Tukar Tambah iPhone')</title>
    <meta name="description" content="@yield('meta_description', 'Pusat belanja iPhone Baru Garansi Resmi Indonesia (iBox & Digimap) dan iPhone Second Like New Teruji. Layanan tukar tambah cepat dalam 15 menit.')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Vite Assets / Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-[#0B0C10] text-[#F5F5F7] min-h-screen flex flex-col relative selection:bg-[#0071E3] selection:text-white">

    <!-- Ambient Glowing Background Atmosphere -->
    <div class="ambient-glow-wrapper" aria-hidden="true">
        <div class="ambient-orb ambient-orb-blue"></div>
        <div class="ambient-orb ambient-orb-purple"></div>
        <div class="ambient-orb ambient-orb-cyan"></div>
    </div>

    <!-- Sticky Frosted Glass Navbar -->
    <header class="glass-navbar border-b border-white/10 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- Logo & Brand Identity -->
                <div class="flex items-center gap-3">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-white/10 to-white/5 border border-white/20 flex items-center justify-center shadow-lg group-hover:border-[#0071E3] transition-all">
                            <i data-lucide="smartphone" class="w-5 h-5 text-[#E5E5EA] group-hover:text-[#0071E3] transition-colors"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-extrabold text-lg tracking-tight bg-gradient-to-r from-white via-[#E5E5EA] to-[#8E8E93] bg-clip-text text-transparent group-hover:from-white group-hover:to-[#0071E3] transition-all">
                                Rap-iPhone
                            </span>
                            <span class="text-[10px] tracking-widest text-[#8E8E93] uppercase font-semibold">
                                Retail & Trade-In
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links (Desktop) -->
                <nav class="hidden md:flex items-center gap-1 lg:gap-2">
                    <a href="{{ url('/katalog') }}" class="px-3.5 py-2 rounded-lg text-sm font-medium text-[#8E8E93] hover:text-white hover:bg-white/5 transition-all {{ request()->is('katalog*') ? 'text-white bg-white/10' : '' }}">
                        Katalog iPhone
                    </a>
                    <a href="{{ url('/trade-in') }}" class="px-3.5 py-2 rounded-lg text-sm font-medium text-[#8E8E93] hover:text-white hover:bg-white/5 transition-all {{ request()->is('trade-in*') ? 'text-white bg-white/10' : '' }}">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-[#34C759] animate-pulse"></span>
                            Tukar Tambah
                        </span>
                    </a>
                    <a href="{{ url('/cek-garansi') }}" class="px-3.5 py-2 rounded-lg text-sm font-medium text-[#8E8E93] hover:text-white hover:bg-white/5 transition-all {{ request()->is('cek-garansi*') ? 'text-white bg-white/10' : '' }}">
                        Cek IMEI & Garansi
                    </a>
                    <a href="{{ url('/lacak-pesanan') }}" class="px-3.5 py-2 rounded-lg text-sm font-medium text-[#8E8E93] hover:text-white hover:bg-white/5 transition-all {{ request()->is('lacak-pesanan*') ? 'text-white bg-white/10' : '' }}">
                        Lacak Pesanan
                    </a>
                </nav>

                <!-- Action Controls (Search, Cart, Profile) -->
                <div class="flex items-center gap-2 sm:gap-3">
                    
                    <!-- Search Trigger Modal Button -->
                    <button type="button" data-modal-target="search-modal" class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-[#8E8E93] hover:text-white hover:border-white/20 hover:bg-white/10 transition-all text-xs" title="Cari Seri iPhone (Ctrl+K)">
                        <i data-lucide="search" class="w-3.5 h-3.5"></i>
                        <span>Cari unit iPhone...</span>
                        <kbd class="px-1.5 py-0.5 text-[10px] rounded bg-white/10 border border-white/15 text-white/70 font-mono">⌘K</kbd>
                    </button>

                    <!-- Mobile Search Icon Button -->
                    <button type="button" data-modal-target="search-modal" class="sm:hidden p-2 rounded-full bg-white/5 border border-white/10 text-[#8E8E93] hover:text-white" aria-label="Cari">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </button>

                    <!-- Cart Floating Pill Badge -->
                    <a href="{{ url('/cart') }}" class="relative p-2.5 rounded-full bg-white/5 border border-white/10 hover:border-[#0071E3] hover:bg-white/10 transition-all group" aria-label="Keranjang Belanja">
                        <i data-lucide="shopping-bag" class="w-5 h-5 text-[#E5E5EA] group-hover:text-[#0071E3] transition-colors"></i>
                        <span id="cart-badge-count" class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-[#0071E3] text-white text-[11px] font-bold flex items-center justify-center border-2 border-[#0B0C10] shadow-sm animate-pulse">
                            {{ session('cart_count', 0) }}
                        </span>
                    </a>

                    <!-- User Profile / Auth Action -->
                    @auth
                        <div class="relative group">
                            <button type="button" class="flex items-center gap-2 p-1.5 pl-3 rounded-full bg-white/5 border border-white/10 hover:border-white/20 transition-all">
                                <span class="text-xs font-semibold text-[#E5E5EA] max-w-[100px] truncate hidden md:inline">
                                    {{ Auth::user()->name }}
                                </span>
                                <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-[#0071E3] to-[#34C759] flex items-center justify-center text-xs font-bold text-white shadow-md">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            </button>
                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 mt-2 w-48 rounded-2xl glass-card-elevated py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <div class="px-4 py-2 border-b border-white/10">
                                    <p class="text-xs text-[#8E8E93]">Masuk sebagai</p>
                                    <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->email }}</p>
                                </div>
                                @if(in_array(Auth::user()->role, ['admin', 'owner']))
                                    <a href="{{ url('/admin/dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-xs text-[#0071E3] hover:bg-white/5 font-semibold">
                                        <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                                        Panel Admin
                                    </a>
                                @endif
                                <a href="{{ url('/customer/orders') }}" class="flex items-center gap-2 px-4 py-2 text-xs text-[#E5E5EA] hover:bg-white/5">
                                    <i data-lucide="package" class="w-3.5 h-3.5"></i>
                                    Pesanan Saya
                                </a>
                                <form action="{{ url('/logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-xs text-[#FF453A] hover:bg-white/5 text-left">
                                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ url('/login') }}" class="glass-btn glass-btn-primary glass-btn-sm">
                            <i data-lucide="user" class="w-3.5 h-3.5"></i>
                            <span>Masuk</span>
                        </a>
                    @endauth

                    <!-- Mobile Navigation Hamburger Toggle -->
                    <button type="button" id="mobile-nav-toggle" class="md:hidden p-2 rounded-xl bg-white/5 border border-white/10 text-[#E5E5EA] hover:text-white hover:bg-white/10 transition-colors" aria-label="Buka Menu Navigasi" aria-expanded="false">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>

                </div>
            </div>

            <!-- Responsive Frosted Glass Mobile Navigation Drawer -->
            <div id="mobile-nav-drawer" class="hidden md:hidden pb-4 pt-2 border-t border-white/10 space-y-1.5">
                <a href="{{ url('/katalog') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->is('katalog*') ? 'bg-[#0071E3]/20 text-white border border-[#0071E3]/40' : 'text-[#E5E5EA] hover:bg-white/5' }}">
                    <span>Katalog iPhone</span>
                    <i data-lucide="smartphone" class="w-4 h-4 text-[#5AC8FA]"></i>
                </a>
                <a href="{{ url('/trade-in') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->is('trade-in*') ? 'bg-[#34C759]/20 text-white border border-[#34C759]/40' : 'text-[#E5E5EA] hover:bg-white/5' }}">
                    <span>Tukar Tambah (Trade-In 15 Menit)</span>
                    <span class="w-2 h-2 rounded-full bg-[#34C759]"></span>
                </a>
                <a href="{{ url('/cek-garansi') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->is('cek-garansi*') ? 'bg-white/10 text-white' : 'text-[#8E8E93] hover:text-white hover:bg-white/5' }}">
                    <span>Cek IMEI & Garansi</span>
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                </a>
                <a href="{{ url('/lacak-pesanan') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold {{ request()->is('lacak-pesanan*') ? 'bg-white/10 text-white' : 'text-[#8E8E93] hover:text-white hover:bg-white/5' }}">
                    <span>Lacak Pesanan</span>
                    <i data-lucide="truck" class="w-4 h-4"></i>
                </a>
                @auth
                    <a href="{{ url('/customer/orders') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-[#5AC8FA] hover:bg-white/5">
                        <span>Pesanan & Akun Saya</span>
                        <i data-lucide="package" class="w-4 h-4"></i>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Global Search Modal (Ctrl+K) -->
    <div id="search-modal" class="glass-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="search-modal-title">
        <div class="glass-modal p-6">
            <div class="flex items-center justify-between pb-4 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#0071E3]/20 flex items-center justify-center text-[#0071E3]">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 id="search-modal-title" class="font-semibold text-white text-base">Cari iPhone Impian</h3>
                        <p class="text-xs text-[#8E8E93]">Cari berdasarkan model, warna titanium, atau seri</p>
                    </div>
                </div>
                <button type="button" data-modal-close class="p-1.5 rounded-lg text-[#8E8E93] hover:text-white hover:bg-white/10 transition-colors" aria-label="Tutup Modal">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ url('/katalog') }}" method="GET" class="mt-4">
                <div class="relative">
                    <input type="text" name="q" placeholder="Ketik contoh: iPhone 16 Pro, Natural Titanium, Second Like New..." class="glass-input pl-10 pr-4 py-3 text-sm w-full" autofocus>
                    <i data-lucide="search" class="w-4 h-4 text-[#8E8E93] absolute left-3.5 top-3.5"></i>
                </div>
            </form>

            <!-- Popular Suggestions Tags -->
            <div class="mt-5">
                <p class="text-xs font-semibold text-[#8E8E93] uppercase tracking-wider mb-2.5">Pencarian Populer</p>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ url('/katalog?q=iPhone+16+Pro') }}" class="px-3 py-1 rounded-full text-xs bg-white/5 border border-white/10 text-[#E5E5EA] hover:border-[#0071E3] hover:text-white transition-colors">
                        iPhone 16 Pro Max
                    </a>
                    <a href="{{ url('/katalog?condition=second') }}" class="px-3 py-1 rounded-full text-xs bg-white/5 border border-white/10 text-[#34C759] hover:border-[#34C759] transition-colors">
                        Second Like New Grade A+
                    </a>
                    <a href="{{ url('/trade-in') }}" class="px-3 py-1 rounded-full text-xs bg-white/5 border border-white/10 text-[#FF9F0A] hover:border-[#FF9F0A] transition-colors">
                        Tukar Tambah 15 Menit
                    </a>
                    <a href="{{ url('/katalog?warranty=resmi_ibox') }}" class="px-3 py-1 rounded-full text-xs bg-white/5 border border-white/10 text-[#5AC8FA] hover:border-[#5AC8FA] transition-colors">
                        Garansi Resmi iBox TAM
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Dynamic Content Wrapper -->
    <main class="relative z-10 flex-grow">
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="glass-card p-4 border border-[#34C759]/40 bg-[#34C759]/10 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i data-lucide="check-circle" class="w-5 h-5 text-[#34C759]"></i>
                        <span class="text-sm font-medium text-white">{{ session('success') }}</span>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="glass-card p-4 border border-[#FF453A]/40 bg-[#FF453A]/10 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-[#FF453A]"></i>
                        <span class="text-sm font-medium text-white">{{ session('error') }}</span>
                    </div>
                </div>
            </div>
        @endif

        @yield('content')
        {{ $slot ?? '' }}
    </main>

    <!-- Professional Apple / iBox Style Footer -->
    <footer class="relative z-10 border-t border-white/10 bg-[#0B0C10]/90 backdrop-blur-xl mt-20 pt-16 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 pb-12 border-b border-white/10">
                
                <!-- Col 1: Store Bio & Guarantee -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-[#0071E3] flex items-center justify-center text-white font-bold">
                            <i data-lucide="smartphone" class="w-4 h-4"></i>
                        </div>
                        <span class="font-bold text-lg text-white tracking-tight">Rap-iPhone Store</span>
                    </div>
                    <p class="text-sm text-[#8E8E93] leading-relaxed max-w-sm">
                        Platform e-commerce premium jual-beli dan program tukar tambah (*trade-in*) iPhone dengan standar pelayanan Apple Authorized Retail. Transparansi garansi dan IMEI 100% terjamin.
                    </p>
                    <div class="flex items-center gap-3 pt-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/5 border border-white/10 text-[#34C759]">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                            100% Produk Original
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/5 border border-white/10 text-[#5AC8FA]">
                            <i data-lucide="truck" class="w-3.5 h-3.5"></i>
                            Gratis Asuransi
                        </span>
                    </div>
                </div>

                <!-- Col 2: Seri Populer -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Seri iPhone</h4>
                    <ul class="space-y-2.5 text-sm text-[#8E8E93]">
                        <li><a href="{{ url('/katalog?series=iPhone+16') }}" class="hover:text-white transition-colors">iPhone 16 Pro Max</a></li>
                        <li><a href="{{ url('/katalog?series=iPhone+16') }}" class="hover:text-white transition-colors">iPhone 16 Pro</a></li>
                        <li><a href="{{ url('/katalog?series=iPhone+15') }}" class="hover:text-white transition-colors">iPhone 15 Pro Max</a></li>
                        <li><a href="{{ url('/katalog?series=iPhone+14') }}" class="hover:text-white transition-colors">iPhone 14 Series</a></li>
                        <li><a href="{{ url('/katalog?condition=second') }}" class="hover:text-[#34C759] transition-colors font-medium">Second Like New (Grade A+)</a></li>
                    </ul>
                </div>

                <!-- Col 3: Layanan Khusus -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Layanan Toko</h4>
                    <ul class="space-y-2.5 text-sm text-[#8E8E93]">
                        <li><a href="{{ url('/trade-in') }}" class="hover:text-white transition-colors">Kalkulator Tukar Tambah</a></li>
                        <li><a href="{{ url('/cek-garansi') }}" class="hover:text-white transition-colors">Cek Status IMEI Kemenperin</a></li>
                        <li><a href="{{ url('/lacak-pesanan') }}" class="hover:text-white transition-colors">Lacak Resi Pengiriman</a></li>
                        <li><a href="{{ url('/syarat-garansi') }}" class="hover:text-white transition-colors">Syarat & Ketentuan Garansi</a></li>
                        <li><a href="{{ url('/design-system') }}" class="hover:text-[#0071E3] transition-colors font-mono text-xs">Design System Showroom</a></li>
                    </ul>
                </div>

                <!-- Col 4: Layanan Pelanggan -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Bantuan & Kontak</h4>
                    <ul class="space-y-2.5 text-sm text-[#8E8E93]">
                        <li class="flex items-start gap-2">
                            <i data-lucide="message-circle" class="w-4 h-4 text-[#34C759] shrink-0 mt-0.5"></i>
                            <div>
                                <p class="text-white font-medium">WhatsApp Resmi</p>
                                <p class="text-xs">0812-3456-7890 (09.00 - 21.00 WIB)</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="credit-card" class="w-4 h-4 text-[#0071E3] shrink-0 mt-0.5"></i>
                            <div>
                                <p class="text-white font-medium">Payment Gateway</p>
                                <p class="text-xs">Didukung Midtrans Snap (QRIS, VA, CC)</p>
                            </div>
                        </li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Copyright -->
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-[#8E8E93] gap-4">
                <p>&copy; 2026 Rap-iPhone. Seluruh hak cipta dilindungi undang-undang.</p>
                <div class="flex items-center gap-6">
                    <span class="hover:text-white transition-colors">Kebijakan Privasi</span>
                    <span class="hover:text-white transition-colors">Ketentuan Layanan</span>
                    <span class="hover:text-white transition-colors">Sertifikasi Apple Partner Style</span>
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
