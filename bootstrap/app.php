<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Sanctum SPA Authentication
        $middleware->statefulApi();

        // Prevent 500 RouteNotFoundException on route('login') for unauthenticated API/invoice requests
        $middleware->redirectGuestsTo(fn (Request $request) => null);

        // Trust Reverse Proxy (Railway, Render, etc.)
        // Set TRUST_PROXIES=* in production only if behind a managed PaaS load balancer.
        // For self-hosted, set TRUST_PROXIES to the actual proxy IP/CIDR range.
        $middleware->trustProxies(
            at: env('TRUST_PROXIES', '127.0.0.1,::1'),
            headers: Request::HEADER_X_FORWARDED_FOR
            | Request::HEADER_X_FORWARDED_HOST
            | Request::HEADER_X_FORWARDED_PORT
            | Request::HEADER_X_FORWARDED_PROTO
        );

        // Spatie Permission Middleware Aliases
        $middleware->alias([
            'role'       => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })
    ->create();

if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || !empty(getenv('VERCEL'))) {
    $app->useStoragePath('/tmp/storage');
}

return $app;
