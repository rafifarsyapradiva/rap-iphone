<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Append standard security headers to protect against clickjacking, MIME sniffing, and XSS (PRD 5.1).
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN', false);
        $response->headers->set('X-Content-Type-Options', 'nosniff', false);
        $response->headers->set('X-XSS-Protection', '1; mode=block', false);
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin', false);

        return $response;
    }
}

