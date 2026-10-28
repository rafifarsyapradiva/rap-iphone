@extends('layouts.customer')

@section('title', 'Masuk ke Akun Anda - Rap-iPhone')

@section('content')
<div class="relative min-h-[calc(100vh-140px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    
    <!-- Background Ambient Glow Orbs -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-[#0071E3]/20 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-[#5E5CE6]/15 rounded-full blur-[140px] pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">

        <!-- Main Glass Card Container -->
        <div class="glass-card-elevated rounded-3xl p-6 sm:p-8 border border-white/15 shadow-2xl backdrop-blur-2xl">
            
            <!-- Apple-inspired Brand Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-white/10 border border-white/20 shadow-inner mb-4">
                    <svg class="w-8 h-8 text-white" viewBox="0 0 170 170" fill="currentColor">
                        <path d="M150.37 130.25c-2.45 5.66-5.35 10.87-8.71 15.66-4.58 6.53-8.33 11.05-11.22 13.56-4.48 4.12-9.28 6.23-14.42 6.35-3.69 0-8.14-1.05-13.32-3.18-5.19-2.12-9.97-3.17-14.34-3.17-4.58 0-9.49 1.05-14.75 3.17-5.26 2.13-9.5 3.24-12.74 3.35-4.35.13-9.16-1.9-14.42-6.08-3.69-3.08-7.7-7.94-12.04-14.58-6.19-9.5-10.96-20.2-14.3-32.1-3.34-11.9-5.01-23.08-5.01-33.54 0-14.07 3.51-25.75 10.53-35.04 7.02-9.29 16.02-14.04 27-14.25 4.35 0 9.28 1.15 14.79 3.44 5.51 2.29 9.39 3.52 11.64 3.69 1.74 0 5.86-1.29 12.35-3.86 6.49-2.58 11.96-3.71 16.42-3.39 12.52.95 22.35 5.55 29.49 13.8-11.05 6.66-16.42 16.04-16.11 28.14.32 9.58 4.1 17.58 11.35 24 7.25 6.41 15.77 10.05 25.56 10.91-2.02 6.08-4.34 11.93-6.95 17.55zM119.22 31.84c0-7.39 2.66-14.15 7.99-20.27 5.33-6.13 11.75-9.98 19.27-11.57.85 7.18-1.54 14.04-7.18 20.57-5.64 6.54-12.34 10.3-20.08 11.27z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Masuk ke Rap-iPhone</h1>
                <p class="text-xs text-[#8E8E93] mt-1.5">Akses katalog resmi, keranjang belanja, dan pelacakan pesanan Anda</p>
            </div>

            <!-- Error Notifications -->
            @if($errors->any())
                <div class="mb-5 p-3.5 rounded-2xl bg-[#FF453A]/15 border border-[#FF453A]/30 text-white flex items-start gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-[#FF453A] shrink-0 mt-0.5"></i>
                    <div class="text-xs space-y-1">
                        @foreach($errors->all() as $error)
                            <p class="leading-relaxed">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- 1-Click Google OAuth Sign-in Button -->
            <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center gap-3 py-3 px-4 rounded-xl bg-white/10 hover:bg-white/15 border border-white/20 hover:border-white/30 text-white font-medium text-xs sm:text-sm transition-all duration-200 shadow-sm group">
                <svg class="w-4 h-4 shrink-0 transition-transform group-hover:scale-110" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
                    <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.34 24 12 24z"/>
                    <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                    <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.34 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                </svg>
                <span>Masuk dengan Google</span>
            </a>

            <!-- Divider -->
            <div class="relative my-6 flex items-center justify-center">
                <div class="border-t border-white/10 w-full"></div>
                <span class="bg-[#161B22] px-3 text-[11px] font-medium text-[#8E8E93] uppercase tracking-wider relative">
                    atau gunakan email
                </span>
            </div>

            <!-- Conventional Login Form -->
            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-[#E5E5EA] mb-1.5">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-4 h-4 text-[#8E8E93] absolute left-3.5 top-3.5"></i>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="contoh: budi@gmail.com" class="glass-input pl-10 pr-4 py-2.5 w-full text-sm rounded-xl">
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="text-xs font-semibold text-[#E5E5EA]">
                            Kata Sandi
                        </label>
                        <span class="text-[11px] text-[#0071E3] hover:text-[#5AC8FA] cursor-pointer">
                            Lupa sandi?
                        </span>
                    </div>
                    <div class="relative">
                        <i data-lucide="lock" class="w-4 h-4 text-[#8E8E93] absolute left-3.5 top-3.5"></i>
                        <input type="password" id="password" name="password" required placeholder="••••••••" class="glass-input pl-10 pr-10 py-2.5 w-full text-sm rounded-xl">
                        <button type="button" id="toggle-password" class="absolute right-3.5 top-3 text-[#8E8E93] hover:text-white" aria-label="Lihat kata sandi">
                            <i data-lucide="eye" class="w-4 h-4" id="eye-icon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded bg-white/10 border-white/20 text-[#0071E3] focus:ring-0 focus:ring-offset-0">
                        <span class="text-xs text-[#8E8E93]">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full mt-2 py-3 px-4 rounded-xl font-semibold text-sm text-white bg-gradient-to-r from-[#0071E3] to-[#0051A2] hover:from-[#0077ED] hover:to-[#005bb7] shadow-lg shadow-[#0071E3]/25 active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                    <span>Masuk ke Akun</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>

            <!-- Quick Demo Account Chips (For instant dev & testing) -->
            <div class="mt-6 pt-5 border-t border-white/10">
                <p class="text-[11px] font-semibold text-[#8E8E93] uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                    <i data-lucide="zap" class="w-3.5 h-3.5 text-[#FF9F0A]"></i>
                    <span>Akun Uji Coba Cepat (1-Klik Isi)</span>
                </p>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" onclick="fillLogin('owner@rap-iphone.com', 'password123')" class="px-2 py-1.5 rounded-lg bg-white/5 hover:bg-white/15 border border-white/10 text-[11px] text-[#E5E5EA] transition-all text-center">
                        <span class="block font-semibold text-[#5AC8FA]">Owner</span>
                        <span class="text-[10px] text-[#8E8E93]">owner@</span>
                    </button>
                    <button type="button" onclick="fillLogin('admin@rap-iphone.com', 'password123')" class="px-2 py-1.5 rounded-lg bg-white/5 hover:bg-white/15 border border-white/10 text-[11px] text-[#E5E5EA] transition-all text-center">
                        <span class="block font-semibold text-[#34C759]">Admin</span>
                        <span class="text-[10px] text-[#8E8E93]">admin@</span>
                    </button>
                    <button type="button" onclick="fillLogin('customer@rap-iphone.com', 'password123')" class="px-2 py-1.5 rounded-lg bg-white/5 hover:bg-white/15 border border-white/10 text-[11px] text-[#E5E5EA] transition-all text-center">
                        <span class="block font-semibold text-white">Customer</span>
                        <span class="text-[10px] text-[#8E8E93]">customer@</span>
                    </button>
                </div>
            </div>

            <!-- Footer Link -->
            <div class="mt-6 text-center">
                <p class="text-xs text-[#8E8E93]">
                    Belum memiliki akun Rap-iPhone?
                    <a href="{{ route('register') }}" class="font-semibold text-[#0071E3] hover:text-[#5AC8FA] transition-colors ml-1">
                        Daftar Sekarang
                    </a>
                </p>
            </div>

        </div>

    </div>
</div>

@push('scripts')
<script>
    // Show/Hide Password Toggle
    document.getElementById('toggle-password')?.addEventListener('click', function () {
        const passInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
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

    // Helper to auto-fill credentials for demo testing
    function fillLogin(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endpush
@endsection
