<?php

namespace App\Domains\Analytics\Controllers;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Analytics\Enums\DashboardPeriod;
use App\Domains\Analytics\Queries\DashboardAnalyticsQuery;
use App\Domains\Analytics\Queries\DashboardQueries;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('can:'.AccessPermission::DashboardView->value)];
    }

    /**
     * First paint is instant; tiles stream in as three deferred groups.
     * `?period=` (7d, 30d, 90d, 12m) scopes the analytics and breakdowns;
     * the live group (pipeline, stock, latest orders) ignores it.
     */
    public function index(Request $request, DashboardQueries $queries): Response
    {
        $period = DashboardPeriod::fromQuery($request->query('period'));
        $analytics = new DashboardAnalyticsQuery($period);

        return Inertia::render('dashboard/index', [
            'period' => $period->value,
            'periods' => DashboardPeriod::options(),
            'analytics' => Inertia::defer(fn (): array => $analytics->analytics()),
            'categories' => Inertia::defer(fn (): array => $analytics->categories(), 'breakdowns'),
            'top_products' => Inertia::defer(fn (): array => $analytics->topProducts(), 'breakdowns'),
            'mix' => Inertia::defer(fn (): array => $analytics->mix(), 'breakdowns'),
            'pipeline' => Inertia::defer(fn (): array => $queries->pipeline(), 'live'),
            'low_stock' => Inertia::defer(fn (): array => $queries->lowStock(), 'live'),
            'recent_orders' => Inertia::defer(fn (): array => $queries->recentOrders(), 'live'),
            'reports' => $queries->reports(),
        ]);
    }
}
