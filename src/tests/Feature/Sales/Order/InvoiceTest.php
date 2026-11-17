<?php

namespace Tests\Feature\Sales\Order;

use App\Domains\Sales\Enums\OrderPermission;
use App\Domains\Sales\Models\Order;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    /** @param  array<string, mixed>  $attributes */
    private function order(array $attributes = []): Order
    {
        return Order::query()->create(array_merge([
            'order_number' => 'ORD-INV-1', 'tracking_token' => 'secret-token', 'customer_name' => 'Ana', 'customer_email' => 'ana@example.test',
            'customer_phone' => '09171234567', 'fulfillment_type' => 'pickup', 'subtotal' => 300, 'total' => 300,
            'payment_method' => 'gcash', 'payment_status' => 'paid', 'status' => 'confirmed',
        ], $attributes));
    }

    public function test_guests_need_the_matching_tracking_token(): void
    {
        $order = $this->order();

        $this->get("/order-invoice/{$order->id}")->assertUnauthorized();
        $this->get("/order-invoice/{$order->id}?token=wrong")->assertUnauthorized();
        $this->get("/order-invoice/{$order->id}?token=secret-token")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('storefront/invoice')
                ->where('invoice.order_number', 'ORD-INV-1')
                ->where('invoice.total', 300)
                ->where('paper', 'a4')
                ->where('autoPrint', false));
    }

    public function test_other_customers_are_forbidden_but_the_owner_and_staff_are_not(): void
    {
        $owner = User::factory()->create();
        $order = $this->order(['user_id' => $owner->id]);

        $this->actingAs(User::factory()->create())->get("/order-invoice/{$order->id}")->assertForbidden();
        $this->actingAs($owner)->get("/order-invoice/{$order->id}")->assertOk();
        $this->actingAs($this->userWith(OrderPermission::OrdersView))->get("/order-invoice/{$order->id}?paper=pos&print=1")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('storefront/invoice')->where('paper', 'pos')->where('autoPrint', true));
    }

    public function test_unsettled_pooling_invoice_is_locked_for_customers_only(): void
    {
        $order = $this->order(['delivery_mode' => Order::MODE_POOLING, 'pooling_status' => 'not_pooled']);

        $this->get("/order-invoice/{$order->id}?token=secret-token")->assertForbidden();
        $this->actingAs($this->userWith(OrderPermission::OrdersView))->get("/order-invoice/{$order->id}")->assertOk();
    }

    public function test_old_download_links_open_the_print_page_and_keep_access_checks(): void
    {
        $order = $this->order();

        $this->get("/order-invoice/{$order->id}/download?token=wrong")->assertUnauthorized();
        $this->get("/order-invoice/{$order->id}/download?token=secret-token")
            ->assertRedirect("/order-invoice/{$order->id}?token=secret-token&print=1");
    }
}
