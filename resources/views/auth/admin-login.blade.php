<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal Masuk Staf Admin Operasional — Rap-iPhone Store Operations</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F5F5F7] text-[#1D1D1F] min-h-screen flex flex-col justify-between selection:bg-[#0071E3] selection:text-white antialiased font-sans">

    <!-- Top Simple Bar -->
    <header class="w-full px-6 py-4 flex items-center justify-between border-b border-[#E5E5EA] bg-white/80 backdrop-blur-md sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-[#1D1D1F] flex items-center justify-center text-white font-bold shadow-sm">
                <i data-lucide="layers" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="font-bold text-sm tracking-tight text-[#1D1D1F] block">Rap-iPhone</span>
                <span class="text-[10px] text-[#6E6E73] font-medium">Store Operations Hub</span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wider bg-blue-50 text-[#0066CC] border border-blue-200 uppercase">
                <span class="w-1.5 h-1.5 rounded-full bg-[#0066CC] animate-pulse"></span>
                Admin Portal
            </span>
            <a href="{{ route('home') }}" class="hidden sm:inline-flex items-center gap-1.5 text-xs text-[#6E6E73] hover:text-[#1D1D1F] transition-colors font-medium">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Toko Publik</span>
            </a>
        </div>
    </header>

    <!-- Main Content Canvas -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-md">

            <!-- Card Container -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#E5E5EA] shadow-sm">

                <!-- Header Brand Section -->
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-[#F5F5F7] border border-[#E5E5EA] text-[#0066CC] shadow-inner mb-4">
                        <i data-lucide="shield-check" class="w-7 h-7"></i>
                    </div>
                    <div class="inline-block mb-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-widest px-2.5 py-0.5 rounded-full bg-emerald-50 text-[#008009] border border-emerald-200">
                            Strict Role Guard Active
                        </span>
                    </div>
                    <h1 class="text-2xl font-bold text-[#1D1D1F] tracking-tight">Login Staf Operasional</h1>
                    <p class="text-xs text-[#6E6E73] mt-1.5 leading-relaxed">
                        Akses khusus staf admin untuk kelola inventaris iPhone, pemenuhan pesanan, verifikasi IMEI, dan logistik toko.
                    </p>
                </div>

                <!-- Flash Notifications -->
                @if(session('success'))
                    <div class="mb-5 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-[#008009] flex items-start gap-2.5">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-[#008009] shrink-0 mt-0.5"></i>
                        <span class="text-xs font-medium leading-relaxed">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-5 p-3.5 rounded-2xl bg-red-50 border border-red-200 text-[#E30000] flex items-start gap-2.5">
                        <i data-lucide="alert-octagon" class="w-4 h-4 text-[#E30000] shrink-0 mt-0.5"></i>
                        <span class="text-xs font-medium leading-relaxed">{{ session('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-5 p-3.5 rounded-2xl bg-red-50 border border-red-200 text-[#E30000] flex items-start gap-2.5">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-[#E30000] shrink-0 mt-0.5"></i>
                        <div class="text-xs space-y-1">
                            @foreach($errors->all() as $error)
                                <p class="leading-relaxed font-medium">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Dedicated Admin Form -->
                <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-[#1D1D1F] mb-1.5">
                            Email Staf Operasional
                        </label>
                        <div class="relative">
                            <i data-lucide="mail" class="w-4 h-4 text-[#6E6E73] absolute left-3.5 top-3.5"></i>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@rap-iphone.com" class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-[#D2D2D7] bg-[#F5F5F7] text-[#1D1D1F] placeholder-[#86868B] focus:bg-white focus:border-[#0071E3] focus:ring-2 focus:ring-[#0071E3]/20 transition-all outline-none">
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="text-xs font-semibold text-[#1D1D1F]">
                                Kata Sandi
                            </label>
                            <span class="text-[11px] text-[#86868B]">
                                Rahasia internal
                            </span>
                        </div>
                        <div class="relative">
                            <i data-lucide="lock" class="w-4 h-4 text-[#6E6E73] absolute left-3.5 top-3.5"></i>
                            <input type="password" id="password" name="password" required placeholder="••••••••" class="w-full pl-10 pr-10 py-2.5 text-sm rounded-xl border border-[#D2D2D7] bg-[#F5F5F7] text-[#1D1D1F] placeholder-[#86868B] focus:bg-white focus:border-[#0071E3] focus:ring-2 focus:ring-[#0071E3]/20 transition-all outline-none">
                            <button type="button" id="toggle-password" class="absolute right-3.5 top-3 text-[#6E6E73] hover:text-[#1D1D1F]" aria-label="Lihat kata sandi">
                                <i data-lucide="eye" class="w-4 h-4" id="eye-icon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me Checkbox -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded border-[#D2D2D7] text-[#0071E3] focus:ring-0 focus:ring-offset-0">
                            <span class="text-xs text-[#6E6E73]">Ingat saya di stasiun kerja ini</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full mt-2 py-3 px-4 rounded-xl font-semibold text-sm text-white bg-[#0071E3] hover:bg-[#0077ED] active:scale-[0.99] transition-all shadow-sm flex items-center justify-center gap-2">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        <span>Masuk ke Panel Operasional</span>
                    </button>
                </form>

                <!-- Quick Demo Account Chip (Dedicated for Admin) -->
                <div class="mt-6 pt-5 border-t border-[#E5E5EA]">
                    <p class="text-[11px] font-semibold text-[#6E6E73] uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                        <i data-lucide="zap" class="w-3.5 h-3.5 text-[#008009]"></i>
                        <span>Akun Uji Coba Admin Operasional</span>
                    </p>
                    <button type="button" onclick="fillAdmin('admin@rap-iphone.com', 'password123')" class="w-full p-2.5 rounded-xl bg-[#F5F5F7] hover:bg-[#E8E8ED] border border-[#E5E5EA] text-left transition-all flex items-center justify-between group">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-emerald-50 text-[#008009] border border-emerald-200 flex items-center justify-center font-bold text-xs group-hover:scale-105 transition-transform">
                                A
                            </div>
                            <div>
                                <span class="block text-xs font-semibold text-[#1D1D1F]">Admin Operasional</span>
                                <span class="block text-[11px] text-[#6E6E73]">admin@rap-iphone.com</span>
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold text-[#0066CC] px-2 py-1 rounded-md bg-white border border-[#E5E5EA]">
                            1-Klik Isi
                        </span>
                    </button>
                </div>

                <!-- Navigation Assistance Links -->
                <div class="mt-6 pt-4 border-t border-[#E5E5EA] flex items-center justify-between text-xs text-[#6E6E73]">
                    <a href="{{ route('home') }}" class="hover:text-[#1D1D1F] transition-colors inline-flex items-center gap-1">
                        <i data-lucide="chevron-left" class="w-3 h-3"></i>
                        <span>Ke Toko Publik</span>
                    </a>
                    <a href="{{ route('owner.login') }}" class="text-[#86868B] hover:text-[#BF4800] transition-colors inline-flex items-center gap-1" title="Akses Portal Pemilik Usaha">
                        <span>Portal Owner</span>
                        <i data-lucide="crown" class="w-3 h-3"></i>
                    </a>
                </div>

            </div>

            <!-- Bottom Footnote -->
            <div class="mt-6 text-center text-[11px] text-[#86868B] space-y-1">
                <p>Rap-iPhone Store Operations Portal &bull; Digimap Enterprise Light UI</p>
                <p class="text-[10px]">Percobaan akses tanpa hak wewenang dimonitor dan dicatat dalam log sistem.</p>
            </div>

        </div>
    </main>

    <!-- Footer Simple -->
    <footer class="w-full py-4 text-center text-xs text-[#86868B] border-t border-[#E5E5EA] bg-white">
        &copy; {{ date('Y') }} Rap-iPhone. Dedicated Operations Security Guard.
    </footer>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide) {
                window.lucide.createIcons();
            }

            // Show/Hide Password Toggle
            const toggleBtn = document.getElementById('toggle-password');
            const passInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (toggleBtn && passInput && eyeIcon) {
                toggleBtn.addEventListener('click', function () {
                    if (passInput.type === 'password') {
                        passInput.type = 'text';
                        eyeIcon.setAttribute('data-lucide', 'eye-off');
                    } else {
                        passInput.type = 'password';
                        eyeIcon.setAttribute('data-lucide', 'eye');
                    }
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                });
            }
        });

        // 1-Click Autofill Helper
        function fillAdmin(email, password) {
            const emailInput = document.getElementById('email');
            const passInput = document.getElementById('password');
            if (emailInput && passInput) {
                emailInput.value = email;
                passInput.value = password;
                emailInput.focus();
            }
        }
    </script>
</body>
</html>

