<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the Apple Glassmorphism login form for Customers.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle customer authentication request with brute-force rate limiting
     * and strict customer role verification.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        // Dedicated customer rate limiting key: customer-login|{email}|{ip}
        $throttleKey = Str::transliterate(
            'customer-login|' . Str::lower($request->input('email')) . '|' . $request->ip()
        );

        // Protect against brute-force attacks (max 5 attempts per minute)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan masuk gagal. Silakan coba kembali dalam {$seconds} detik.",
            ])->onlyInput('email');
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            /** @var User $user */
            $user = Auth::user();

            // Strict Customer Role Guard: Reject internal accounts (admin or owner)
            if (!$user->isCustomer()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                RateLimiter::hit($throttleKey, 60);

                if ($user->isAdmin()) {
                    return back()->withErrors([
                        'email' => 'Akses ditolak: Akun Staf Operasional (Admin) wajib masuk melalui Portal Operasional (/admin/login).',
                    ])->onlyInput('email');
                }

                if ($user->isOwner()) {
                    return back()->withErrors([
                        'email' => 'Akses ditolak: Akun Pemilik Usaha (Owner) wajib masuk melalui Portal Eksekutif (/owner/login).',
                    ])->onlyInput('email');
                }

                return back()->withErrors([
                    'email' => 'Akses ditolak: Portal ini khusus diperuntukkan bagi Pelanggan (Customer).',
                ])->onlyInput('email');
            }

            // Customer credentials and role validated successfully
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            return redirect()->intended(url('/'))
                ->with('success', "Selamat datang kembali, {$user->name}!");
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'email' => 'Kombinasi email dan kata sandi yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    /**
     * Show the Apple Glassmorphism registration form for Customers.
     */
    public function showRegisterForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.register');
    }

    /**
     * Handle user self-registration with Bcrypt hashing and customer role assignment.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone_number' => ['nullable', 'string', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:9', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'terms' => ['accepted'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan masuk atau gunakan email lain.',
            'phone_number.regex' => 'Format nomor WhatsApp tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal terdiri dari 8 karakter.',
            'terms.accepted' => 'Anda harus menyetujui syarat & ketentuan layanan Rap-iPhone.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => Str::lower($validated['email']),
            'phone_number' => $validated['phone_number'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')
            ->with('success', 'Akun Rap-iPhone Anda berhasil dibuat! Selamat menjelajahi katalog iPhone kami.');
    }

    /**
     * Handle session termination and logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'Anda telah berhasil keluar dari akun Rap-iPhone.');
    }

    /**
     * Helper to perform role-based redirect for authenticated sessions.
     */
    public static function redirectBasedOnRole(User $user): RedirectResponse
    {
        if ($user->isOwner()) {
            return redirect()->intended(route('owner.dashboard'));
        }

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(url('/'));
    }
}
