<?php

namespace Tests\Feature;

use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductCategory;
use App\Domains\Sales\Models\Cart;
use App\Domains\Sales\Models\CartItem;
use App\Domains\Sales\Models\DeliveryPool;
use App\Domains\Sales\Models\Order;
use App\Domains\User\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeliveryPoolingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);

        $category = ProductCategory::create(['name' => 'Pastries', 'slug' => 'pastries']);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Brownie Dip Box',
            'slug' => 'brownie-dip-box',
            'sku' => 'PRD-BRN-01',
            'price' => 200.00,
            'stock_qty' => 50,
            'is_active' => true,
        ]);
    }

    public function test_checkout_with_priority_mode_applies_full_fee(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole('customer');

        $cart = Cart::create(['user_id' => $user->id, 'token' => Str::random(40), 'last_active_at' => now(), 'expires_at' => now()->addDays(30)]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => Product::first()->id,
            'qty' => 1,
            'unit_price' => 200.00,
            'subtotal' => 200.00,
        ]);

        $orderService = app(OrderService::class);
        $order = $orderService->createOrderFromCart($cart, [
            'customer_name' => 'Priority Customer',
            'customer_email' => 'priority@example.com',
            'customer_phone' => '09171234567',
            'fulfillment_type' => 'delivery',
            'delivery_mode' => 'priority',
            'delivery_address' => '123 Priority St',
            'city' => 'Bacoor',
            'payment_method' => 'cod',
            'shipping_fee' => 120.00,
        ], $user);

        $this->assertEquals(Order::MODE_PRIORITY, $order->delivery_mode);
        $this->assertEquals(Order::POOLING_NOT_POOLED, $order->pooling_status);
        $this->assertEquals(120.00, $order->delivery_fee);
        $this->assertEquals(320.00, $order->total);
    }

    public function test_checkout_with_pooling_mode_sets_awaiting_assignment_and_estimated_fee(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole('customer');

        $cart = Cart::create(['user_id' => $user->id, 'token' => Str::random(40), 'last_active_at' => now(), 'expires_at' => now()->addDays(30)]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => Product::first()->id,
            'qty' => 1,
            'unit_price' => 200.00,
            'subtotal' => 200.00,
        ]);

        $orderService = app(OrderService::class);
        $order = $orderService->createOrderFromCart($cart, [
            'customer_name' => 'Pooled Customer 1',
            'customer_email' => 'pool1@example.com',
            'customer_phone' => '09171234567',
            'fulfillment_type' => 'delivery',
            'delivery_mode' => 'pooling',
            'delivery_address' => '456 Molino Rd',
            'city' => 'Bacoor',
            'payment_method' => 'cod',
            'shipping_fee' => 120.00,
        ], $user);

        $this->assertEquals(Order::MODE_POOLING, $order->delivery_mode);
        $this->assertEquals(Order::POOLING_AWAITING_ASSIGNMENT, $order->pooling_status);
        $this->assertEquals(0.00, $order->delivery_fee);
        $this->assertEquals(200.00, $order->total);
    }

    public function test_admin_settling_delivery_pool_splits_fees_and_updates_orders(): void
    {
        $order1 = Order::create([
            'order_number' => 'ABCD-TEST-001',
            'tracking_token' => 'token1',
            'customer_name' => 'Customer A',
            'customer_email' => 'a@example.com',
            'customer_phone' => '09171234567',
            'fulfillment_type' => 'delivery',
            'delivery_mode' => Order::MODE_POOLING,
            'pooling_status' => Order::POOLING_AWAITING_ASSIGNMENT,
            'delivery_address' => 'Addr A (Near Store)',
            'city' => 'Bacoor',
            'subtotal' => 300.00,
            'discount_amount' => 0.00,
            'delivery_fee' => 0.00,
            'total' => 300.00,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'status' => Order::STATUS_PENDING,
        ]);

        $order2 = Order::create([
            'order_number' => 'ABCD-TEST-002',
            'tracking_token' => 'token2',
            'customer_name' => 'Customer B',
            'customer_email' => 'b@example.com',
            'customer_phone' => '09181234567',
            'fulfillment_type' => 'delivery',
            'delivery_mode' => Order::MODE_POOLING,
            'pooling_status' => Order::POOLING_AWAITING_ASSIGNMENT,
            'delivery_address' => 'Addr B (Border Area - Farther)',
            'city' => 'Bacoor Border',
            'subtotal' => 400.00,
            'discount_amount' => 0.00,
            'delivery_fee' => 0.00,
            'total' => 400.00,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'status' => Order::STATUS_PENDING,
        ]);

        $pool = DeliveryPool::create([
            'pool_code' => 'POOL-TEST-01',
            'city' => 'Bacoor',
            'zone_name' => 'Molino Corridor',
            'total_delivery_fee' => 125.00,
            'shared_fee_per_order' => 62.50,
            'status' => DeliveryPool::STATUS_OPEN,
        ]);

        $order1->update(['delivery_pool_id' => $pool->id, 'pooling_status' => Order::POOLING_POOLED]);
        $order2->update(['delivery_pool_id' => $pool->id, 'pooling_status' => Order::POOLING_POOLED]);

        // Admin settles pool with custom per-customer rates (Customer A near store = ₱50, Customer B border area = ₱75)
        $customFees = [
            $order1->id => 50.00,
            $order2->id => 75.00,
        ];

        foreach ($pool->orders as $o) {
            $fee = $customFees[$o->id];
            $newTotal = max(0.0, round($o->subtotal - $o->discount_amount + $fee, 2));
            $o->update([
                'delivery_fee' => $fee,
                'total' => $newTotal,
                'pooling_status' => Order::POOLING_SETTLED,
                'status' => Order::STATUS_CONFIRMED,
            ]);
        }

        $order1->refresh();
        $order2->refresh();

        $this->assertEquals(50.00, $order1->delivery_fee);
        $this->assertEquals(350.00, $order1->total);
        $this->assertEquals(Order::POOLING_SETTLED, $order1->pooling_status);
        $this->assertEquals(Order::STATUS_CONFIRMED, $order1->status);

        $this->assertEquals(75.00, $order2->delivery_fee);
        $this->assertEquals(475.00, $order2->total);
    }

    public function test_unsettled_pooled_order_cannot_advance_status(): void
    {
        $order = Order::create([
            'order_number' => 'ABCD-TEST-UNSETTLED',
            'tracking_token' => 'token_unsettled',
            'customer_name' => 'Pending Pool Customer',
            'customer_email' => 'unsettled@example.com',
            'customer_phone' => '09171234567',
            'fulfillment_type' => 'delivery',
            'delivery_mode' => Order::MODE_POOLING,
            'pooling_status' => Order::POOLING_AWAITING_ASSIGNMENT,
            'delivery_address' => 'Sample Address',
            'city' => 'Bacoor',
            'subtotal' => 300.00,
            'discount_amount' => 0.00,
            'delivery_fee' => 0.00,
            'total' => 300.00,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'status' => Order::STATUS_PENDING,
        ]);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('awaiting admin assignment');

        $order->transitionTo(Order::STATUS_CONFIRMED);
    }

    public function test_payment_creation_is_blocked_until_pooling_is_settled(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole('customer');

        $order = Order::create([
            'order_number' => 'ABCD-PAY-LOCK',
            'tracking_token' => 'token_pay_lock',
            'user_id' => $user->id,
            'customer_name' => 'Pending Pool Customer',
            'customer_email' => 'unsettled@example.com',
            'customer_phone' => '09171234567',
            'fulfillment_type' => 'delivery',
            'delivery_mode' => Order::MODE_POOLING,
            'pooling_status' => Order::POOLING_AWAITING_ASSIGNMENT,
            'delivery_address' => 'Sample Address',
            'city' => 'Bacoor',
            'subtotal' => 300.00,
            'discount_amount' => 0.00,
            'delivery_fee' => 0.00,
            'total' => 300.00,
            'payment_method' => 'gcash',
            'payment_status' => 'pending',
            'status' => Order::STATUS_PENDING,
        ]);

        // Attempt payment when pooling is not settled -> refused
        $this->actingAs($user)->postJson('/orders/token_pay_lock/pay')
            ->assertStatus(422)
            ->assertJsonFragment(['message' => 'This order cannot be paid right now.']);
        $this->assertSame('pending', $order->fresh()->payment_status);

        // Settle pooling rate
        $order->update([
            'delivery_fee' => 50.00,
            'total' => 350.00,
            'pooling_status' => Order::POOLING_SETTLED,
        ]);

        // Attempt payment after pooling is settled -> the payment starts
        $this->actingAs($user)->post('/orders/token_pay_lock/pay')->assertRedirect();
        $this->assertSame('awaiting_payment', $order->fresh()->payment_status);
    }
}
