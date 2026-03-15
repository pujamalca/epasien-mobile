<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Content-Security-Policy — NativePHP berjalan lokal di 127.0.0.1
        // 'unsafe-inline' diperlukan untuk inline <script> di Blade views
        // connect-src 'self' mencegah exfiltration data ke server eksternal
        $response->headers->set('Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline'; " .
            "style-src 'self' 'unsafe-inline'; " .
            "img-src 'self' data: blob:; " .
            "connect-src 'self'; " .
            "font-src 'self' data:; " .
            "object-src 'none'; " .
            "base-uri 'self'; " .
            "form-action 'self'"
        );

        // Hapus header yang mengekspos informasi server
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
