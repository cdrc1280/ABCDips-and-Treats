<?php

namespace Tests\Feature\Storefront\Checkout;

use App\Contracts\PaymentGatewayInterface;
use App\Domains\Sales\Models\Order;
use App\Domains\User\Models\User;
use App\Services\Payments\PaymentManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Feature\Storefront\StorefrontTestHelpers;
use Tests\TestCase;

/** The payment starts after the order is committed: a broken gateway never loses or rolls back an order. */
class PaymentStartTest extends TestCase
{
    use RefreshDatabase, StorefrontTestHelpers;

    public function test_a_failing_gateway_leaves_a_valid_unpaid_order(): void
    {
        $this->withoutVite();
        Http::preventStrayRequests();
        $this->mock(PaymentManager::class, function ($mock) {
            $gateway = \Mockery::mock(PaymentGatewayInterface::class);
            $gateway->shouldReceive('charge')->andThrow(new \RuntimeException('PayMongo timed out'));
            $mock->shouldReceive('driver')->with('gcash')->andReturn($gateway);
        });

        $product = $this->product(['price' => 250, 'stock_qty' => 5]);
        $customer = User::factory()->customer()->create(['email_verified_at' => now()]);
        $this->actingAs($customer)->post('/cart/items', ['product_id' => $product->id, 'qty' => 2]);

        $this->post('/checkout', ['customer_name' => 'Ana Cruz', 'customer_email' => $customer->email, 'customer_phone' => '09171234567',
            'fulfillment_type' => 'pickup', 'delivery_mode' => 'priority', 'payment_method' => 'gcash'])->assertRedirect();

        $order = Order::query()->sole();
        $this->assertSame('pending', $order->payment_status);
        $this->assertSame(3, $product->fresh()->stock_qty);
    }
}
