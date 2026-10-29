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

    <!-- Lucide Icons (versi dipin agar tampilan stabil) -->
    <script src="https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js"></script>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
@php
    $authUser = Auth::user();
    $isOwner = $authUser && $authUser->role === 'owner';
    $inOwnerArea = request()->is('owner/*');
    $shell = $shell ?? ['pending_orders' => 0, 'pending_trade_ins' => 0, 'midtrans' => ['label' => 'Midtrans', 'ok' => false], 'fonnte' => ['label' => 'WhatsApp', 'ok' => false]];

    $navClass = fn (bool $active) => 'adm-nav-link' . ($active ? ' is-active' : '');

    $logoutRoute = url('/logout');
    if ($isOwner && Route::has('owner.logout') && $inOwnerArea) {
        $logoutRoute = route('owner.logout');
    } elseif (Route::has('admin.logout') && ! $isOwner) {
        $logoutRoute = route('admin.logout');
    } elseif ($isOwner && Route::has('owner.logout')) {
        $logoutRoute = route('owner.logout');
    }
@endphp
<body class="bg-[#F5F5F7] text-[#1D1D1F] daylight-enterprise adm-shell min-h-screen flex selection:bg-[#0071E3] selection:text-white antialiased">

    <!-- Mobile Sidebar Backdrop -->
    <div id="admin-sidebar-backdrop" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-[35] hidden lg:hidden"></div>

    <!-- Sidebar -->
    <aside id="admin-sidebar" aria-label="Navigasi utama panel" class="bg-white w-64 min-h-screen shrink-0 fixed inset-y-0 left-0 z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col justify-between border-r border-[#E5E5EA]">

        <div class="p-5 overflow-y-auto">
            <!-- Brand -->
            <a href="{{ $inOwnerArea ? route('owner.dashboard') : route('admin.dashboard') }}" class="flex items-center gap-3 mb-6 px-1">
                <div class="w-10 h-10 rounded-xl bg-[#1D1D1F] flex items-center justify-center text-white shadow-sm">
                    <i data-lucide="layers" class="w-5 h-5"></i>
                </div>
                <div class="min-w-0">
                    <span class="font-extrabold text-base tracking-tight text-[#1D1D1F] block leading-tight">Rap-iPhone</span>
                    <span class="inline-block mt-0.5 text-[10px] font-bold tracking-wider px-2 py-0.5 rounded-full bg-blue-50 text-[#0066CC] border border-blue-200 uppercase">
                        {{ $isOwner ? 'Executive Owner' : 'Operations Admin' }}
                    </span>
                </div>
            </a>

            <nav class="space-y-1" id="admin-sidebar-nav">
                <p class="adm-nav-group" style="margin-top:0">Operasional</p>

                <a href="{{ route('admin.dashboard') }}" @if(request()->is('admin/dashboard*')) aria-current="page" @endif class="{{ $navClass(request()->is('admin/dashboard*')) }}">
                    <span class="adm-nav-label"><i data-lucide="layout-dashboard"></i><span>Dashboard</span></span>
                </a>

                <a href="{{ route('admin.products.index') }}" @if(request()->is('admin/products*')) aria-current="page" @endif class="{{ $navClass(request()->is('admin/products*')) }}">
                    <span class="adm-nav-label"><i data-lucide="smartphone"></i><span>Katalog &amp; Varian Stok</span></span>
                </a>

                <a href="{{ route('admin.orders.index') }}" @if(request()->is('admin/orders*')) aria-current="page" @endif class="{{ $navClass(request()->is('admin/orders*')) }}">
                    <span class="adm-nav-label"><i data-lucide="shopping-cart"></i><span>Pesanan &amp; Logistik</span></span>
                    @if(($shell['pending_orders'] ?? 0) > 0)
                        <span class="adm-nav-count" title="Pesanan lunas menunggu diproses">{{ $shell['pending_orders'] }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.trade-ins.index') }}" @if(request()->is('admin/trade-ins*')) aria-current="page" @endif class="{{ $navClass(request()->is('admin/trade-ins*')) }}">
                    <span class="adm-nav-label"><i data-lucide="refresh-cw"></i><span>Antrean Trade-In</span></span>
                    @if(($shell['pending_trade_ins'] ?? 0) > 0)
                        <span class="adm-nav-count" title="Pengajuan trade-in menunggu inspeksi">{{ $shell['pending_trade_ins'] }}</span>
                    @endif
                </a>

                @if($isOwner)
                    <p class="adm-nav-group">Finansial &amp; Owner</p>

                    <a href="{{ route('owner.dashboard') }}" @if(request()->is('owner/dashboard*')) aria-current="page" @endif class="{{ $navClass(request()->is('owner/dashboard*')) }}">
                        <span class="adm-nav-label"><i data-lucide="crown"></i><span>Analitik &amp; Arus Kas</span></span>
                    </a>

                    <a href="{{ route('owner.financial.report') }}" @if(request()->is('owner/financial-report*')) aria-current="page" @endif class="{{ $navClass(request()->is('owner/financial-report*')) }}">
                        <span class="adm-nav-label"><i data-lucide="file-spreadsheet"></i><span>Laporan &amp; Ekspor Data</span></span>
                    </a>

                    <a href="{{ route('owner.staff.index') }}" @if(request()->is('owner/staff*')) aria-current="page" @endif class="{{ $navClass(request()->is('owner/staff*')) }}">
                        <span class="adm-nav-label"><i data-lucide="users"></i><span>Manajemen Staf &amp; Admin</span></span>
                    </a>
                @endif

                <p class="adm-nav-group">Sistem</p>

                <a href="{{ route('admin.settings') }}" @if(request()->is('admin/settings*')) aria-current="page" @endif class="{{ $navClass(request()->is('admin/settings*')) }}">
                    <span class="adm-nav-label"><i data-lucide="sliders"></i><span>Konfigurasi &amp; API</span></span>
                </a>

                <a href="{{ url('/') }}" target="_blank" rel="noopener" class="adm-nav-link" style="color:#0066CC">
                    <span class="adm-nav-label"><i data-lucide="external-link"></i><span>Buka Toko (Storefront)</span></span>
                </a>
            </nav>
        </div>

        <!-- Profil & Logout -->
        <div class="p-4 border-t border-[#E5E5EA] bg-[#F5F5F7]">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="adm-avatar">{{ $authUser ? mb_substr($authUser->name, 0, 1) : 'A' }}</span>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-[#1D1D1F] truncate">{{ $authUser ? $authUser->name : 'Admin Operasional' }}</p>
                        <p class="text-[11px] text-[#6E6E73] truncate">{{ $authUser ? $authUser->email : 'admin@rap-iphone.com' }}</p>
                    </div>
                </div>
                <form action="{{ $logoutRoute }}" method="POST">
                    @csrf
                    <button type="submit" class="adm-icon-btn is-danger" title="Keluar" aria-label="Keluar">
                        <i data-lucide="log-out"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main -->
    <div class="flex-1 lg:pl-64 flex flex-col min-h-screen min-w-0 relative">

        <header class="adm-topbar px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <button type="button" id="admin-sidebar-toggle" class="lg:hidden adm-icon-btn" aria-label="Buka Sidebar" aria-controls="admin-sidebar">
                    <i data-lucide="menu"></i>
                </button>

                <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs text-[#6E6E73] min-w-0">
                    <a href="{{ $inOwnerArea ? route('owner.dashboard') : route('admin.dashboard') }}" class="hover:text-[#1D1D1F] transition-colors shrink-0">{{ $inOwnerArea ? 'Owner Hub' : 'Admin Hub' }}</a>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#86868B] shrink-0"></i>
                    <span class="text-[#1D1D1F] font-semibold truncate" aria-current="page">@yield('page_title', 'Dashboard')</span>
                </nav>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <div class="hidden md:flex items-center gap-2"><span class="adm-badge {{ $shell['midtrans']['ok'] ? 'adm-tone-green' : 'adm-tone-amber' }}" title="Status konfigurasi Midtrans dari .env">
                    <span class="adm-badge-dot"></span>{{ $shell['midtrans']['label'] }}
                </span>
                <span class="adm-badge {{ $shell['fonnte']['ok'] ? 'adm-tone-green' : 'adm-tone-amber' }}" title="Status konfigurasi Fonnte dari .env">
                    <span class="adm-badge-dot"></span>{{ $shell['fonnte']['label'] }}
                </span></div>

                @php $attention = ($shell['pending_orders'] ?? 0) + ($shell['pending_trade_ins'] ?? 0); @endphp
                <a href="{{ ($shell['pending_orders'] ?? 0) > 0 ? route('admin.orders.index', ['status' => 'paid']) : route('admin.trade-ins.index') }}"
                   class="adm-icon-btn relative" title="{{ $attention > 0 ? $attention . ' item menunggu tindakan' : 'Tidak ada antrean tindakan' }}" aria-label="Antrean tindakan">
                    <i data-lucide="bell"></i>
                    @if($attention > 0)
                        <span class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-[#D70015] border-2 border-white"></span>
                    @endif
                </a>
            </div>
        </header>

        <main class="p-4 sm:p-6 lg:p-8 flex-grow min-w-0">
            <div class="adm-page">
                @if(session('success'))
                    <div class="adm-alert adm-tone-green" role="status">
                        <i data-lucide="check-circle-2"></i>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="adm-alert adm-tone-red" role="alert">
                        <i data-lucide="alert-octagon"></i>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
                {{ $slot ?? '' }}
            </div>
        </main>

        <footer class="px-6 py-4 border-t border-[#E5E5EA] bg-white text-xs text-[#6E6E73] flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ date('Y') }} Rap-iPhone Operations</p>
            <p>{{ $isOwner ? 'Executive Owner' : 'Operations Admin' }} &middot; Panel v2.1</p>
        </footer>
    </div>

    <!-- Lucide init & sidebar auto-close (mobile) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide) {
                window.lucide.createIcons();
            }

            var sidebar = document.getElementById('admin-sidebar');
            var backdrop = document.getElementById('admin-sidebar-backdrop');
            document.querySelectorAll('#admin-sidebar-nav a').forEach(function (link) {
                link.addEventListener('click', function () {
                    if (window.innerWidth < 1024 && sidebar && sidebar.classList.contains('translate-x-0')) {
                        sidebar.classList.remove('translate-x-0');
                        sidebar.classList.add('-translate-x-full');
                        if (backdrop) { backdrop.classList.add('hidden'); }
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
