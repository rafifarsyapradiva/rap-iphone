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
     * Redirect user to Google OAuth Consent screen.
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
     * Handle callback from Google OAuth.
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

        if ($user) {
            // Update avatar if currently empty
            if (empty($user->avatar) && !empty($avatar)) {
                $user->update(['avatar' => $avatar]);
            }
        } else {
            // 2. Search existing user by email to link account
            $user = User::where('email', $email)->first();

            if ($user) {
                $user->update([
                    'google_id' => $googleId,
                    'avatar' => $user->avatar ?: $avatar,
                ]);
            } else {
                // 3. Create a brand new user with default 'customer' role
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
        }

        // Authenticate the user and regenerate session
        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return AuthController::redirectBasedOnRole($user)
            ->with('success', "Selamat datang di Rap-iPhone, {$user->name}!");
    }
}
