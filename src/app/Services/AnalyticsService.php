<?php

namespace App\Services;

use App\Domains\Inventory\Models\Ingredient;
use App\Domains\Sales\Models\Order;
use App\Domains\Sales\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function getExecutiveSummary(): array
    {
        $totalRevenue = (float) Order::where('status', Order::STATUS_COMPLETED)->sum('total');
        $totalOrders = Order::count();
        $completedOrders = Order::where('status', Order::STATUS_COMPLETED)->count();
        $lowStockItems = Ingredient::whereColumn('stock_qty', '<=', 'min_stock_qty')->count();

        return [
            'total_revenue' => round($totalRevenue, 2),
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'low_stock_alerts' => $lowStockItems,
        ];
    }

    public function getMonthlyRevenueChartData(): array
    {
        // Monthly aggregated sales (YYYY-MM), portable across MySQL, SQLite (Vercel/tests) and Postgres
        $monthExpression = match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', created_at)",
            'pgsql' => "to_char(created_at, 'YYYY-MM')",
            default => "DATE_FORMAT(created_at, '%Y-%m')",
        };

        $sales = Order::where('status', Order::STATUS_COMPLETED)
            ->select(
                DB::raw("{$monthExpression} as month_key"),
                DB::raw('SUM(total) as revenue_total'),
                DB::raw('COUNT(id) as order_count')
            )
            ->groupBy('month_key')
            ->orderBy('month_key', 'asc')
            ->get();

        $labels = [];
        $revenue = [];
        $orders = [];

        foreach ($sales as $s) {
            $labels[] = (string) $s->getAttribute('month_key');
            $revenue[] = (float) $s->getAttribute('revenue_total');
            $orders[] = (int) $s->getAttribute('order_count');
        }

        if (empty($labels)) {
            $labels = [date('M Y')];
            $revenue = [0.0];
            $orders = [0];
        }

        return [
            'labels' => $labels,
            'series' => [
                ['name' => 'Revenue (₱)', 'data' => $revenue],
                ['name' => 'Orders Count', 'data' => $orders],
            ],
        ];
    }

    public function getTopSellingProducts(int $limit = 5): array
    {
        return OrderItem::query()
            ->select('product_name', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
