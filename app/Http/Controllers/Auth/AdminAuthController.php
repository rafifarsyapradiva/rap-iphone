<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    /**
     * Show the dedicated Admin Operations Login Portal.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            if ($user->isOwner()) {
                return redirect()->intended(route('owner.dashboard'));
            }

            return redirect()->intended(url('/'));
        }

        return view('auth.admin-login');
    }

    /**
     * Handle admin authentication request with dedicated brute-force protection
     * and strict role verification.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Alamat email admin wajib diisi.',
            'email.email' => 'Format email admin tidak valid.',
            'password.required' => 'Kata sandi admin wajib diisi.',
        ]);

        // Dedicated admin rate limiting key: admin-login|{email}|{ip}
        $throttleKey = Str::transliterate(
            'admin-login|' . Str::lower($request->input('email')) . '|' . $request->ip()
        );

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan masuk admin yang gagal. Silakan coba kembali dalam {$seconds} detik.",
            ])->onlyInput('email');
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            /** @var User $user */
            $user = Auth::user();

            // Strict Admin Role Guard
            if (!$user->isAdmin()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                RateLimiter::hit($throttleKey, 60);

                if ($user->isOwner()) {
                    return back()->withErrors([
                        'email' => 'Akses ditolak: Akun Pemilik Usaha (Owner) wajib masuk melalui Portal Eksekutif (/owner/login).',
                    ])->onlyInput('email');
                }

                return back()->withErrors([
                    'email' => 'Akses ditolak: Portal ini khusus diperuntukkan bagi Staf Operasional Toko (Admin). Pelanggan silakan masuk melalui portal utama (/login).',
                ])->onlyInput('email');
            }

            // Credentials and role confirmed as admin
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', "Selamat datang di Portal Operasional Rap-iPhone, {$user->name}!");
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'email' => 'Kombinasi email dan kata sandi admin tidak sesuai.',
        ])->onlyInput('email');
    }

    /**
     * Terminate the admin session and redirect back to the dedicated admin login portal.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'Anda telah berhasil keluar dari Portal Operasional Admin.');
    }
}

