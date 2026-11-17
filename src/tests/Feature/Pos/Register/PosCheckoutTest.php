<?php

namespace Tests\Feature\Pos\Register;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductCategory;
use App\Domains\Pos\Enums\PosPermission;
use App\Domains\Sales\Models\Order;
use App\Domains\User\Models\User;
use App\Rules\PhilippinePhone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Inertia\Support\SessionKey;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PosCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private Product $loaf;

    protected function setUp(): void
    {
        parent::setUp();
        $category = ProductCategory::query()->create(['name' => 'Breads', 'slug' => 'breads']);
        $this->loaf = Product::query()->create(['category_id' => $category->id, 'sku' => 'LOAF', 'barcode' => '4800001', 'name' => 'Banana loaf', 'slug' => 'loaf', 'price' => 250, 'stock_qty' => 5]);
    }

    private function cashier(): User
    {
        return $this->userWith(AccessPermission::DashboardView, PosPermission::PosUse);
    }

    public function test_register_requires_the_pos_permission(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/pos')->assertForbidden();
        $this->actingAs($this->cashier())->get('/admin/pos')->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('pos/index')->where('products.0.barcode', '4800001'));
    }

    public function test_cash_sale_completes_deducts_stock_and_flashes_a_receipt(): void
    {
        $response = $this->actingAs($this->cashier())
            ->post('/admin/pos/checkout', ['payment_method' => 'cash', 'cash_tendered' => '1000', 'items' => [['id' => $this->loaf->id, 'qty' => 2], ['id' => $this->loaf->id, 'qty' => 1]]])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/pos')
            ->assertInertiaFlash('receipt.order_number');

        $receipt = session(SessionKey::FLASH_DATA)['receipt'];
        $this->assertEquals(750, $receipt['total']);
        $this->assertEquals(250, $receipt['change_due']);

        $order = Order::query()->firstOrFail();
        $this->assertSame('completed', $order->status);
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame(Order::WALK_IN_PHONE, $order->customer_phone);
        $this->assertTrue(Validator::make(['p' => $order->customer_phone], ['p' => [new PhilippinePhone]])->passes());
        $this->assertSame(3, (int) $order->items()->first()->qty);
        $this->assertSame(2, $this->loaf->refresh()->stock_qty);
    }

    public function test_cannot_oversell_or_underpay(): void
    {
        $this->actingAs($this->cashier());

        $this->from('/admin/pos')->post('/admin/pos/checkout', ['payment_method' => 'gcash', 'items' => [['id' => $this->loaf->id, 'qty' => 6]]])
            ->assertInertiaFlash('toast.type', 'error');
        $this->from('/admin/pos')->post('/admin/pos/checkout', ['payment_method' => 'cash', 'cash_tendered' => '100', 'items' => [['id' => $this->loaf->id, 'qty' => 1]]])
            ->assertInertiaFlash('toast.type', 'error');
        $this->post('/admin/pos/checkout', ['payment_method' => 'cash', 'items' => [['id' => $this->loaf->id, 'qty' => 1]]])
            ->assertSessionHasErrors('cash_tendered');

        $this->assertSame(0, Order::query()->count());
        $this->assertSame(5, $this->loaf->refresh()->stock_qty);
    }
}
