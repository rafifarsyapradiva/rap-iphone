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

class OwnerAuthController extends Controller
{
    /**
     * Show the dedicated Owner Executive & Financial Login Portal.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            if ($user->isOwner()) {
                return redirect()->intended(route('owner.dashboard'));
            }

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(url('/'));
        }

        return view('auth.owner-login');
    }

    /**
     * Handle owner authentication request with dedicated executive rate limiting
     * and strict role verification.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Alamat email pemilik usaha (owner) wajib diisi.',
            'email.email' => 'Format email pemilik usaha tidak valid.',
            'password.required' => 'Kata sandi pemilik usaha wajib diisi.',
        ]);

        // Dedicated executive rate limiting key: owner-login|{email}|{ip}
        $throttleKey = Str::transliterate(
            'owner-login|' . Str::lower($request->input('email')) . '|' . $request->ip()
        );

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan masuk owner yang gagal. Silakan coba kembali dalam {$seconds} detik.",
            ])->onlyInput('email');
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            /** @var User $user */
            $user = Auth::user();

            // Strict Owner Role Guard
            if (!$user->isOwner()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                RateLimiter::hit($throttleKey, 60);

                if ($user->isAdmin()) {
                    return back()->withErrors([
                        'email' => 'Akses ditolak: Akun Staf Operasional (Admin) wajib masuk melalui Portal Operasional (/admin/login).',
                    ])->onlyInput('email');
                }

                return back()->withErrors([
                    'email' => 'Akses ditolak: Portal Eksekutif ini khusus diperuntukkan bagi Pemilik Usaha (Owner). Pelanggan silakan masuk melalui portal utama (/login).',
                ])->onlyInput('email');
            }

            // Credentials and role confirmed as owner
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            return redirect()->intended(route('owner.dashboard'))
                ->with('success', "Selamat datang di Portal Eksekutif & Finansial Rap-iPhone, {$user->name}!");
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'email' => 'Kombinasi email dan kata sandi eksekutif tidak sesuai.',
        ])->onlyInput('email');
    }

    /**
     * Terminate the executive owner session and redirect back to the dedicated owner login portal.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('owner.login')
            ->with('success', 'Anda telah berhasil keluar dari Portal Eksekutif Owner.');
    }
}

