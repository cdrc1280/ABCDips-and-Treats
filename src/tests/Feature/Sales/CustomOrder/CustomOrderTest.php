<?php

namespace Tests\Feature\Sales\CustomOrder;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Sales\Enums\CustomOrderPermission;
use App\Domains\Sales\Models\CustomOrder;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Generated from the Category reference module (tests/Feature/Catalog/Category). */
class CustomOrderTest extends TestCase
{
    use RefreshDatabase;

    private function admin(CustomOrderPermission ...$permissions): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: CustomOrderPermission::cases()));
    }

    /** @param  array<string, mixed>  $attributes */
    private function record(array $attributes = []): CustomOrder
    {
        static $n = 0;
        $n++;

        return CustomOrder::query()->create(array_merge(['reference_number' => "CUST-{$n}", 'customer_name' => "Client {$n}", 'customer_email' => "c{$n}@example.test", 'customer_phone' => '09171234567', 'event_date' => '2026-12-20', 'servings_count' => 30, 'tiers_count' => 2, 'theme_description' => 'Pastel unicorn', 'status' => 'inquiry'], $attributes));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/custom-orders')->assertRedirect('/login');
        $this->post('/admin/custom-orders', [])->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/custom-orders')->assertForbidden();
    }

    public function test_view_only_user_cannot_change_anything(): void
    {
        $record = $this->record();
        $this->actingAs($this->admin(CustomOrderPermission::CustomOrdersView));

        $this->get('/admin/custom-orders')->assertOk();
        $this->get('/admin/custom-orders/create')->assertForbidden();
        $this->get("/admin/custom-orders/{$record->id}/edit")->assertForbidden();
        $this->put("/admin/custom-orders/{$record->id}", [])->assertForbidden();
        $this->delete("/admin/custom-orders/{$record->id}")->assertForbidden();
    }

    public function test_index_renders_records_and_deferred_summary(): void
    {
        $this->record();
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/custom-orders')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('custom-order/index')
                ->has('records.data', 2)
                ->where('table.name', 'custom_orders')
                ->missing('summary')
                ->loadDeferredProps(fn (Assert $reload) => $reload->has('summary', 4)));
    }

    public function test_store_persists_and_flashes_a_toast(): void
    {

        $this->actingAs($this->admin())
            ->post('/admin/custom-orders', ['customer_name' => 'Rina', 'customer_email' => 'rina@example.test', 'customer_phone' => '09171234567', 'event_date' => '2026-12-24', 'servings_count' => '40', 'tiers_count' => '2', 'theme_description' => 'Christmas forest', 'status' => 'inquiry'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/custom-orders')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('custom_orders', ['customer_name' => 'Rina', 'servings_count' => 40]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/custom-orders', [])
            ->assertSessionHasErrors(['customer_name', 'customer_email', 'customer_phone', 'event_date', 'theme_description']);
    }

    public function test_unique_reference_number_ignores_soft_deleted_rows(): void
    {

        $existing = $this->record();
        $payload = array_merge(['customer_name' => 'Rina', 'customer_email' => 'rina@example.test', 'customer_phone' => '09171234567', 'event_date' => '2026-12-24', 'servings_count' => '40', 'tiers_count' => '2', 'theme_description' => 'Christmas forest', 'status' => 'inquiry'], ['reference_number' => $existing->reference_number]);

        $this->actingAs($this->admin())->post('/admin/custom-orders', $payload)->assertSessionHasErrors(['reference_number']);

        $existing->delete();
        $this->post('/admin/custom-orders', $payload)->assertSessionHasNoErrors();
    }

    public function test_edit_renders_and_update_persists(): void
    {
        $record = $this->record();

        $this->actingAs($this->admin())
            ->get("/admin/custom-orders/{$record->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('custom-order/edit')->where('record.id', $record->id));

        $this->put("/admin/custom-orders/{$record->id}", ['reference_number' => 'CUST-EDIT', 'customer_name' => 'Rina', 'customer_email' => 'rina@example.test', 'customer_phone' => '09171234567', 'event_date' => '2026-12-24', 'servings_count' => '50', 'tiers_count' => '3', 'theme_description' => 'Forest', 'quoted_price' => '4500', 'status' => 'quoted'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/custom-orders')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('custom_orders', array_merge(['id' => $record->id], ['status' => 'quoted', 'quoted_price' => 4500]));
    }

    public function test_delete_soft_deletes(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->delete("/admin/custom-orders/{$record->id}")->assertRedirect()->assertInertiaFlash('toast.type', 'success');

        $this->assertSoftDeleted($record);
    }

    public function test_complete(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->from('/admin/custom-orders')->patch("/admin/custom-orders/{$record->id}/complete", [])->assertSessionHasNoErrors()->assertRedirect('/admin/custom-orders');

        $this->assertSame('completed', $record->refresh()->status);
    }

    public function test_index_search_finds_by_keyword(): void
    {
        $this->record(['theme_description' => 'Dinosaur jungle']);
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/custom-orders?search[keyword]=dinosaur')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1));
    }
}
