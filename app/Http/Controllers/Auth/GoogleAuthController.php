<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    /**
     * Redirect user to Google OAuth Consent screen (Strictly for Customer Portal).
     */
    public function redirectToGoogle(): RedirectResponse
    {
        try {
            return Socialite::driver('google')->redirect();
        } catch (Throwable $e) {
            Log::error('Google OAuth Redirect Error: ' . $e->getMessage());

            return redirect()->route('login')->with(
                'error',
                'Layanan Google Sign-in sedang tidak dapat diakses saat ini. Silakan gunakan login email atau coba beberapa saat lagi.'
            );
        }
    }

    /**
     * Handle callback from Google OAuth with Strict Customer Role Guard.
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        // Check if user cancelled authorization or Google returned an error
        if ($request->has('error')) {
            return redirect()->route('login')->with(
                'error',
                'Otorisasi login Google dibatalkan. Anda dapat mencoba kembali kapan saja.'
            );
        }

        try {
            /** @var \Laravel\Socialite\Two\User $googleUser */
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            Log::error('Google OAuth Callback Error: ' . $e->getMessage());

            return redirect()->route('login')->with(
                'error',
                'Gagal menyinkronkan data dari akun Google Anda. Silakan coba kembali atau gunakan email dan kata sandi.'
            );
        }

        $email = Str::lower($googleUser->getEmail());
        $googleId = $googleUser->getId();
        $name = $googleUser->getName() ?? 'Customer Rap-iPhone';
        $avatar = $googleUser->getAvatar();

        // 1. Search existing user by google_id
        $user = User::where('google_id', $googleId)->first();

        // 2. If not found by google_id, search by email to link account
        if (!$user) {
            $user = User::where('email', $email)->first();
        }

        // Strict Role Guard for Google OAuth: Reject internal roles (admin & owner)
        if ($user) {
            if ($user->isAdmin()) {
                return redirect()->route('admin.login')->withErrors([
                    'email' => 'Akses ditolak: Akun Google ini terdaftar sebagai Staf Operasional (Admin). Silakan masuk menggunakan kata sandi di Portal Operasional.',
                ]);
            }

            if ($user->isOwner()) {
                return redirect()->route('owner.login')->withErrors([
                    'email' => 'Akses ditolak: Akun Google ini terdaftar sebagai Pemilik Usaha (Owner). Silakan masuk menggunakan kata sandi di Portal Eksekutif.',
                ]);
            }

            // User is customer: update google_id & avatar
            $updates = [];
            if ($user->google_id !== $googleId) {
                $updates['google_id'] = $googleId;
            }
            if (empty($user->avatar) && !empty($avatar)) {
                $updates['avatar'] = $avatar;
            }
            if (!empty($updates)) {
                $user->update($updates);
            }
        } else {
            // 3. Create a brand new customer account
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'google_id' => $googleId,
                'avatar' => $avatar,
                'role' => 'customer',
                'password' => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
            ]);
        }

        // Authenticate the verified customer and regenerate session
        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return redirect()->intended(url('/'))
            ->with('success', "Selamat datang di Rap-iPhone, {$user->name}!");
    }
}
