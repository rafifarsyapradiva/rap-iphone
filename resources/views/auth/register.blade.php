@extends('layouts.customer')

@section('title', 'Daftar Akun Pelanggan Baru — Rap-iPhone')

@section('content')
<div class="cust-auth-shell">
    <div class="cust-auth-card cust-auth-card-wide">
        
        <!-- Studio Header -->
        <div class="cust-auth-header">
            <div class="cust-auth-mark" aria-hidden="true">
                <svg width="24" height="24" viewBox="0 0 170 170" fill="currentColor">
                    <path d="M150.37 130.25c-2.45 5.66-5.35 10.87-8.71 15.66-4.58 6.53-8.33 11.05-11.22 13.56-4.48 4.12-9.28 6.23-14.42 6.35-3.69 0-8.14-1.05-13.32-3.18-5.19-2.12-9.97-3.17-14.34-3.17-4.58 0-9.49 1.05-14.75 3.17-5.26 2.13-9.5 3.24-12.74 3.35-4.35.13-9.16-1.9-14.42-6.08-3.69-3.08-7.7-7.94-12.04-14.58-6.19-9.5-10.96-20.2-14.3-32.1-3.34-11.9-5.01-23.08-5.01-33.54 0-14.07 3.51-25.75 10.53-35.04 7.02-9.29 16.02-14.04 27-14.25 4.35 0 9.28 1.15 14.79 3.44 5.51 2.29 9.39 3.52 11.64 3.69 1.74 0 5.86-1.29 12.35-3.86 6.49-2.58 11.96-3.71 16.42-3.39 12.52.95 22.35 5.55 29.49 13.8-11.05 6.66-16.42 16.04-16.11 28.14.32 9.58 4.1 17.58 11.35 24 7.25 6.41 15.77 10.05 25.56 10.91-2.02 6.08-4.34 11.93-6.95 17.55zM119.22 31.84c0-7.39 2.66-14.15 7.99-20.27 5.33-6.13 11.75-9.98 19.27-11.57.85 7.18-1.54 14.04-7.18 20.57-5.64 6.54-12.34 10.3-20.08 11.27z"/>
                </svg>
            </div>
            <h1 class="cust-auth-title">Buat Akun Rap-iPhone</h1>
            <p class="cust-auth-desc">Bergabung untuk kemudahan belanja iPhone resmi dan tukar tambah cepat.</p>
            <div class="cust-auth-badge">
                <span class="cust-auth-badge-dot" aria-hidden="true"></span>
                <span>Registrasi Mandiri Pelanggan</span>
            </div>
        </div>

        <!-- Alert Notifications -->
        @if($errors->any())
            <div class="cust-auth-alert cust-auth-alert-error" role="alert">
                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0" style="margin-top: 0.125rem;"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <p style="margin: 0;">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 1-Click Google OAuth Registration -->
        <a href="{{ route('auth.google') }}" class="cust-auth-google-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.34 24 12 24z"/>
                <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.34 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
            </svg>
            <span>Daftar 1-Klik dengan Google</span>
        </a>

        <!-- Divider -->
        <div class="cust-auth-divider">
            <span class="cust-auth-divider-text">atau isi formulir pendaftaran</span>
        </div>

        <!-- Registration Form -->
        <form action="{{ route('register') }}" method="POST">
            @csrf

            <!-- Name Input -->
            <div class="cust-auth-field">
                <label for="name" class="cust-auth-label">Nama lengkap</label>
                <div class="cust-auth-input-wrap">
                    <i data-lucide="user" class="cust-auth-input-icon"></i>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="contoh: Budi Pratama" class="cust-auth-input">
                </div>
            </div>

            <!-- Email Input -->
            <div class="cust-auth-field">
                <label for="email" class="cust-auth-label">Alamat email aktif</label>
                <div class="cust-auth-input-wrap">
                    <i data-lucide="mail" class="cust-auth-input-icon"></i>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="contoh: budi@gmail.com" class="cust-auth-input">
                </div>
            </div>

            <!-- WhatsApp Number (Optional) -->
            <div class="cust-auth-field">
                <div class="cust-auth-field-row">
                    <label for="phone_number" class="cust-auth-label" style="margin-bottom: 0;">Nomor WhatsApp</label>
                    <span style="font-size: 0.6875rem; color: var(--cust-tone-healthy-ink);">Notifikasi pesanan &amp; resi via Fonnte WA</span>
                </div>
                <div class="cust-auth-input-wrap">
                    <i data-lucide="phone" class="cust-auth-input-icon"></i>
                    <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" placeholder="contoh: 081234567890" class="cust-auth-input">
                </div>
            </div>

            <!-- Password & Confirmation Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.875rem; margin-bottom: 1rem;">
                <div>
                    <label for="password" class="cust-auth-label">Kata sandi (min 8 karakter)</label>
                    <div class="cust-auth-input-wrap">
                        <i data-lucide="lock" class="cust-auth-input-icon"></i>
                        <input type="password" id="password" name="password" required placeholder="••••••••" class="cust-auth-input">
                    </div>
                </div>
                <div>
                    <label for="password_confirmation" class="cust-auth-label">Ulangi kata sandi</label>
                    <div class="cust-auth-input-wrap">
                        <i data-lucide="check" class="cust-auth-input-icon"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="••••••••" class="cust-auth-input">
                    </div>
                </div>
            </div>

            <!-- Terms & Conditions Checkbox -->
            <div class="cust-auth-field" style="margin-bottom: 1.25rem;">
                <label style="display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="terms" id="terms" required style="accent-color: var(--cust-signal); width: 1rem; height: 1rem; margin-top: 0.125rem;">
                    <span style="font-size: 0.75rem; color: var(--cust-ink-muted); line-height: 1.4;">
                        Saya menyetujui <span style="color: var(--cust-signal);">Syarat &amp; Ketentuan</span> transaksi serta <span style="color: var(--cust-signal);">Kebijakan Privasi</span> resmi Rap-iPhone.
                    </span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="cust-auth-submit-btn">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Daftar akun Rap-iPhone</span>
            </button>
        </form>

        <!-- Footer Links & Staff Advisory Note (F10 Zero Middle-Dot) -->
        <div class="cust-auth-footer">
            <p style="margin: 0;">
                Sudah memiliki akun Rap-iPhone?
                <a href="{{ route('login') }}" class="cust-auth-link">Masuk di sini</a>
            </p>
            <div class="cust-auth-footer-cross">
                <p style="margin: 0 0 0.5rem 0;">Staf Toko (Admin) dikelola dan didaftarkan langsung oleh Pemilik Usaha</p>
                <span>Portal internal:</span>
                <a href="{{ route('admin.login') }}" class="cust-auth-link" style="margin: 0 0.25rem;">Portal operasional</a>
                <span>/</span>
                <a href="{{ route('owner.login') }}" class="cust-auth-link" style="margin: 0 0.25rem;">Portal eksekutif</a>
            </div>
        </div>

    </div>
</div>
@endsection
