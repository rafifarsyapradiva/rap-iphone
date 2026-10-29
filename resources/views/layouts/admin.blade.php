<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Hub — Rap-iPhone Operations & Management')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-[#F5F5F7] text-[#1D1D1F] daylight-enterprise min-h-screen flex selection:bg-[#0071E3] selection:text-white antialiased">

    <!-- Mobile Sidebar Frosted Backdrop -->
    <div id="admin-sidebar-backdrop" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-30 hidden lg:hidden"></div>

    <!-- Responsive Collapsible Digimap Daylight Sidebar -->
    <aside id="admin-sidebar" class="glass-sidebar bg-white w-64 min-h-screen shrink-0 fixed inset-y-0 left-0 z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col justify-between border-r border-[#E5E5EA]">
        
        <!-- Sidebar Top: Brand & Menu -->
        <div class="p-6">
            <!-- Brand Logo -->
            <a href="{{ url('/') }}" class="flex items-center gap-3 mb-8">
                <div class="w-10 h-10 rounded-xl bg-[#1D1D1F] flex items-center justify-center text-white font-bold shadow-sm">
                    <i data-lucide="layers" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="font-extrabold text-base tracking-tight text-[#1D1D1F] block">Rap-iPhone</span>
                    <span class="inline-block text-[10px] font-bold tracking-wider px-2 py-0.5 rounded-full bg-blue-50 text-[#0066CC] border border-blue-200 uppercase">
                        {{ Auth::check() && Auth::user()->role === 'owner' ? 'Executive Owner' : 'Operations Admin' }}
                    </span>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="space-y-1.5">
                <p class="text-[11px] font-semibold text-[#6E6E73] uppercase tracking-wider px-3 mb-2">Menu Utama</p>

                <!-- Dashboard -->
                <a href="{{ url('/admin/dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->is('admin/dashboard*') ? 'bg-[#0071E3] text-white shadow-sm font-semibold' : 'text-[#6E6E73] hover:text-[#1D1D1F] hover:bg-[#F5F5F7]' }}">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Dashboard Operasional</span>
                </a>

                <!-- Products & Variants -->
                <a href="{{ url('/admin/products') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->is('admin/products*') ? 'bg-[#0071E3] text-white shadow-sm font-semibold' : 'text-[#6E6E73] hover:text-[#1D1D1F] hover:bg-[#F5F5F7]' }}">
                    <i data-lucide="smartphone" class="w-4 h-4"></i>
                    <span>Katalog & Varian Stok</span>
                </a>

                <!-- Orders & Logistics -->
                <a href="{{ url('/admin/orders') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->is('admin/orders*') ? 'bg-[#0071E3] text-white shadow-sm font-semibold' : 'text-[#6E6E73] hover:text-[#1D1D1F] hover:bg-[#F5F5F7]' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                        <span>Pesanan & Logistik</span>
                    </div>
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ request()->is('admin/orders*') ? 'bg-white/20 text-white' : 'bg-[#F5F5F7] text-[#1D1D1F] border border-[#E5E5EA]' }}">
                        Live
                    </span>
                </a>

                <!-- Trade-In Verification -->
                <a href="{{ url('/admin/trade-ins') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->is('admin/trade-ins*') ? 'bg-[#0071E3] text-white shadow-sm font-semibold' : 'text-[#6E6E73] hover:text-[#1D1D1F] hover:bg-[#F5F5F7]' }}">
                    <i data-lucide="refresh-cw" class="w-4 h-4 {{ request()->is('admin/trade-ins*') ? 'text-white' : 'text-[#008009]' }}"></i>
                    <span>Antrean Tukar Tambah</span>
                </a>

                <!-- Financial & Executive Portal (Owner Only) -->
                @if(Auth::check() && Auth::user()->role === 'owner')
                    <p class="text-[11px] font-semibold text-[#6E6E73] uppercase tracking-wider px-3 mt-6 mb-2">Finansial & Owner</p>

                    <a href="{{ route('owner.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->is('owner/dashboard*') ? 'bg-[#0071E3] text-white shadow-sm font-semibold' : 'text-[#6E6E73] hover:text-[#1D1D1F] hover:bg-[#F5F5F7]' }}">
                        <i data-lucide="crown" class="w-4 h-4 {{ request()->is('owner/dashboard*') ? 'text-white' : 'text-[#BF4800]' }}"></i>
                        <span>Analitik & Arus Kas</span>
                    </a>

                    <a href="{{ route('owner.financial.report') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->is('owner/financial-report*') ? 'bg-[#0071E3] text-white shadow-sm font-semibold' : 'text-[#6E6E73] hover:text-[#1D1D1F] hover:bg-[#F5F5F7]' }}">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4 {{ request()->is('owner/financial-report*') ? 'text-white' : 'text-[#008009]' }}"></i>
                        <span>Laporan & Ekspor Data</span>
                    </a>

                    <a href="{{ route('owner.staff.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->is('owner/staff*') ? 'bg-[#0071E3] text-white shadow-sm font-semibold' : 'text-[#6E6E73] hover:text-[#1D1D1F] hover:bg-[#F5F5F7]' }}">
                        <i data-lucide="users" class="w-4 h-4 {{ request()->is('owner/staff*') ? 'text-white' : 'text-[#0066CC]' }}"></i>
                        <span>Manajemen Staf & Admin</span>
                    </a>
                @endif

                <p class="text-[11px] font-semibold text-[#6E6E73] uppercase tracking-wider px-3 mt-6 mb-2">Sistem</p>

                <!-- Settings -->
                <a href="{{ url('/admin/settings') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-[#6E6E73] hover:text-[#1D1D1F] hover:bg-[#F5F5F7] transition-all">
                    <i data-lucide="sliders" class="w-4 h-4"></i>
                    <span>Konfigurasi & API</span>
                </a>

                <!-- Storefront Link -->
                <a href="{{ url('/') }}" target="_blank" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-[#0066CC] hover:bg-blue-50 transition-all">
                    <i data-lucide="external-link" class="w-4 h-4"></i>
                    <span>Buka Toko (Storefront)</span>
                </a>
            </nav>
        </div>

        <!-- Sidebar Bottom: User Profile & Logout -->
        <div class="p-4 border-t border-[#E5E5EA] bg-[#F5F5F7]">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-[#1D1D1F] flex items-center justify-center font-bold text-white text-xs shrink-0">
                        {{ Auth::check() ? substr(Auth::user()->name, 0, 1) : 'A' }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-[#1D1D1F] truncate">{{ Auth::check() ? Auth::user()->name : 'Admin Operasional' }}</p>
                        <p class="text-[11px] text-[#6E6E73] truncate">{{ Auth::check() ? Auth::user()->email : 'admin@rap-iphone.com' }}</p>
                    </div>
                </div>
                <form action="{{ Route::has('admin.logout') && (!Auth::check() || Auth::user()->role === 'admin') ? route('admin.logout') : (Route::has('owner.logout') && Auth::check() && Auth::user()->role === 'owner' ? route('owner.logout') : url('/logout')) }}" method="POST">
                    @csrf
                    <button type="submit" class="p-2 text-[#6E6E73] hover:text-[#E30000] transition-colors" title="Keluar">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 lg:pl-64 flex flex-col min-h-screen relative z-10">
        
        <!-- Topbar Daylight Frosted Header -->
        <header class="glass-navbar sticky top-0 z-30 px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between border-b border-[#E5E5EA]">
            <div class="flex items-center gap-4">
                <!-- Mobile Sidebar Toggle -->
                <button type="button" id="admin-sidebar-toggle" class="lg:hidden p-2 rounded-xl text-[#6E6E73] hover:text-[#1D1D1F] hover:bg-[#F5F5F7]" aria-label="Buka Sidebar">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>

                <!-- Breadcrumbs -->
                <div class="flex items-center gap-2 text-xs text-[#6E6E73]">
                    <a href="{{ url('/admin/dashboard') }}" class="hover:text-[#1D1D1F] transition-colors">Admin Hub</a>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#86868B]"></i>
                    <span class="text-[#1D1D1F] font-semibold">@yield('page_title', 'Dashboard')</span>
                </div>
            </div>

            <!-- Header Right: System Indicators & Actions -->
            <div class="flex items-center gap-3">
                
                <!-- Status Midtrans Sandbox -->
                <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-[#0066CC] border border-blue-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#008009]"></span>
                    Midtrans Snap OK
                </span>

                <!-- Status WhatsApp Fonnte -->
                <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-[#008009] border border-emerald-200">
                    <i data-lucide="message-square" class="w-3 h-3"></i>
                    Fonnte WA Ready
                </span>

                <!-- Notification Bell -->
                <button type="button" class="p-2 rounded-full bg-[#F5F5F7] border border-[#E5E5EA] text-[#6E6E73] hover:text-[#1D1D1F] transition-colors relative" title="Notifikasi Baru">
                    <i data-lucide="bell" class="w-4 h-4"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-[#E30000]"></span>
                </button>
            </div>
        </header>

        <!-- Page Content Workspace -->
        <main class="p-4 sm:p-6 lg:p-8 flex-grow">
            @if(session('success'))
                <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-[#1D1D1F] flex items-center gap-3">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-[#008009]"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-[#1D1D1F] flex items-center gap-3">
                    <i data-lucide="alert-octagon" class="w-5 h-5 text-[#E30000]"></i>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
            {{ $slot ?? '' }}
        </main>

        <!-- Admin Workspace Footer -->
        <footer class="px-6 py-4 border-t border-[#E5E5EA] bg-white text-xs text-[#6E6E73] flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>Rap-iPhone Operational Portal &mdash; Digimap Enterprise Light UI v2.0 (Laravel 12)</p>
            <div class="flex items-center gap-4">
                <span>Database: MySQL (phpMyAdmin)</span>
                <span>Role Guard: Active</span>
            </div>
        </footer>
    </div>

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
