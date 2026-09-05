<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$app = Application::configure(basePath: dirname(__DIR__))
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

        // Trust Reverse Proxy (Vercel, Railway, Render, etc.)
        $middleware->trustProxies(
            at: env('TRUST_PROXIES', '*'),
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

// Auto-switch storage path to writable /tmp on serverless environments
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || !empty(getenv('VERCEL')) || !empty(getenv('LARAVEL_STORAGE_PATH'))) {
    $storagePath = getenv('LARAVEL_STORAGE_PATH') ?: '/tmp/storage';
    $app->useStoragePath($storagePath);
}

return $app;
