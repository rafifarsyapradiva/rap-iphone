<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal Masuk Eksekutif &amp; Finansial Owner — Rap-iPhone Executive Portal</title>

    <!-- Typography: Sentient Serif & Hanken Grotesk -->
    <link rel="preconnect" href="https://api.fontshare.com">
    <link href="https://api.fontshare.com/v2/css?f[]=sentient@300,400&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="adm-auth-shell">

    <!-- Executive Masthead -->
    <header class="adm-auth-masthead">
        <div class="adm-auth-brand">
            <div class="adm-auth-brand-mark" aria-hidden="true" style="background-color: var(--adm-accent);">
                <i data-lucide="crown" class="w-4 h-4"></i>
            </div>
            <div>
                <span class="adm-auth-brand-title">Rap-iPhone</span>
                <span class="adm-auth-brand-sub">Executive &amp; Financial Hub</span>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span class="adm-auth-badge adm-auth-badge-owner">
                <span style="width: 0.375rem; height: 0.375rem; border-radius: 999px; background-color: currentColor;" aria-hidden="true"></span>
                <span>Strict Owner Role Guard Active</span>
            </span>
            <a href="{{ route('home') }}" class="adm-auth-link" style="font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Toko publik</span>
            </a>
        </div>
    </header>

    <!-- Main Content Canvas -->
    <main class="adm-auth-main">
        <div class="adm-auth-card">

            <!-- Executive Header -->
            <div class="adm-auth-header">
                <div class="adm-auth-icon-wrap" aria-hidden="true" style="color: var(--adm-ink);">
                    <i data-lucide="shield-alert" class="w-6 h-6"></i>
                </div>
                <h1 class="adm-auth-title">Portal Eksekutif &amp; Finansial</h1>
                <p class="adm-auth-desc">
                    Akses khusus Pemilik Usaha (Owner) untuk memantau metrik finansial, omzet toko, laporan laba rugi, dan kendali staf.
                </p>
            </div>

            <!-- Flash & Error Notifications -->
            @if(session('success'))
                <div class="adm-auth-alert adm-auth-alert-success" role="status">
                    <i data-lucide="check-circle-2" class="w-4 h-4 shrink-0" style="margin-top: 0.125rem;"></i>
                    <p style="margin: 0;">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="adm-auth-alert adm-auth-alert-error" role="alert">
                    <i data-lucide="alert-octagon" class="w-4 h-4 shrink-0" style="margin-top: 0.125rem;"></i>
                    <p style="margin: 0;">{{ session('error') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="adm-auth-alert adm-auth-alert-error" role="alert">
                    <i data-lucide="alert-circle" class="w-4 h-4 shrink-0" style="margin-top: 0.125rem;"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <p style="margin: 0;">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Dedicated Owner Form -->
            <form action="{{ route('owner.login.submit') }}" method="POST">
                @csrf

                <!-- Email Input -->
                <div class="adm-auth-field">
                    <label for="email" class="adm-auth-label">Email pemilik usaha</label>
                    <div class="adm-auth-input-wrap">
                        <i data-lucide="mail" class="adm-auth-input-icon"></i>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="owner@rap-iphone.com" class="adm-auth-input">
                    </div>
                </div>

                <!-- Password Input -->
                <div class="adm-auth-field">
                    <div class="adm-auth-field-row">
                        <label for="password" class="adm-auth-label" style="margin-bottom: 0;">Kata sandi eksekutif</label>
                        <span style="font-size: 0.6875rem; color: var(--adm-ink-muted);">Kredensial keamanan tinggi</span>
                    </div>
                    <div class="adm-auth-input-wrap">
                        <i data-lucide="lock" class="adm-auth-input-icon"></i>
                        <input type="password" id="password" name="password" required placeholder="••••••••" class="adm-auth-input" style="padding-right: 2.5rem;">
                        <button type="button" id="toggle-password" class="adm-auth-toggle-pass" aria-label="Lihat kata sandi">
                            <i data-lucide="eye" class="w-4 h-4" id="eye-icon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="adm-auth-field" style="margin-bottom: 1.25rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="remember" id="remember" style="accent-color: var(--adm-accent); width: 1rem; height: 1rem;">
                        <span style="font-size: 0.75rem; color: var(--adm-ink-muted);">Ingat sesi eksekutif ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="adm-auth-submit-btn" style="background-color: var(--adm-accent);">
                    <i data-lucide="crown" class="w-4 h-4"></i>
                    <span>Masuk ke portal eksekutif</span>
                </button>
            </form>

            <!-- Quick Demo Inline Ruled Action (F11 Zero Card-in-Card) -->
            <div class="adm-auth-demo-bar">
                <div class="adm-auth-demo-label">
                    <span>Akun Uji Coba Pemilik Usaha (Owner)</span>
                    <span>1-klik isi</span>
                </div>
                <button type="button" onclick="fillOwner('owner@rap-iphone.com', 'password123')" class="adm-auth-demo-btn">
                    <div class="adm-auth-demo-left">
                        <div class="adm-auth-demo-monogram" aria-hidden="true" style="background-color: var(--adm-accent);">O</div>
                        <div>
                            <div class="adm-auth-demo-title">Pemilik Usaha (Owner)</div>
                            <div class="adm-auth-demo-email">owner@rap-iphone.com</div>
                        </div>
                    </div>
                    <span class="adm-auth-demo-action">Gunakan &rarr;</span>
                </button>
            </div>

            <!-- Alternative Portal Guidance (F10 Zero Middle-Dot) -->
            <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: var(--adm-rule-hair); text-align: center; font-size: 0.6875rem; color: var(--adm-ink-muted);">
                <span>Bukan pemilik usaha?</span>
                <a href="{{ route('admin.login') }}" class="adm-auth-link" style="margin-left: 0.25rem;">Portal operasional admin</a>
                <span style="margin: 0 0.25rem;">/</span>
                <a href="{{ route('login') }}" class="adm-auth-link">Portal customer</a>
            </div>

        </div>
    </main>

    <!-- Executive Masthead Footer -->
    <footer class="adm-auth-footer">
        <span>Rap-iPhone Executive Governance &copy; {{ date('Y') }}. Jalur masuk terenkripsi khusus eksekutif.</span>
    </footer>

    <script>
        // Lucide icon initializer
        if (window.lucide) {
            window.lucide.createIcons();
        }

        // Toggle password visibility
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

        // Demo credential helper
        function fillOwner(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
