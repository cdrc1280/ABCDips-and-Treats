<?php

namespace App\Domains\Analytics\Queries;

use App\Domains\Inventory\Models\Ingredient;
use App\Domains\Inventory\Models\PackagingMaterial;
use App\Domains\Sales\Models\Order;
use Illuminate\Support\Carbon;

/**
 * Live (not period-scoped) side of the dashboard: open-order pipeline, low stock,
 * latest orders and the report list. Period numbers live in DashboardAnalyticsQuery.
 */
final class DashboardQueries
{
    /** Order stages that still need someone to act, in kitchen order. */
    private const OPEN_STAGES = [
        Order::STATUS_PENDING,
        Order::STATUS_CONFIRMED,
        Order::STATUS_PREPARING,
        Order::STATUS_PACKAGING,
        Order::STATUS_OUT_FOR_DELIVERY,
        Order::STATUS_READY_FOR_PICKUP,
    ];

    /**
     * Live count of open orders per stage (zeros included, so the strip never reflows).
     *
     * @return list<array{status: string, label: string, count: int}>
     */
    public function pipeline(): array
    {
        $counts = Order::query()
            ->whereIn('status', self::OPEN_STAGES)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return array_map(fn (string $status) => [
            'status' => $status,
            'label' => ucfirst(str_replace('_', ' ', $status)),
            'count' => (int) ($counts[$status] ?? 0),
        ], self::OPEN_STAGES);
    }

    /**
     * Ingredients and packaging at or below their minimum, lowest first.
     *
     * @return array<int, array{kind: string, name: string, stock: float, minimum: float, unit: string}>
     */
    public function lowStock(int $limit = 8): array
    {
        $ingredients = Ingredient::query()->whereColumn('stock_qty', '<=', 'min_stock_qty')->orderBy('stock_qty')->limit($limit)
            ->get(['name', 'stock_qty', 'min_stock_qty', 'unit'])
            ->map(fn ($i) => ['kind' => 'Ingredient', 'name' => $i->name, 'stock' => (float) $i->stock_qty, 'minimum' => (float) $i->min_stock_qty, 'unit' => $i->unit]);

        $packaging = PackagingMaterial::query()->where('is_active', true)->whereColumn('stock_qty', '<=', 'min_stock_qty')->orderBy('stock_qty')->limit($limit)
            ->get(['name', 'stock_qty', 'min_stock_qty', 'unit'])
            ->map(fn ($p) => ['kind' => 'Packaging', 'name' => $p->name, 'stock' => (float) $p->stock_qty, 'minimum' => (float) $p->min_stock_qty, 'unit' => $p->unit]);

        return $ingredients->concat($packaging)->sortBy('stock')->take($limit)->values()->all();
    }

    /**
     * @return array<int, array{id: string, order_number: string, customer_name: string, total: float, status: string, created_at: string|null}>
     */
    public function recentOrders(int $limit = 6): array
    {
        return Order::query()->latest()->limit($limit)->get(['id', 'order_number', 'customer_name', 'total', 'status', 'created_at'])
            ->map(fn (Order $o) => [
                'id' => $o->id,
                'order_number' => $o->order_number,
                'customer_name' => $o->customer_name,
                'total' => (float) $o->total,
                'status' => $o->status,
                'created_at' => $o->created_at instanceof Carbon ? $o->created_at->toIso8601String() : null,
            ])
            ->values()->all();
    }

    /**
     * Report cards for the dashboard (downloaded through admin.reports.download).
     *
     * @return list<array{key: string, title: string, description: string}>
     */
    public function reports(): array
    {
        return [
            ['key' => 'sales', 'title' => 'Sales & revenue', 'description' => 'Orders, payment methods, discounts and totals.'],
            ['key' => 'products', 'title' => 'Product performance', 'description' => 'Units sold, stock and revenue per product.'],
            ['key' => 'inventory', 'title' => 'Inventory & reorder', 'description' => 'Ingredients and packaging, stock and valuation.'],
            ['key' => 'production', 'title' => 'Production batches', 'description' => 'Bakes, yields and status.'],
            ['key' => 'payroll', 'title' => 'Payroll', 'description' => 'Salaries, overtime, deductions and net pay.'],
            ['key' => 'coupons', 'title' => 'Coupons', 'description' => 'Redemptions, discounts and validity.'],
        ];
    }
}
