<?php

namespace Tests\Feature\Storefront\Checkout;

use App\Domains\Sales\Models\Order;
use App\Domains\Setting\Models\Setting;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Feature\Storefront\StorefrontTestHelpers;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase, StorefrontTestHelpers;

    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Http::preventStrayRequests();
        Http::fake(['nominatim.openstreetmap.org/*' => Http::response([], 200)]);
        Setting::set('store_lat', '14.4597');
        Setting::set('store_lng', '120.9640');
        $this->customer = User::factory()->customer()->create(['email_verified_at' => now()]);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return [
            'customer_name' => 'Ana Cruz',
            'customer_email' => 'ana@example.com',
            'customer_phone' => '09171234567',
            'fulfillment_type' => 'delivery',
            'delivery_mode' => 'priority',
            'payment_method' => 'bank_transfer',
            'region' => 'Region IV-A', 'province' => 'Cavite', 'city' => 'Bacoor', 'barangay' => 'Molino III', 'street_address' => 'Blk 1 Lot 2',
            'lat' => 14.40, 'lng' => 120.98,
            'shipping_fee' => 0, 'delivery_fee' => 0, 'total' => 1,
            ...$overrides,
        ];
    }

    private function fillCart(int $qty = 2, float $price = 300): void
    {
        $this->actingAs($this->customer)->post('/cart/items', ['product_id' => $this->product(['price' => $price, 'stock_qty' => 10])->id, 'qty' => $qty]);
    }

    public function test_checkout_page_requires_login_and_a_cart(): void
    {
        $this->get('/checkout')->assertRedirect('/login');
        $this->actingAs($this->customer)->get('/checkout')->assertRedirect('/cart');

        $this->fillCart();
        $this->get('/checkout')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('storefront/checkout')->where('emailVerified', true)->where('store.lat', 14.4597));
    }

    public function test_priority_delivery_fee_is_computed_on_the_server(): void
    {
        $this->fillCart();

        $this->post('/checkout', $this->payload())->assertRedirect();

        $order = Order::query()->sole();
        $this->assertGreaterThanOrEqual(80, (float) $order->delivery_fee);
        $this->assertEquals(600 + (float) $order->delivery_fee, (float) $order->total);
        $this->assertSame('Blk 1 Lot 2, Molino III, Bacoor, Cavite', $order->delivery_address);
        $this->assertSame(8, $order->items->first()->product->fresh()->stock_qty);
    }

    public function test_pickup_and_pooling_have_no_fee_and_pooling_is_not_charged_yet(): void
    {
        $this->fillCart();
        $this->post('/checkout', $this->payload(['delivery_mode' => 'pooling', 'payment_method' => 'gcash']))->assertRedirect();

        $order = Order::query()->sole();
        $this->assertEquals(0, (float) $order->delivery_fee);
        $this->assertSame(Order::POOLING_AWAITING_ASSIGNMENT, $order->pooling_status);
        $this->assertNull($order->payment_reference);
    }

    public function test_unverified_customers_cannot_place_orders(): void
    {
        $this->customer->forceFill(['email_verified_at' => null])->save();
        $this->fillCart();

        $this->post('/checkout', $this->payload())->assertRedirect();
        $this->assertSame(0, Order::query()->count());
    }

    public function test_payment_return_cannot_be_forged_from_the_query_string(): void
    {
        $this->fillCart();
        $this->post('/checkout', $this->payload(['payment_method' => 'bank_transfer']));
        $order = Order::query()->sole();

        // A different browser hitting the return URL with the order id learns nothing and pays nothing.
        $this->app['auth']->forgetGuards();
        $this->flushSession();
        $this->get("/checkout/payment-success?order={$order->id}&source_id=src_someone_elses")->assertRedirect('/');
        $this->assertNotSame('paid', $order->fresh()->payment_status);
    }

    public function test_gcash_return_captures_the_source_and_checks_the_amount(): void
    {
        Setting::set('paymongo_secret_key', 'sk_test_x');
        Http::fake([
            'api.paymongo.com/v1/sources' => Http::response(['data' => ['id' => 'src_1', 'attributes' => ['status' => 'pending', 'redirect' => ['checkout_url' => 'https://pm.test/checkout']]]]),
            'api.paymongo.com/v1/sources/src_1' => Http::response(['data' => ['id' => 'src_1', 'attributes' => ['status' => 'chargeable', 'amount' => 60000]]]),
            'api.paymongo.com/v1/payments' => Http::response(['data' => ['id' => 'pay_1', 'attributes' => ['status' => 'paid']]]),
        ]);
        $this->fillCart();

        $this->post('/checkout', $this->payload(['fulfillment_type' => 'pickup', 'payment_method' => 'gcash']))
            ->assertRedirect('https://pm.test/checkout');
        $this->assertSame('awaiting_payment', Order::query()->sole()->payment_status);

        $this->get('/checkout/payment-success')->assertRedirect();

        $order = Order::query()->sole();
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('pay_1', $order->payment_reference);
        $this->assertSame(Order::STATUS_CONFIRMED, $order->status);
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/payments') && $request['data']['attributes']['amount'] === 60000);
    }

    public function test_mismatched_source_amount_is_not_marked_paid(): void
    {
        Setting::set('paymongo_secret_key', 'sk_test_x');
        Http::fake([
            'api.paymongo.com/v1/sources' => Http::response(['data' => ['id' => 'src_1', 'attributes' => ['status' => 'pending', 'redirect' => ['checkout_url' => 'https://pm.test/checkout']]]]),
            'api.paymongo.com/v1/sources/src_1' => Http::response(['data' => ['id' => 'src_1', 'attributes' => ['status' => 'chargeable', 'amount' => 100]]]),
        ]);
        $this->fillCart();
        $this->post('/checkout', $this->payload(['fulfillment_type' => 'pickup', 'payment_method' => 'gcash']));

        $this->get('/checkout/payment-success');

        $this->assertNotSame('paid', Order::query()->sole()->payment_status);
        Http::assertNotSent(fn ($request) => str_ends_with($request->url(), '/payments'));
    }

    public function test_orders_are_only_visible_by_token_or_number_plus_email(): void
    {
        $this->fillCart();
        $this->post('/checkout', $this->payload(['fulfillment_type' => 'pickup']));
        $order = Order::query()->sole();

        $this->app['auth']->forgetGuards();
        $this->get("/track/{$order->id}")->assertNotFound();
        $this->get("/track/{$order->order_number}")->assertNotFound();
        $this->get("/track/{$order->tracking_token}")->assertOk()->assertInertia(fn (Assert $page) => $page->where('order.order_number', $order->order_number));

        $this->post('/track', ['order_number' => $order->order_number, 'email' => 'wrong@example.com'])->assertSessionHasErrors('order_number');
        $this->post('/track', ['order_number' => $order->order_number, 'email' => 'ANA@example.com'])->assertRedirect("/track/{$order->tracking_token}");

        $other = User::factory()->customer()->create();
        $this->actingAs($other)->get("/account/orders/{$order->id}")->assertNotFound();
        $this->post("/account/orders/{$order->id}/cancel")->assertNotFound();

        // ...while the owner can open (and cancel) their own order.
        $this->actingAs($this->customer)->get("/account/orders/{$order->id}")->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('account/order')->where('order.order_number', $order->order_number));
        $this->get('/account/orders/not-a-uuid')->assertNotFound();
    }

    public function test_customer_cancel_restocks_once(): void
    {
        $this->fillCart(qty: 3);
        $this->post('/checkout', $this->payload(['fulfillment_type' => 'pickup']));
        $order = Order::query()->sole();
        $product = $order->items->first()->product;
        $this->assertSame(7, $product->fresh()->stock_qty);

        $this->post("/account/orders/{$order->id}/cancel")->assertRedirect();
        $this->post("/account/orders/{$order->id}/cancel")->assertRedirect();

        $this->assertSame(Order::STATUS_CANCELLED, $order->fresh()->status);
        $this->assertSame(10, $product->fresh()->stock_qty);
    }

    public function test_account_pages_render(): void
    {
        $this->actingAs($this->customer);
        $this->get('/account/orders')->assertOk()->assertInertia(fn (Assert $page) => $page->component('account/orders'));
        $this->get('/account/profile')->assertOk()->assertInertia(fn (Assert $page) => $page->component('account/profile')->where('profile.email_verified', true));

        $this->put('/account/profile', ['name' => 'Ana C', 'email' => $this->customer->email, 'phone' => '09171234567', 'city' => 'Bacoor', 'barangay' => 'Molino', 'street_address' => '1 Main'])
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('1 Main, Molino, Bacoor', $this->customer->fresh()->address);
    }

    public function test_self_verify_email_backdoor_is_gone_from_the_storefront(): void
    {
        $this->customer->forceFill(['email_verified_at' => null])->save();

        $status = $this->actingAs($this->customer)->post('/account/verify-email')->getStatusCode();
        $this->assertGreaterThanOrEqual(400, $status);
        $this->assertNull($this->customer->fresh()->email_verified_at);
    }
}
