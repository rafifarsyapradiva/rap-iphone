<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request and check if user possesses the required role.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Ensure user is authenticated with contextual portal redirect
        if (!Auth::check()) {
            if ($request->is('admin*')) {
                return redirect()->guest(route('admin.login'))->with(
                    'error',
                    'Silakan masuk ke Portal Operasional Admin terlebih dahulu.'
                );
            }

            if ($request->is('owner*')) {
                return redirect()->guest(route('owner.login'))->with(
                    'error',
                    'Silakan masuk ke Portal Eksekutif Owner terlebih dahulu.'
                );
            }

            return redirect()->guest(route('login'))->with(
                'error',
                'Silakan masuk terlebih dahulu untuk mengakses halaman tersebut.'
            );
        }

        $user = Auth::user();

        // 2. Normalize allowed roles (supports both comma-separated and multiple parameters: 'role:admin,owner')
        $allowedRoles = [];
        foreach ($roles as $role) {
            foreach (explode(',', $role) as $subRole) {
                $trimmed = trim($subRole);
                if ($trimmed !== '') {
                    $allowedRoles[] = $trimmed;
                }
            }
        }

        // 3. Verify user has at least one of the allowed roles
        if (in_array($user->role, $allowedRoles, true)) {
            return $next($request);
        }

        // 4. Handle unauthorized access gracefully
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Akses ditolak. Anda tidak memiliki izin untuk tindakan ini.',
            ], 403);
        }

        // If Customer tries to access Admin/Owner workspace -> redirect to storefront home
        if ($user->isCustomer()) {
            return redirect('/')->with(
                'error',
                'Akses ditolak: Anda tidak memiliki wewenang untuk mengakses area operasional toko.'
            );
        }

        // If Admin tries to access Owner exclusive workspace -> redirect to Admin Dashboard
        if ($user->isAdmin() && in_array('owner', $allowedRoles, true)) {
            return redirect()->route('admin.dashboard')->with(
                'error',
                'Akses ditolak: Area ini khusus diperuntukkan bagi Pemilik Usaha (Owner).'
            );
        }

        // General 403 fallback
        abort(403, 'Akses tidak diizinkan untuk peran akun Anda.');
    }
}
