<?php

namespace Tests\Feature\Flows;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Sales\Enums\CouponPermission;
use App\Domains\Sales\Models\Coupon;
use App\Domains\Sales\Models\Order;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\TestResponse;
use Tests\Feature\Storefront\StorefrontTestHelpers;
use Tests\TestCase;

/** Staff create a coupon → customers redeem it at checkout → the usage limit holds → cancelling gives the use back. */
class CouponFlowTest extends TestCase
{
    use RefreshDatabase, StorefrontTestHelpers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Http::preventStrayRequests();
    }

    private function checkoutWithCoupon(User $customer, string $code): TestResponse
    {
        $this->actingAs($customer)->post('/cart/items', ['product_id' => $this->product(['price' => 500, 'stock_qty' => 20])->id, 'qty' => 2]);
        $this->post('/cart/coupon', ['code' => $code]);

        return $this->post('/checkout', [
            'customer_name' => $customer->name, 'customer_email' => $customer->email, 'customer_phone' => '09171234567',
            'fulfillment_type' => 'pickup', 'delivery_mode' => 'pooling', 'payment_method' => 'bank_transfer',
        ]);
    }

    public function test_a_single_use_coupon_is_redeemed_once_and_released_when_the_order_is_cancelled(): void
    {
        $staff = $this->userWith(AccessPermission::DashboardView, ...CouponPermission::cases());
        $this->actingAs($staff)->post('/admin/coupons', ['code' => 'MERIENDA10', 'type' => 'percent', 'value' => 10, 'min_spend' => 500, 'usage_limit' => 1, 'is_active' => true])
            ->assertSessionHasNoErrors();

        $ana = User::factory()->customer()->create(['email_verified_at' => now()]);
        $this->checkoutWithCoupon($ana, 'merienda10')->assertRedirect();

        $order = Order::query()->sole();
        $this->assertSame('MERIENDA10', $order->coupon_code);
        $this->assertEquals(100, (float) $order->discount_amount); // 10% of 1,000
        $this->assertEquals(900, (float) $order->total);
        $this->assertSame(1, Coupon::query()->sole()->used_count);

        // The only use is taken: the next customer can't apply it.
        $ben = User::factory()->customer()->create(['email_verified_at' => now()]);
        $this->checkoutWithCoupon($ben, 'MERIENDA10');
        $this->assertSame(0, Order::query()->where('customer_email', $ben->email)->where('coupon_code', 'MERIENDA10')->count());

        // Ana cancels → the use comes back → Ben can redeem it now.
        $this->actingAs($ana)->post("/account/orders/{$order->id}/cancel")->assertRedirect();
        $this->assertSame(0, Coupon::query()->sole()->used_count);

        // (Ben's first checkout went through without the coupon, so his basket is empty again.)
        $this->actingAs($ben)->post('/cart/items', ['product_id' => $this->product(['price' => 500, 'stock_qty' => 20])->id, 'qty' => 2]);
        $this->post('/cart/coupon', ['code' => 'MERIENDA10'])->assertSessionHasNoErrors();
        $this->post('/checkout', ['customer_name' => $ben->name, 'customer_email' => $ben->email, 'customer_phone' => '09171234567',
            'fulfillment_type' => 'pickup', 'delivery_mode' => 'pooling', 'payment_method' => 'bank_transfer'])->assertRedirect();
        $this->assertSame(1, Order::query()->where('customer_email', $ben->email)->where('coupon_code', 'MERIENDA10')->count());
        $this->assertSame(1, Coupon::query()->sole()->used_count);
    }

    public function test_a_coupon_used_up_between_apply_and_checkout_is_refused(): void
    {
        $coupon = Coupon::query()->create(['code' => 'LASTONE', 'type' => 'fixed', 'value' => 50, 'min_spend' => 0, 'usage_limit' => 1, 'used_count' => 0, 'is_active' => true]);
        $ana = User::factory()->customer()->create(['email_verified_at' => now()]);
        $this->actingAs($ana)->post('/cart/items', ['product_id' => $this->product(['price' => 300])->id, 'qty' => 1]);
        $this->post('/cart/coupon', ['code' => 'LASTONE'])->assertSessionHasNoErrors();

        $coupon->update(['used_count' => 1]); // someone else redeemed it meanwhile

        $this->post('/checkout', ['customer_name' => 'Ana', 'customer_email' => $ana->email, 'customer_phone' => '09171234567',
            'fulfillment_type' => 'pickup', 'delivery_mode' => 'pooling', 'payment_method' => 'bank_transfer'])->assertRedirect();
        $this->assertSame(0, Order::query()->where('coupon_code', 'LASTONE')->count());
        $this->assertSame(1, $coupon->fresh()->used_count);
    }
}
