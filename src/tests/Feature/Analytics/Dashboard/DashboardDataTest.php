<?php

namespace Tests\Feature\Analytics\Dashboard;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Analytics\Enums\ReportPermission;
use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductCategory;
use App\Domains\Inventory\Models\Ingredient;
use App\Domains\Sales\Models\Order;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardDataTest extends TestCase
{
    use RefreshDatabase;

    private int $n = 0;

    private Product $loaf;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.business_timezone' => 'Asia/Manila']);
        // 10:00 in Manila on Nov 16 = 02:00 UTC
        $this->travelTo(Carbon::parse('2026-11-16 02:00:00', 'UTC'));

        $category = ProductCategory::query()->create(['name' => 'Breads', 'slug' => 'breads']);
        $this->loaf = Product::query()->create(['category_id' => $category->id, 'sku' => 'L', 'name' => 'Loaf', 'slug' => 'loaf', 'price' => 200]);
    }

    /** @param array<string, mixed> $attributes */
    private function order(string $status, float $total, array $attributes = [], ?string $createdAtUtc = null): Order
    {
        $this->n++;
        $order = Order::query()->create([
            'order_number' => "O-{$this->n}", 'tracking_token' => "t{$this->n}", 'customer_name' => 'C', 'customer_email' => 'c@x.test',
            'customer_phone' => '09171234567', 'fulfillment_type' => 'pickup', 'subtotal' => $total, 'total' => $total, 'payment_method' => 'gcash',
            'payment_status' => 'paid', 'status' => $status, ...$attributes,
        ]);
        $order->items()->create(['product_id' => $this->loaf->id, 'product_name' => $this->loaf->name, 'product_sku' => $this->loaf->sku, 'qty' => 2, 'unit_price' => $total / 2, 'subtotal' => $total]);

        if ($createdAtUtc !== null) {
            $order->forceFill(['created_at' => Carbon::parse($createdAtUtc, 'UTC')])->saveQuietly();
        }

        return $order;
    }

    private function admin(): User
    {
        return $this->userWith(AccessPermission::DashboardView, ReportPermission::ReportsExport);
    }

    public function test_analytics_exclude_cancelled_orders_and_every_group_is_filled(): void
    {
        $this->order('completed', 400);
        $this->order('cancelled', 9999);
        $this->order('preparing', 100, ['fulfillment_type' => 'delivery', 'payment_method' => 'maya']);
        Ingredient::query()->create(['sku' => 'FL', 'name' => 'Flour', 'unit' => 'kg', 'stock_qty' => 1, 'min_stock_qty' => 5]);

        $this->actingAs($this->admin())
            ->get('/admin')
            ->assertInertia(fn (Assert $page) => $page
                ->where('period', '30d')
                ->has('periods', 4)
                ->has('reports', 6)
                ->missing('analytics')
                ->missing('mix')
                ->loadDeferredProps('default', fn (Assert $reload) => $reload
                    ->where('analytics.granularity', 'day')
                    ->has('analytics.points', 30)
                    ->where('analytics.points.29.key', '2026-11-16')
                    ->where('analytics.points.29.revenue', 500)
                    ->where('analytics.points.29.orders', 2)
                    ->where('analytics.totals.revenue', 500)
                    ->where('analytics.totals.orders', 2)
                    ->where('analytics.totals.average', 250))
                ->loadDeferredProps('breakdowns', fn (Assert $reload) => $reload
                    ->where('categories.0.name', 'Breads')
                    ->where('categories.0.revenue', 500)
                    ->where('top_products.0.qty', 4)
                    ->has('mix.payment', 2)
                    ->where('mix.payment.0.label', 'GCash')
                    ->where('mix.payment.0.revenue', 400)
                    ->where('mix.payment.1.label', 'Maya')
                    ->has('mix.fulfilment', 2))
                ->loadDeferredProps('live', fn (Assert $reload) => $reload
                    ->where('low_stock.0.name', 'Flour')
                    ->has('recent_orders', 3)
                    ->has('pipeline', 6)
                    ->where('pipeline.0', ['status' => 'pending', 'label' => 'Pending', 'count' => 0])
                    ->where('pipeline.2', ['status' => 'preparing', 'label' => 'Preparing', 'count' => 1])));
    }

    public function test_days_are_cut_in_the_shop_timezone(): void
    {
        // 01:00 Nov 16 in Manila, which is still Nov 15 in UTC.
        $this->order('completed', 300, [], '2026-11-15 17:00:00');
        // 23:00 Nov 15 in Manila.
        $this->order('completed', 50, [], '2026-11-15 15:00:00');

        $this->actingAs($this->admin())
            ->get('/admin?period=7d')
            ->assertInertia(fn (Assert $page) => $page
                ->where('period', '7d')
                ->loadDeferredProps('default', fn (Assert $reload) => $reload
                    ->has('analytics.points', 7)
                    ->where('analytics.points.6.key', '2026-11-16')
                    ->where('analytics.points.6.revenue', 300)
                    ->where('analytics.points.5.key', '2026-11-15')
                    ->where('analytics.points.5.revenue', 50)));
    }

    public function test_previous_period_totals_power_the_comparison(): void
    {
        $this->order('completed', 200);
        $this->order('completed', 120, [], '2026-10-10 04:00:00'); // 37 days back: previous 30-day window
        $this->order('completed', 999, [], '2026-08-01 04:00:00'); // outside both windows

        $this->actingAs($this->admin())
            ->get('/admin')
            ->assertInertia(fn (Assert $page) => $page
                ->loadDeferredProps('default', fn (Assert $reload) => $reload
                    ->where('analytics.totals.revenue', 200)
                    ->where('analytics.previous.revenue', 120)
                    ->where('analytics.previous.orders', 1)));
    }

    public function test_year_view_uses_month_buckets_and_bad_periods_fall_back(): void
    {
        $this->order('completed', 80, [], '2026-02-10 04:00:00');

        $this->actingAs($this->admin())
            ->get('/admin?period=12m')
            ->assertInertia(fn (Assert $page) => $page
                ->where('period', '12m')
                ->loadDeferredProps('default', fn (Assert $reload) => $reload
                    ->where('analytics.granularity', 'month')
                    ->has('analytics.points', 12)
                    ->where('analytics.points.0.key', '2025-12')
                    ->where('analytics.points.2.key', '2026-02')
                    ->where('analytics.points.2.revenue', 80)));

        $this->actingAs($this->admin())
            ->get('/admin?period=forever')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('period', '30d'));
    }

    public function test_payment_mix_folds_the_tail_into_other(): void
    {
        foreach (['gcash', 'gcash', 'gcash', 'maya', 'maya', 'qrph', 'bank_transfer', 'cash', 'card'] as $method) {
            $this->order('completed', 100, ['payment_method' => $method]);
        }

        $this->actingAs($this->admin())
            ->get('/admin')
            ->assertInertia(fn (Assert $page) => $page
                ->loadDeferredProps('breakdowns', fn (Assert $reload) => $reload
                    ->has('mix.payment', 5)
                    ->where('mix.payment.0.key', 'gcash')
                    ->where('mix.payment.0.orders', 3)
                    ->where('mix.payment.4.key', 'other')
                    ->where('mix.payment.4.orders', 2)));
    }

    public function test_report_downloads_require_the_permission(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin-report-download/sales/pdf')->assertForbidden();
    }
}
