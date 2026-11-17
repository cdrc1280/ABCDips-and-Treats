<?php

namespace Tests\Feature\Sales\DeliveryPool;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Sales\Enums\DeliveryPoolPermission;
use App\Domains\Sales\Models\DeliveryPool;
use App\Domains\Sales\Models\Order;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Generated from the Category reference module (tests/Feature/Catalog/Category). */
class DeliveryPoolTest extends TestCase
{
    use RefreshDatabase;

    private function admin(DeliveryPoolPermission ...$permissions): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: DeliveryPoolPermission::cases()));
    }

    /** @param  array<string, mixed>  $attributes */
    private function record(array $attributes = []): DeliveryPool
    {
        static $n = 0;
        $n++;

        return DeliveryPool::query()->create(array_merge(['pool_code' => "POOL-{$n}", 'zone_name' => "Zone {$n}", 'total_delivery_fee' => 300, 'status' => 'open'], $attributes));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/delivery-pools')->assertRedirect('/login');
        $this->post('/admin/delivery-pools', [])->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/delivery-pools')->assertForbidden();
    }

    public function test_view_only_user_cannot_change_anything(): void
    {
        $record = $this->record();
        $this->actingAs($this->admin(DeliveryPoolPermission::DeliveryPoolsView));

        $this->get('/admin/delivery-pools')->assertOk();
        $this->get('/admin/delivery-pools/create')->assertForbidden();
        $this->get("/admin/delivery-pools/{$record->id}/edit")->assertForbidden();
        $this->put("/admin/delivery-pools/{$record->id}", [])->assertForbidden();
        $this->delete("/admin/delivery-pools/{$record->id}")->assertForbidden();
    }

    public function test_index_renders_records_and_deferred_summary(): void
    {
        $this->record();
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/delivery-pools')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('delivery-pool/index')
                ->has('records.data', 2)
                ->where('table.name', 'delivery_pools')
                ->missing('summary')
                ->loadDeferredProps(fn (Assert $reload) => $reload->has('summary', 3)));
    }

    public function test_store_persists_and_flashes_a_toast(): void
    {
        $order = Order::query()->create(['order_number' => 'ORD-P1', 'tracking_token' => 'tp1', 'customer_name' => 'Pia', 'customer_email' => 'p@x.test', 'customer_phone' => '09171234567', 'fulfillment_type' => 'delivery', 'delivery_mode' => 'pooling', 'pooling_status' => 'awaiting_assignment', 'subtotal' => 500, 'total' => 500, 'payment_method' => 'gcash', 'payment_status' => 'paid', 'status' => 'pending']);

        $this->actingAs($this->admin())
            ->post('/admin/delivery-pools', ['zone_name' => 'Imus', 'total_delivery_fee' => '200', 'status' => 'open', 'allocations' => [['order_id' => (string) $order->id, 'assigned_fee' => '80']]])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/delivery-pools')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('delivery_pools', ['zone_name' => 'Imus', 'total_delivery_fee' => 200]);
    }

    public function test_unique_pool_code_ignores_soft_deleted_rows(): void
    {
        $order = Order::query()->create(['order_number' => 'ORD-P1', 'tracking_token' => 'tp1', 'customer_name' => 'Pia', 'customer_email' => 'p@x.test', 'customer_phone' => '09171234567', 'fulfillment_type' => 'delivery', 'delivery_mode' => 'pooling', 'pooling_status' => 'awaiting_assignment', 'subtotal' => 500, 'total' => 500, 'payment_method' => 'gcash', 'payment_status' => 'paid', 'status' => 'pending']);

        $existing = $this->record();
        $payload = array_merge(['zone_name' => 'Imus', 'total_delivery_fee' => '200', 'status' => 'open', 'allocations' => [['order_id' => (string) $order->id, 'assigned_fee' => '80']]], ['pool_code' => $existing->pool_code]);

        $this->actingAs($this->admin())->post('/admin/delivery-pools', $payload)->assertSessionHasErrors(['pool_code']);

        $existing->delete();
        $this->post('/admin/delivery-pools', $payload)->assertSessionHasNoErrors();
    }

    public function test_edit_renders_and_update_persists(): void
    {
        $record = $this->record();
        $order = Order::query()->create(['order_number' => 'ORD-P1', 'tracking_token' => 'tp1', 'customer_name' => 'Pia', 'customer_email' => 'p@x.test', 'customer_phone' => '09171234567', 'fulfillment_type' => 'delivery', 'delivery_mode' => 'pooling', 'pooling_status' => 'awaiting_assignment', 'subtotal' => 500, 'total' => 500, 'payment_method' => 'gcash', 'payment_status' => 'paid', 'status' => 'pending']);

        $this->actingAs($this->admin())
            ->get("/admin/delivery-pools/{$record->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('delivery-pool/edit')->where('record.id', $record->id));

        $this->put("/admin/delivery-pools/{$record->id}", ['pool_code' => 'POOL-EDIT', 'zone_name' => 'Imus', 'total_delivery_fee' => '250', 'status' => 'building', 'allocations' => [['order_id' => (string) $order->id, 'assigned_fee' => '90']]])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/delivery-pools')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('delivery_pools', array_merge(['id' => $record->id], ['pool_code' => 'POOL-EDIT', 'status' => 'building']));
    }

    public function test_delete_soft_deletes(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->delete("/admin/delivery-pools/{$record->id}")->assertRedirect()->assertInertiaFlash('toast.type', 'success');

        $this->assertSoftDeleted($record);
    }

    public function test_index_search_finds_by_keyword(): void
    {
        $this->record(['zone_name' => 'Bacoor Molino']);
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/delivery-pools?search[keyword]=molino')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1));
    }
}
