@extends('layouts.customer')

@section('title', 'Daftar Akun Baru - Rap-iPhone')

@section('content')
<div class="relative min-h-[calc(100vh-140px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#F5F5F7]">

    <div class="w-full max-w-lg relative z-10">

        <!-- Main Daylight Card Container -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#E5E5EA] shadow-sm">
            
            <!-- Apple-inspired Brand Header -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-[#1D1D1F] text-white shadow-sm mb-4">
                    <svg class="w-8 h-8" viewBox="0 0 170 170" fill="currentColor">
                        <path d="M150.37 130.25c-2.45 5.66-5.35 10.87-8.71 15.66-4.58 6.53-8.33 11.05-11.22 13.56-4.48 4.12-9.28 6.23-14.42 6.35-3.69 0-8.14-1.05-13.32-3.18-5.19-2.12-9.97-3.17-14.34-3.17-4.58 0-9.49 1.05-14.75 3.17-5.26 2.13-9.5 3.24-12.74 3.35-4.35.13-9.16-1.9-14.42-6.08-3.69-3.08-7.7-7.94-12.04-14.58-6.19-9.5-10.96-20.2-14.3-32.1-3.34-11.9-5.01-23.08-5.01-33.54 0-14.07 3.51-25.75 10.53-35.04 7.02-9.29 16.02-14.04 27-14.25 4.35 0 9.28 1.15 14.79 3.44 5.51 2.29 9.39 3.52 11.64 3.69 1.74 0 5.86-1.29 12.35-3.86 6.49-2.58 11.96-3.71 16.42-3.39 12.52.95 22.35 5.55 29.49 13.8-11.05 6.66-16.42 16.04-16.11 28.14.32 9.58 4.1 17.58 11.35 24 7.25 6.41 15.77 10.05 25.56 10.91-2.02 6.08-4.34 11.93-6.95 17.55zM119.22 31.84c0-7.39 2.66-14.15 7.99-20.27 5.33-6.13 11.75-9.98 19.27-11.57.85 7.18-1.54 14.04-7.18 20.57-5.64 6.54-12.34 10.3-20.08 11.27z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-[#1D1D1F] tracking-tight">Buat Akun Rap-iPhone</h1>
                <p class="text-xs text-[#6E6E73] mt-1.5">Bergabung untuk kemudahan belanja iPhone resmi & tukar tambah cepat</p>
            </div>

            <!-- Error Notifications -->
            @if($errors->any())
                <div class="mb-5 p-3.5 rounded-2xl bg-red-50 border border-red-200 text-[#E30000] flex items-start gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-[#E30000] shrink-0 mt-0.5"></i>
                    <div class="text-xs space-y-1">
                        @foreach($errors->all() as $error)
                            <p class="leading-relaxed">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- 1-Click Google OAuth Sign-in Button -->
            <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center gap-3 py-3 px-4 rounded-xl bg-[#F5F5F7] hover:bg-[#E8E8ED] border border-[#D2D2D7] text-[#1D1D1F] font-semibold text-xs sm:text-sm transition-all duration-200 group">
                <svg class="w-4 h-4 shrink-0 transition-transform group-hover:scale-110" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
                    <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.34 24 12 24z"/>
                    <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                    <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.34 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                </svg>
                <span>Daftar 1-Klik dengan Google</span>
            </a>

            <!-- Divider -->
            <div class="relative my-6 flex items-center justify-center">
                <div class="border-t border-[#E5E5EA] w-full"></div>
                <span class="bg-white px-3 text-[11px] font-medium text-[#6E6E73] uppercase tracking-wider relative">
                    atau isi formulir pendaftaran
                </span>
            </div>

            <!-- Conventional Registration Form -->
            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Name Input -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-[#1D1D1F] mb-1.5">
                        Nama Lengkap
                    </label>
                    <div class="relative">
                        <i data-lucide="user" class="w-4 h-4 text-[#6E6E73] absolute left-3.5 top-3.5"></i>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="contoh: Budi Pratama" class="glass-input pl-10 pr-4 py-2.5 w-full text-sm rounded-xl">
                    </div>
                </div>

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-[#1D1D1F] mb-1.5">
                        Alamat Email Aktif
                    </label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-4 h-4 text-[#6E6E73] absolute left-3.5 top-3.5"></i>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="contoh: budi@gmail.com" class="glass-input pl-10 pr-4 py-2.5 w-full text-sm rounded-xl">
                    </div>
                </div>

                <!-- WhatsApp Phone Number Input -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="phone_number" class="text-xs font-semibold text-[#1D1D1F]">
                            Nomor WhatsApp (Opsional)
                        </label>
                        <span class="text-[11px] text-[#008009] font-medium">Untuk update nota & resi</span>
                    </div>
                    <div class="relative">
                        <i data-lucide="phone" class="w-4 h-4 text-[#6E6E73] absolute left-3.5 top-3.5"></i>
                        <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" placeholder="contoh: 081234567890" class="glass-input pl-10 pr-4 py-2.5 w-full text-sm rounded-xl">
                    </div>
                </div>

                <!-- Password Inputs (Grid) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="password" class="block text-xs font-semibold text-[#1D1D1F] mb-1.5">
                            Kata Sandi (Min 8 Karakter)
                        </label>
                        <div class="relative">
                            <i data-lucide="lock" class="w-4 h-4 text-[#6E6E73] absolute left-3.5 top-3.5"></i>
                            <input type="password" id="password" name="password" required placeholder="••••••••" class="glass-input pl-10 pr-4 py-2.5 w-full text-sm rounded-xl">
                        </div>
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-[#1D1D1F] mb-1.5">
                            Ulangi Kata Sandi
                        </label>
                        <div class="relative">
                            <i data-lucide="check" class="w-4 h-4 text-[#6E6E73] absolute left-3.5 top-3.5"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="••••••••" class="glass-input pl-10 pr-4 py-2.5 w-full text-sm rounded-xl">
                        </div>
                    </div>
                </div>

                <!-- Terms & Conditions Checkbox -->
                <div class="pt-2">
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" name="terms" id="terms" required class="w-4 h-4 mt-0.5 rounded border-[#D2D2D7] text-[#0071E3] focus:ring-0 focus:ring-offset-0">
                        <span class="text-xs text-[#6E6E73] leading-relaxed">
                            Saya menyetujui <span class="text-[#0066CC] hover:underline cursor-pointer">Syarat & Ketentuan</span> transaksi serta <span class="text-[#0066CC] hover:underline cursor-pointer">Kebijakan Privasi</span> resmi Rap-iPhone.
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full mt-3 py-3 px-4 rounded-xl font-semibold text-sm text-white bg-[#0071E3] hover:bg-[#0077ED] shadow-sm active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    <span>Daftar Akun Rap-iPhone</span>
                </button>
            </form>

            <!-- Footer Link -->
            <div class="mt-6 text-center pt-4 border-t border-[#E5E5EA]">
                <p class="text-xs text-[#6E6E73]">
                    Sudah memiliki akun?
                    <a href="{{ route('login') }}" class="font-semibold text-[#0066CC] hover:underline transition-colors ml-1">
                        Masuk di Sini
                    </a>
                </p>
            </div>

        </div>

    </div>
</div>
@endsection
