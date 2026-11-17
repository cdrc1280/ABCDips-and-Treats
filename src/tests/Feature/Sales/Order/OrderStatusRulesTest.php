<?php

namespace Tests\Feature\Sales\Order;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Sales\Enums\OrderPermission;
use App\Domains\Sales\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStatusRulesTest extends TestCase
{
    use RefreshDatabase;

    /** @param  array<string, mixed>  $attributes */
    private function order(array $attributes = []): Order
    {
        static $n = 0;
        $n++;

        return Order::query()->create(array_merge([
            'order_number' => "ORD-RULE-{$n}", 'tracking_token' => "tok-rule-{$n}", 'customer_name' => 'Ana', 'customer_email' => 'ana@example.test',
            'customer_phone' => '09171234567', 'fulfillment_type' => 'delivery', 'subtotal' => 300, 'total' => 300,
            'payment_method' => 'bank_transfer', 'payment_status' => 'pending', 'status' => 'pending',
        ], $attributes));
    }

    public function test_orders_move_forward_and_can_skip_steps_but_not_go_back(): void
    {
        $order = $this->order();
        $order->transitionTo(Order::STATUS_PREPARING);
        $this->assertSame('preparing', $order->fresh()->status);

        $this->expectException(\DomainException::class);
        $order->transitionTo(Order::STATUS_PENDING);
    }

    public function test_handover_step_matches_the_fulfilment_type(): void
    {
        $delivery = $this->order(['status' => 'packaging']);
        $pickup = $this->order(['status' => 'packaging', 'fulfillment_type' => 'pickup']);

        $this->assertContains('out_for_delivery', $delivery->allowedTransitions());
        $this->assertNotContains('ready_for_pickup', $delivery->allowedTransitions());
        $this->assertContains('ready_for_pickup', $pickup->allowedTransitions());
        $this->assertNotContains('out_for_delivery', $pickup->allowedTransitions());
    }

    public function test_final_states_only_allow_their_follow_ups(): void
    {
        $this->assertSame(['refunded', 'archived'], $this->order(['status' => 'completed'])->allowedTransitions());
        $this->assertSame(['pending', 'archived'], $this->order(['status' => 'cancelled'])->allowedTransitions());
        $this->assertSame([], $this->order(['status' => 'archived'])->allowedTransitions());
    }

    public function test_setting_the_same_status_records_nothing(): void
    {
        $order = $this->order();
        $order->transitionTo(Order::STATUS_PENDING);

        $this->assertSame(0, $order->statusHistories()->count());
    }

    public function test_a_stale_copy_cannot_cancel_twice(): void
    {
        $order = $this->order();
        $stale = Order::query()->findOrFail($order->id);

        $order->transitionTo(Order::STATUS_CANCELLED);
        $stale->transitionTo(Order::STATUS_CANCELLED); // re-reads the locked row: already cancelled → no-op

        $this->assertSame(1, $order->statusHistories()->where('to_status', 'cancelled')->count());
    }

    public function test_unpaid_e_wallet_orders_are_not_prepared(): void
    {
        $order = $this->order(['payment_method' => 'gcash', 'payment_status' => 'awaiting_payment']);
        $order->transitionTo(Order::STATUS_CONFIRMED); // confirming is fine

        try {
            $order->transitionTo(Order::STATUS_PREPARING);
            $this->fail('An unpaid GCash order was prepared.');
        } catch (\DomainException $e) {
            $this->assertStringContainsString('GCASH', $e->getMessage());
        }

        $order->update(['payment_status' => 'paid']);
        $order->transitionTo(Order::STATUS_PREPARING);
        $this->assertSame('preparing', $order->fresh()->status);
    }

    public function test_the_back_office_shows_a_friendly_error_for_an_invalid_move(): void
    {
        $order = $this->order(['status' => 'completed']);
        $staff = $this->userWith(AccessPermission::DashboardView, ...OrderPermission::cases());

        $this->actingAs($staff)->from('/admin/orders')->patch("/admin/orders/{$order->id}/change-status", ['status' => 'pending'])
            ->assertRedirect('/admin/orders')->assertInertiaFlash('toast.type', 'error');
        $this->assertSame('completed', $order->fresh()->status);
    }
}
