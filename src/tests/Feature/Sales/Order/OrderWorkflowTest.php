<?php

namespace Tests\Feature\Sales\Order;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Sales\Enums\OrderPermission;
use App\Domains\Sales\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Hand-written: business rules of the order workflow surface as error toasts, and the detail panel data. */
class OrderWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function order(array $attributes = []): Order
    {
        return Order::query()->create(array_merge([
            'order_number' => 'ORD-1', 'tracking_token' => 'tok', 'customer_name' => 'A', 'customer_email' => 'a@a.test', 'customer_phone' => '09171234567',
            'fulfillment_type' => 'delivery', 'subtotal' => 300, 'total' => 300, 'payment_method' => 'gcash', 'payment_status' => 'paid', 'status' => 'pending',
        ], $attributes));
    }

    public function test_unsettled_pooling_order_cannot_advance(): void
    {
        $order = $this->order(['delivery_mode' => 'pooling', 'pooling_status' => 'awaiting_assignment']);
        $this->actingAs($this->userWith(AccessPermission::DashboardView, ...OrderPermission::cases()));

        $this->from('/admin/orders')->patch("/admin/orders/{$order->id}/change-status", ['status' => 'confirmed'])
            ->assertRedirect('/admin/orders')
            ->assertInertiaFlash('toast.type', 'error');

        $this->assertSame('pending', $order->refresh()->status);
    }

    public function test_status_must_be_a_known_value(): void
    {
        $order = $this->order();
        $this->actingAs($this->userWith(AccessPermission::DashboardView, ...OrderPermission::cases()));

        $this->patch("/admin/orders/{$order->id}/change-status", ['status' => 'teleported'])->assertSessionHasErrors('status');
    }

    public function test_edit_page_carries_items_and_history(): void
    {
        $order = $this->order();
        $order->items()->create(['product_name' => 'Banana loaf', 'product_sku' => 'BL', 'qty' => 2, 'unit_price' => 150, 'subtotal' => 300]);
        $order->transitionTo('confirmed', 'Called the customer');
        $this->actingAs($this->userWith(AccessPermission::DashboardView, ...OrderPermission::cases()));

        $this->get("/admin/orders/{$order->id}/edit")->assertInertia(fn (Assert $page) => $page
            ->component('order/edit')
            ->where('record.order_number', 'ORD-1')
            ->has('record.items', 1)
            ->where('record.items.0.qty', 2)
            ->has('record.history', 1)
            ->where('record.history.0.comment', 'Called the customer'));
    }
}
