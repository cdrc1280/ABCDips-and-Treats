<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Content Security Policy — conservative defaults
        $viteDevHosts = app()->environment('local') ? "http://127.0.0.1:5173 http://localhost:5173" : '';
        $viteDevConnect = app()->environment('local') ? "ws://127.0.0.1:5173 ws://localhost:5173" : '';
        $csp = "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https: {$viteDevHosts}; style-src 'self' 'unsafe-inline' https: {$viteDevHosts}; img-src 'self' data: https:; font-src 'self' https:; connect-src 'self' https: wss: {$viteDevConnect}; frame-ancestors 'none';";

        $response->headers->set('Content-Security-Policy', $csp);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        if (app()->environment('production')) {
            // Enforce HSTS in production only
            $response->headers->set('Strict-Transport-Security', 'max-age=63072000; includeSubDomains; preload');
        }

        return $response;
    }
}
