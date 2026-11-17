<?php

namespace Tests\Feature\Sales\DeliveryPool;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Sales\Enums\DeliveryPoolPermission;
use App\Domains\Sales\Models\DeliveryPool;
use App\Domains\Sales\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Hand-written: assigning orders to a pool, re-allocating, settling and locking. */
class DeliveryPoolFlowTest extends TestCase
{
    use RefreshDatabase;

    private function poolingOrder(string $number, float $subtotal): Order
    {
        return Order::query()->create([
            'order_number' => $number, 'tracking_token' => "tok-{$number}", 'customer_name' => 'C', 'customer_email' => 'c@x.test', 'customer_phone' => '09171234567',
            'fulfillment_type' => 'delivery', 'delivery_mode' => 'pooling', 'pooling_status' => 'awaiting_assignment',
            'subtotal' => $subtotal, 'total' => $subtotal, 'payment_method' => 'gcash', 'payment_status' => 'paid', 'status' => 'pending',
        ]);
    }

    public function test_assign_reallocate_settle_and_lock(): void
    {
        $a = $this->poolingOrder('A', 500);
        $b = $this->poolingOrder('B', 300);
        $this->actingAs($this->userWith(AccessPermission::DashboardView, ...DeliveryPoolPermission::cases()));

        $this->post('/admin/delivery-pools', ['zone_name' => 'Imus', 'total_delivery_fee' => '150', 'status' => 'open', 'allocations' => [
            ['order_id' => (string) $a->id, 'assigned_fee' => '80'],
            ['order_id' => (string) $b->id, 'assigned_fee' => '70'],
        ]])->assertSessionHasNoErrors();
        $pool = DeliveryPool::query()->firstOrFail();
        $this->assertSame('pooled', $a->refresh()->pooling_status);
        $this->assertEquals(80, (float) $a->delivery_fee);

        // Re-allocate: B leaves the pool, A's share changes.
        $this->put("/admin/delivery-pools/{$pool->id}", ['pool_code' => $pool->pool_code, 'total_delivery_fee' => '100', 'status' => 'building', 'allocations' => [
            ['order_id' => (string) $a->id, 'assigned_fee' => '100'],
        ]])->assertSessionHasNoErrors();
        $this->assertNull($b->refresh()->delivery_pool_id);
        $this->assertSame('awaiting_assignment', $b->pooling_status);

        $this->from('/admin/delivery-pools')->patch("/admin/delivery-pools/{$pool->id}/settle")->assertInertiaFlash('toast.type', 'success');

        $a->refresh();
        $this->assertSame('settled', $a->pooling_status);
        $this->assertSame('confirmed', $a->status);
        $this->assertEquals(600, (float) $a->total);
        $this->assertSame('settled', $pool->refresh()->status);

        // Locked after settling.
        $this->from('/admin/delivery-pools')->put("/admin/delivery-pools/{$pool->id}", ['pool_code' => $pool->pool_code, 'total_delivery_fee' => '0', 'status' => 'open'])
            ->assertInertiaFlash('toast.type', 'error');
        $this->from('/admin/delivery-pools')->patch("/admin/delivery-pools/{$pool->id}/settle")->assertInertiaFlash('toast.type', 'error');
    }

    public function test_empty_pool_cannot_be_settled(): void
    {
        $pool = DeliveryPool::query()->create(['pool_code' => 'P-EMPTY', 'total_delivery_fee' => 0, 'status' => 'open']);
        $this->actingAs($this->userWith(AccessPermission::DashboardView, ...DeliveryPoolPermission::cases()));

        $this->from('/admin/delivery-pools')->patch("/admin/delivery-pools/{$pool->id}/settle")->assertInertiaFlash('toast.type', 'error');
        $this->assertSame('open', $pool->refresh()->status);
    }
}
