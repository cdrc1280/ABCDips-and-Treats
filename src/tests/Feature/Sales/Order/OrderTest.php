<?php

namespace Tests\Feature\Sales\Order;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Sales\Enums\OrderPermission;
use App\Domains\Sales\Models\Order;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Generated from the Category reference module (tests/Feature/Catalog/Category). */
class OrderTest extends TestCase
{
    use RefreshDatabase;

    private function admin(OrderPermission ...$permissions): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: OrderPermission::cases()));
    }

    /** @param  array<string, mixed>  $attributes */
    private function record(array $attributes = []): Order
    {
        static $n = 0;
        $n++;

        return Order::query()->create(array_merge(['order_number' => "ORD-{$n}", 'tracking_token' => "tok-{$n}", 'customer_name' => "Customer {$n}", 'customer_email' => "c{$n}@example.test", 'customer_phone' => '09171234567', 'fulfillment_type' => 'pickup', 'subtotal' => 300, 'total' => 300, 'payment_method' => 'gcash', 'payment_status' => 'paid', 'status' => 'pending'], $attributes));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/orders')->assertRedirect('/login');
        $this->put('/admin/orders/1', [])->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/orders')->assertForbidden();
    }

    public function test_view_only_user_cannot_change_anything(): void
    {
        $record = $this->record();
        $this->actingAs($this->admin(OrderPermission::OrdersView));

        $this->get('/admin/orders')->assertOk();
        $this->get("/admin/orders/{$record->id}/edit")->assertForbidden();
        $this->put("/admin/orders/{$record->id}", [])->assertForbidden();
    }

    public function test_index_renders_records_and_deferred_summary(): void
    {
        $this->record();
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/orders')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('order/index')
                ->has('records.data', 2)
                ->where('table.name', 'orders')
                ->missing('summary')
                ->loadDeferredProps(fn (Assert $reload) => $reload->has('summary', 4)));
    }

    public function test_edit_renders_and_update_persists(): void
    {
        $record = $this->record();

        $this->actingAs($this->admin())
            ->get("/admin/orders/{$record->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('order/edit')->where('record.id', $record->id));

        $this->put("/admin/orders/{$record->id}", ['customer_name' => 'Edited Name', 'customer_email' => 'e@example.test', 'customer_phone' => '09171234567', 'payment_status' => 'paid', 'payment_reference' => 'GC-123'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/orders')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('orders', array_merge(['id' => $record->id], ['customer_name' => 'Edited Name', 'payment_reference' => 'GC-123']));
    }

    public function test_update_requires_an_11_digit_phone_and_a_4_digit_postal_code(): void
    {
        $record = $this->record();
        $payload = ['customer_name' => 'Ana', 'customer_email' => 'e@example.test', 'payment_status' => 'paid'];

        $this->actingAs($this->admin())
            ->put("/admin/orders/{$record->id}", $payload + ['customer_phone' => '+639171234567', 'postal_code' => '41O2'])
            ->assertSessionHasErrors(['customer_phone', 'postal_code']);

        $this->put("/admin/orders/{$record->id}", $payload + ['customer_phone' => '09171234567', 'postal_code' => '4102'])
            ->assertSessionHasNoErrors();
    }

    public function test_change_status(): void
    {
        $record = $this->record(['status' => 'pending']);

        $this->actingAs($this->admin())->from('/admin/orders')->patch("/admin/orders/{$record->id}/change-status", ['status' => 'confirmed'])->assertSessionHasNoErrors()->assertRedirect('/admin/orders');

        $this->assertSame('confirmed', $record->refresh()->status);
        $this->assertDatabaseHas('order_status_histories', ['order_id' => $record->id, 'from_status' => 'pending', 'to_status' => 'confirmed']);
    }

    public function test_index_search_finds_by_keyword(): void
    {
        $this->record(['customer_name' => 'Imelda Santos']);
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/orders?search[keyword]=imelda')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1));
    }
}
