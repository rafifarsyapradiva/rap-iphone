<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Hub — Rap-iPhone Operations & Management')</title>

    <!-- Fonts & Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo/favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo/apple-touch-icon.png') }}">

    <!-- Hallmark Luxury Editorial Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://api.fontshare.com/v2/css?f[]=sentient@300,400&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Lucide Icons (versi dipin) -->
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
<body class="adm-shell min-h-screen flex antialiased">

    <!-- Mobile Sidebar Backdrop -->
    <div id="admin-sidebar-backdrop" class="fixed inset-0 bg-black/20 backdrop-blur-xs z-[35] hidden lg:hidden"></div>

    <!-- Editorial Sidebar -->
    <aside id="admin-sidebar" aria-label="Navigasi utama panel" class="adm-sidebar w-64 min-h-screen shrink-0 fixed inset-y-0 left-0 z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-out flex flex-col justify-between">

        <div class="p-5 overflow-y-auto">
            <!-- Brand Editorial -->
            <a href="{{ $inOwnerArea ? route('owner.dashboard') : route('admin.dashboard') }}" class="flex items-center gap-3 mb-6 px-1 group">
                <img src="{{ asset('images/logo/logo-mark.svg') }}" alt="Rap-iPhone Logomark" class="w-8 h-8 rounded-lg shadow-xs shrink-0">
                <div class="min-w-0">
                    <span class="font-serif text-lg tracking-tight text-[var(--adm-ink)] block leading-tight truncate">Rap-iPhone</span>
                    <span class="text-[11px] text-[var(--adm-ink-muted)] block mt-0.5 truncate">
                        {{ $isOwner ? 'Executive & Financial Suite' : 'Store Operations Hub' }}
                    </span>
                </div>
            </a>

            <nav class="space-y-1" id="admin-sidebar-nav">
                <p class="adm-nav-group" style="margin-top:0">Operasional toko</p>

                <a href="{{ route('admin.dashboard') }}" @if(request()->is('admin/dashboard*')) aria-current="page" @endif class="{{ $navClass(request()->is('admin/dashboard*')) }}">
                    <span class="adm-nav-label"><i data-lucide="layout-dashboard"></i><span>Dashboard</span></span>
                </a>

                <a href="{{ route('admin.products.index') }}" @if(request()->is('admin/products*')) aria-current="page" @endif class="{{ $navClass(request()->is('admin/products*')) }}">
                    <span class="adm-nav-label"><i data-lucide="smartphone"></i><span>Katalog &amp; varian stok</span></span>
                </a>

                <a href="{{ route('admin.orders.index') }}" @if(request()->is('admin/orders*')) aria-current="page" @endif class="{{ $navClass(request()->is('admin/orders*')) }}">
                    <span class="adm-nav-label"><i data-lucide="shopping-cart"></i><span>Pesanan &amp; logistik</span></span>
                    @if(($shell['pending_orders'] ?? 0) > 0)
                        <span class="adm-nav-count" title="Pesanan lunas menunggu diproses">{{ $shell['pending_orders'] }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.trade-ins.index') }}" @if(request()->is('admin/trade-ins*')) aria-current="page" @endif class="{{ $navClass(request()->is('admin/trade-ins*')) }}">
                    <span class="adm-nav-label"><i data-lucide="refresh-cw"></i><span>Antrean trade-in</span></span>
                    @if(($shell['pending_trade_ins'] ?? 0) > 0)
                        <span class="adm-nav-count" title="Pengajuan trade-in menunggu inspeksi">{{ $shell['pending_trade_ins'] }}</span>
                    @endif
                </a>

                @if($isOwner)
                    <p class="adm-nav-group">Buku besar eksekutif</p>

                    <a href="{{ route('owner.dashboard') }}" @if(request()->is('owner/dashboard*')) aria-current="page" @endif class="{{ $navClass(request()->is('owner/dashboard*')) }}">
                        <span class="adm-nav-label"><i data-lucide="crown"></i><span>Analitik &amp; arus kas</span></span>
                    </a>

                    <a href="{{ route('owner.financial.report') }}" @if(request()->is('owner/financial-report*')) aria-current="page" @endif class="{{ $navClass(request()->is('owner/financial-report*')) }}">
                        <span class="adm-nav-label"><i data-lucide="file-spreadsheet"></i><span>Laporan keuangan &amp; ekspor</span></span>
                    </a>

                    <a href="{{ route('owner.staff.index') }}" @if(request()->is('owner/staff*')) aria-current="page" @endif class="{{ $navClass(request()->is('owner/staff*')) }}">
                        <span class="adm-nav-label"><i data-lucide="users"></i><span>Manajemen staf &amp; akun</span></span>
                    </a>
                @endif

                <p class="adm-nav-group">Konfigurasi</p>

                <a href="{{ route('admin.settings') }}" @if(request()->is('admin/settings*')) aria-current="page" @endif class="{{ $navClass(request()->is('admin/settings*')) }}">
                    <span class="adm-nav-label"><i data-lucide="sliders"></i><span>Konfigurasi &amp; status API</span></span>
                </a>

                <a href="{{ url('/') }}" target="_blank" rel="noopener" class="adm-nav-link text-[var(--adm-accent)]">
                    <span class="adm-nav-label"><i data-lucide="external-link"></i><span>Buka etalase toko</span></span>
                </a>
            </nav>
        </div>

        <!-- Profil & Logout -->
        <div class="p-4 border-t border-[var(--adm-rule-border)] bg-[var(--adm-paper-subtle)]">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="adm-avatar">{{ $authUser ? mb_substr($authUser->name, 0, 1) : 'A' }}</span>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-[var(--adm-ink)] truncate">{{ $authUser ? $authUser->name : 'Admin Operasional' }}</p>
                        <p class="text-[11px] text-[var(--adm-ink-muted)] truncate">{{ $authUser ? $authUser->email : 'admin@rap-iphone.com' }}</p>
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

        <header class="adm-masthead px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <button type="button" id="admin-sidebar-toggle" class="lg:hidden adm-icon-btn" aria-label="Buka Sidebar" aria-controls="admin-sidebar">
                    <i data-lucide="menu"></i>
                </button>

                <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs text-[var(--adm-ink-muted)] min-w-0">
                    <a href="{{ $inOwnerArea ? route('owner.dashboard') : route('admin.dashboard') }}" class="hover:text-[var(--adm-ink)] transition-colors shrink-0">{{ $inOwnerArea ? 'Owner Suite' : 'Operations Hub' }}</a>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[var(--adm-ink-faint)] shrink-0"></i>
                    <span class="text-[var(--adm-ink)] font-medium truncate" aria-current="page">@yield('page_title', 'Dashboard')</span>
                </nav>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <div class="hidden md:flex items-center gap-2">
                    <span class="adm-badge {{ $shell['midtrans']['ok'] ? 'adm-tone-green' : 'adm-tone-amber' }}" title="Status konfigurasi Midtrans">
                        <span class="adm-badge-dot"></span>{{ $shell['midtrans']['label'] }}
                    </span>
                    <span class="adm-badge {{ $shell['fonnte']['ok'] ? 'adm-tone-green' : 'adm-tone-amber' }}" title="Status konfigurasi Fonnte WhatsApp">
                        <span class="adm-badge-dot"></span>{{ $shell['fonnte']['label'] }}
                    </span>
                </div>

                @php $attention = ($shell['pending_orders'] ?? 0) + ($shell['pending_trade_ins'] ?? 0); @endphp
                <a href="{{ ($shell['pending_orders'] ?? 0) > 0 ? route('admin.orders.index', ['status' => 'paid']) : route('admin.trade-ins.index') }}"
                   class="adm-icon-btn relative" title="{{ $attention > 0 ? $attention . ' item menunggu tindakan' : 'Tidak ada antrean' }}" aria-label="Antrean tindakan">
                    <i data-lucide="bell"></i>
                    @if($attention > 0)
                        <span class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-[var(--adm-tone-red-ink)] border border-[var(--adm-surface)]"></span>
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

        <footer class="px-6 py-4 border-t border-[var(--adm-rule-border)] bg-[var(--adm-paper)] text-xs text-[var(--adm-ink-muted)] flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ date('Y') }} Rap-iPhone Maison &middot; Hak Cipta Dilindungi</p>
            <p>{{ $isOwner ? 'Executive Owner' : 'Operations Admin' }} &middot; Luxury Editorial System v2.1</p>
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
            var toggle = document.getElementById('admin-sidebar-toggle');

            if (toggle && sidebar) {
                toggle.addEventListener('click', function () {
                    var isClosed = sidebar.classList.contains('-translate-x-full');
                    if (isClosed) {
                        sidebar.classList.remove('-translate-x-full');
                        sidebar.classList.add('translate-x-0');
                        if (backdrop) { backdrop.classList.remove('hidden'); }
                    } else {
                        sidebar.classList.add('-translate-x-full');
                        sidebar.classList.remove('translate-x-0');
                        if (backdrop) { backdrop.classList.add('hidden'); }
                    }
                });
            }

            if (backdrop && sidebar) {
                backdrop.addEventListener('click', function () {
                    sidebar.classList.add('-translate-x-full');
                    sidebar.classList.remove('translate-x-0');
                    backdrop.classList.add('hidden');
                });
            }

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