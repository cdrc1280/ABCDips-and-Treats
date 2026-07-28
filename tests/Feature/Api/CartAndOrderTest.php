<?php

namespace Tests\Feature\Api;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartAndOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ProductSeeder::class);
    }

    public function test_can_get_cart(): void
    {
        $response = $this->getJson('/api/cart');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'token', 'subtotal', 'total', 'item_count', 'items']
            ]);
    }

    public function test_can_add_item_to_cart(): void
    {
        $product = Product::first();

        $response = $this->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'qty'        => 2,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.item_count', 2)
            ->assertJsonPath('data.subtotal', (int) round($product->effective_price * 2));
    }

    public function test_can_update_cart_item(): void
    {
        $product = Product::first();

        $addRes = $this->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'qty'        => 1,
        ]);

        $token  = $addRes->json('data.token');
        $itemId = $addRes->json('data.items.0.id');

        $response = $this->withHeader('X-Cart-Token', $token)
            ->putJson("/api/cart/items/{$itemId}", ['qty' => 5]);

        $response->assertStatus(200)
            ->assertJsonPath('data.item_count', 5);
    }

    public function test_can_soft_delete_and_restore_cart_item(): void
    {
        $product = Product::first();

        $addRes = $this->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'qty'        => 2,
        ]);

        $token  = $addRes->json('data.token');
        $itemId = $addRes->json('data.items.0.id');

        // Delete
        $delRes = $this->withHeader('X-Cart-Token', $token)
            ->deleteJson("/api/cart/items/{$itemId}");
        $delRes->assertStatus(200)->assertJsonPath('data.item_count', 0);

        // Restore
        $restoreRes = $this->withHeader('X-Cart-Token', $token)
            ->postJson("/api/cart/items/{$itemId}/restore");
        $restoreRes->assertStatus(200)->assertJsonPath('data.item_count', 2);
    }

    public function test_can_apply_valid_coupon(): void
    {
        $product = Product::first();

        $addRes = $this->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'qty'        => 2,
        ]);
        $token = $addRes->json('data.token');

        // Seed coupon
        Coupon::create([
            'code'      => 'WELCOME50',
            'type'      => 'fixed',
            'value'     => 50.00,
            'is_active' => true,
        ]);

        $response = $this->withHeader('X-Cart-Token', $token)
            ->postJson('/api/cart/coupon', ['code' => 'WELCOME50']);

        $response->assertStatus(200)
            ->assertJsonPath('data.coupon_code', 'WELCOME50')
            ->assertJsonPath('data.discount_amount', 50);
    }

    public function test_can_checkout_and_create_order(): void
    {
        $product = Product::first();

        $addRes = $this->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'qty'        => 2,
        ]);
        $token = $addRes->json('data.token');

        $response = $this->withHeader('X-Cart-Token', $token)
            ->postJson('/api/checkout', [
                'customer_name'    => 'Juana Dela Cruz',
                'customer_email'   => 'juana@abcdips.test',
                'customer_phone'   => '09171234567',
                'fulfillment_type' => 'delivery',
                'delivery_address' => '123 Katipunan Ave, Loyola Heights',
                'city'             => 'Quezon City',
                'payment_method'   => 'gcash',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'order_number', 'tracking_token', 'status', 'total']
            ]);

        $this->assertDatabaseHas('orders', ['customer_email' => 'juana@abcdips.test']);
    }

    public function test_can_track_order_by_token(): void
    {
        $product = Product::first();

        $addRes = $this->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'qty'        => 1,
        ]);
        $token = $addRes->json('data.token');

        $checkoutRes = $this->withHeader('X-Cart-Token', $token)
            ->postJson('/api/checkout', [
                'customer_name'    => 'Juana Dela Cruz',
                'customer_email'   => 'juana@abcdips.test',
                'customer_phone'   => '09171234567',
                'fulfillment_type' => 'pickup',
                'payment_method'   => 'maya',
            ]);

        $trackingToken = $checkoutRes->json('data.tracking_token');

        $trackRes = $this->getJson("/api/orders/track/{$trackingToken}");

        $trackRes->assertStatus(200)
            ->assertJsonPath('data.customer_name', 'Juana Dela Cruz');
    }

    public function test_can_advance_order_status_pipeline(): void
    {
        $order = Order::create([
            'order_number'      => 'ABCD-TEST-001',
            'tracking_token'    => 'TOKEN-TEST-001',
            'customer_name'     => 'Test Customer',
            'customer_email'    => 'test@abcdips.test',
            'customer_phone'    => '09170000000',
            'fulfillment_type'  => 'delivery',
            'subtotal'          => 280.00,
            'total'             => 400.00,
            'payment_method'    => 'gcash',
            'payment_status'    => 'paid',
            'status'            => Order::STATUS_PENDING,
        ]);

        $orderService = app(\App\Services\OrderService::class);
        $orderService->updateOrderStatus($order, Order::STATUS_CONFIRMED, 'Confirmed by kitchen.');

        $this->assertEquals(Order::STATUS_CONFIRMED, $order->fresh()->status);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id'  => $order->id,
            'to_status' => Order::STATUS_CONFIRMED,
        ]);
    }
}
