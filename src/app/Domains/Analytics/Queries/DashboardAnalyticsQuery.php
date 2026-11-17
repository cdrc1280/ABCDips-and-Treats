<?php

namespace App\Domains\Analytics\Queries;

use App\Domains\Analytics\Enums\DashboardPeriod;
use App\Domains\Sales\Models\Order;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

/**
 * Period-scoped numbers for the dashboard (revenue trend, KPIs, breakdowns).
 *
 * Timestamps are stored in UTC; days and months are cut in the shop's timezone
 * (config app.business_timezone), so an order at 7:30am Manila lands on that day
 * and not on the UTC day before. Revenue excludes cancelled, refunded and archived
 * orders. Every dataset is one grouped query.
 */
final class DashboardAnalyticsQuery
{
    public const NOT_REVENUE = [Order::STATUS_CANCELLED, Order::STATUS_REFUNDED, Order::STATUS_ARCHIVED];

    private const MIX_SLOTS = 5;

    private const PAYMENT_LABELS = [
        'gcash' => 'GCash',
        'maya' => 'Maya',
        'qrph' => 'QR Ph',
        'bank_transfer' => 'Bank transfer',
        'cash' => 'Cash',
        'card' => 'Card',
        'cod' => 'Cash on delivery',
    ];

    private readonly string $timezone;

    private readonly CarbonImmutable $start;

    private readonly CarbonImmutable $end;

    private readonly CarbonImmutable $previousStart;

    public function __construct(private readonly DashboardPeriod $period)
    {
        $this->timezone = (string) config('app.business_timezone', 'UTC');
        $now = CarbonImmutable::now($this->timezone);
        $count = $period->buckets();

        $this->start = $period->monthly()
            ? $now->startOfMonth()->subMonths($count - 1)
            : $now->startOfDay()->subDays($count - 1);
        $this->end = $now;
        $this->previousStart = $period->monthly() ? $this->start->subMonths($count) : $this->start->subDays($count);
    }

    /**
     * Revenue trend + headline totals for the period and the one before it.
     *
     * @return array{
     *     period: string, label: string, granularity: string,
     *     points: list<array{key: string, label: string, full: string, revenue: float, orders: int}>,
     *     totals: array{revenue: float, orders: int, average: float},
     *     previous: array{revenue: float, orders: int, average: float}
     * }
     */
    public function analytics(): array
    {
        [$bucket, $bindings] = $this->bucketExpression();

        $rows = $this->revenueOrders($this->start, $this->end)
            ->selectRaw($bucket, $bindings)
            ->selectRaw('SUM(total) as revenue, COUNT(*) as orders')
            ->groupBy('bucket')
            ->toBase()
            ->get()
            ->keyBy('bucket');

        $points = [];
        for ($i = 0; $i < $this->period->buckets(); $i++) {
            $date = $this->period->monthly() ? $this->start->addMonths($i) : $this->start->addDays($i);
            $key = $date->format($this->period->monthly() ? 'Y-m' : 'Y-m-d');
            $row = $rows->get($key);
            $points[] = [
                'key' => $key,
                'label' => $date->format($this->period->monthly() ? 'M' : 'M j'),
                'full' => $date->format($this->period->monthly() ? 'F Y' : 'D, M j'),
                'revenue' => round((float) ($row->revenue ?? 0), 2),
                'orders' => (int) ($row->orders ?? 0),
            ];
        }

        return [
            'period' => $this->period->value,
            'label' => $this->period->label(),
            'granularity' => $this->period->monthly() ? 'month' : 'day',
            'points' => $points,
            'totals' => $this->totals($this->start, $this->end),
            'previous' => $this->totals($this->previousStart, $this->start),
        ];
    }

    /**
     * @return list<array{name: string, revenue: float}>
     */
    public function categories(int $limit = 5): array
    {
        return array_values($this->itemsInPeriod()
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('product_categories', 'product_categories.id', '=', 'products.category_id')
            ->groupBy('product_categories.id', 'product_categories.name')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get(['product_categories.name', DB::raw('SUM(order_items.subtotal) as revenue')])
            ->map(fn ($row) => ['name' => (string) $row->name, 'revenue' => round((float) $row->revenue, 2)])
            ->all());
    }

    /**
     * @return list<array{name: string, qty: int, revenue: float}>
     */
    public function topProducts(int $limit = 5): array
    {
        return array_values($this->itemsInPeriod()
            ->groupBy('order_items.product_name')
            ->orderByDesc('qty')
            ->limit($limit)
            ->get(['order_items.product_name', DB::raw('SUM(order_items.qty) as qty'), DB::raw('SUM(order_items.subtotal) as revenue')])
            ->map(fn ($row) => ['name' => (string) $row->product_name, 'qty' => (int) $row->qty, 'revenue' => round((float) $row->revenue, 2)])
            ->all());
    }

    /**
     * How orders were paid and handed over. Five slots at most; the rest folds into "Other".
     *
     * @return array{payment: list<array{key: string, label: string, orders: int, revenue: float}>, fulfilment: list<array{key: string, label: string, orders: int, revenue: float}>}
     */
    public function mix(): array
    {
        return [
            'payment' => $this->share('payment_method', fn (string $key) => self::PAYMENT_LABELS[$key] ?? $this->sentence($key)),
            'fulfilment' => $this->share('fulfillment_type', fn (string $key) => $this->sentence($key)),
        ];
    }

    /**
     * @param  'payment_method'|'fulfillment_type'  $column
     * @param  callable(string): string  $label
     * @return list<array{key: string, label: string, orders: int, revenue: float}>
     */
    private function share(string $column, callable $label): array
    {
        $rows = $this->revenueOrders($this->start, $this->end)
            ->select([DB::raw("{$column} as slot"), DB::raw('COUNT(*) as orders'), DB::raw('SUM(total) as revenue')])
            ->groupBy('slot')
            ->orderByDesc('orders')
            ->orderBy('slot')
            ->toBase()
            ->get()
            ->map(fn ($row) => [
                'key' => (string) ($row->slot ?? 'unknown'),
                'label' => $label((string) ($row->slot ?? 'unknown')),
                'orders' => (int) $row->orders,
                'revenue' => round((float) $row->revenue, 2),
            ])
            ->values();

        if ($rows->count() <= self::MIX_SLOTS) {
            return array_values($rows->all());
        }

        $rest = $rows->slice(self::MIX_SLOTS - 1);

        return array_values($rows->take(self::MIX_SLOTS - 1)->push([
            'key' => 'other',
            'label' => 'Other',
            'orders' => (int) $rest->sum('orders'),
            'revenue' => round((float) $rest->sum('revenue'), 2),
        ])->all());
    }

    /** @return array{revenue: float, orders: int, average: float} */
    private function totals(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $row = $this->revenueOrders($from, $to)->selectRaw('COALESCE(SUM(total), 0) as revenue, COUNT(*) as orders')->toBase()->first();
        $revenue = round((float) ($row->revenue ?? 0), 2);
        $orders = (int) ($row->orders ?? 0);

        return ['revenue' => $revenue, 'orders' => $orders, 'average' => $orders > 0 ? round($revenue / $orders, 2) : 0.0];
    }

    /** @return Builder<Order> */
    private function revenueOrders(CarbonImmutable $from, CarbonImmutable $to): Builder
    {
        return Order::query()
            ->whereNotIn('status', self::NOT_REVENUE)
            ->where('created_at', '>=', $from->utc())
            ->where('created_at', '<', $to->utc()->addSecond());
    }

    private function itemsInPeriod(): QueryBuilder
    {
        return DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereNotIn('orders.status', self::NOT_REVENUE)
            ->where('orders.created_at', '>=', $this->start->utc())
            ->where('orders.created_at', '<', $this->end->utc()->addSecond());
    }

    /**
     * Select expression ("… as bucket") that turns UTC `created_at` into a local day
     * ("Y-m-d") or month ("Y-m") key, with the zone offset as a bound parameter.
     * Uses the zone's current UTC offset: exact for zones without daylight saving (Asia/Manila).
     *
     * @return array{0: literal-string, 1: list<int|string>}
     */
    private function bucketExpression(): array
    {
        $offset = (int) CarbonImmutable::now($this->timezone)->utcOffset();
        $monthly = $this->period->monthly();

        return match (DB::connection()->getDriverName()) {
            'pgsql' => [$monthly
                ? "to_char(created_at + (? * interval '1 minute'), 'YYYY-MM') as bucket"
                : "to_char(created_at + (? * interval '1 minute'), 'YYYY-MM-DD') as bucket", [$offset]],
            'sqlite' => [$monthly
                ? "strftime('%Y-%m', created_at, ?) as bucket"
                : "strftime('%Y-%m-%d', created_at, ?) as bucket", [sprintf('%+d minutes', $offset)]],
            default => [$monthly
                ? "DATE_FORMAT(DATE_ADD(created_at, INTERVAL ? MINUTE), '%Y-%m') as bucket"
                : "DATE_FORMAT(DATE_ADD(created_at, INTERVAL ? MINUTE), '%Y-%m-%d') as bucket", [$offset]],
        };
    }

    private function sentence(string $key): string
    {
        return ucfirst(str_replace('_', ' ', $key));
    }
}
