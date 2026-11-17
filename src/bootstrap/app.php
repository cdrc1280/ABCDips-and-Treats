<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpFoundation\Response;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::using(20),
        ]);

        $middleware->redirectGuestsTo(fn (Request $request) => $request->expectsJson() ? null : route('login'));

        // Trust reverse proxies (Vercel, Railway, Render, ...).
        $middleware->trustProxies(
            // Middleware is configured before config/ is loaded, so env() is the only source here.
            at: (string) env('TRUST_PROXIES', '*'), // @phpstan-ignore larastan.noEnvCallsOutsideOfConfig
            headers: Request::HEADER_X_FORWARDED_FOR
            | Request::HEADER_X_FORWARDED_HOST
            | Request::HEADER_X_FORWARDED_PORT
            | Request::HEADER_X_FORWARDED_PROTO
        );

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || ($request->expectsJson() && ! $request->header('X-Inertia')),
        );

        // Business-rule failures: error toast + back() for page requests, 422 for JSON.
        $exceptions->render(function (DomainRuleViolation $e, Request $request) {
            if ($request->expectsJson() && ! $request->header('X-Inertia')) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            Inertia::flash('toast', ['type' => 'error', 'message' => $e->getMessage()]);

            return back();
        });

        // One branded error page for 403/404/500/503 outside debug mode.
        $exceptions->respond(function (Response $response, Throwable $e, Request $request) {
            $status = $response->getStatusCode();

            if (app()->hasDebugModeEnabled() && $status >= 500) {
                return $response;
            }

            if (in_array($status, [403, 404, 500, 503], true) && ! $request->is('api/*') && ! ($request->expectsJson() && ! $request->header('X-Inertia'))) {
                return Inertia::render('error', ['status' => $status])
                    ->toResponse($request)
                    ->setStatusCode($status);
            }

            if ($status === 419 && $request->header('X-Inertia')) {
                return back()->with('toast', ['type' => 'error', 'message' => __('Your session expired. Please try again.')]);
            }

            return $response;
        });
    })
    ->create();

// Auto-switch storage path to writable /tmp on serverless environments.
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || ! empty(getenv('VERCEL')) || ! empty(getenv('LARAVEL_STORAGE_PATH'))) {
    $app->useStoragePath(getenv('LARAVEL_STORAGE_PATH') ?: '/tmp/storage');
}

return $app;
