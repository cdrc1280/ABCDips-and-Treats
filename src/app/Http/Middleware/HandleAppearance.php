<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Shares the `appearance` cookie (light | dark | system) with the root Blade views so the
 * correct theme class is on <html> before React boots (no flash of the wrong theme).
 */
class HandleAppearance
{
    public function handle(Request $request, Closure $next): Response
    {
        $appearance = $request->cookie('appearance');
        View::share('appearance', in_array($appearance, ['light', 'dark', 'system'], true) ? $appearance : 'system');

        return $next($request);
    }
}
