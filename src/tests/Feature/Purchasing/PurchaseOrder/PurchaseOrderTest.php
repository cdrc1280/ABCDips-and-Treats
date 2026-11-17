<?php

namespace Tests\Feature\Purchasing\PurchaseOrder;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Inventory\Models\Ingredient;
use App\Domains\Inventory\Models\Supplier;
use App\Domains\Purchasing\Enums\PurchaseOrderPermission;
use App\Domains\Purchasing\Models\PurchaseOrder;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Generated from the Category reference module (tests/Feature/Catalog/Category). */
class PurchaseOrderTest extends TestCase
{
    use RefreshDatabase;

    private function admin(PurchaseOrderPermission ...$permissions): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: PurchaseOrderPermission::cases()));
    }

    /** @param  array<string, mixed>  $attributes */
    private function record(array $attributes = []): PurchaseOrder
    {
        static $n = 0;
        $n++;
        $supplier = Supplier::query()->firstOrCreate(['email' => 'mill@example.test'], ['name' => 'Mill', 'contact_person' => 'A', 'phone' => '0917', 'address' => 'Cavite']);

        $model = PurchaseOrder::query()->create(array_merge(['po_number' => "PO-{$n}", 'supplier_id' => $supplier->id, 'status' => 'draft', 'tax' => 0], $attributes));

        return $model;
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/purchase-orders')->assertRedirect('/login');
        $this->post('/admin/purchase-orders', [])->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/purchase-orders')->assertForbidden();
    }

    public function test_view_only_user_cannot_change_anything(): void
    {
        $record = $this->record();
        $this->actingAs($this->admin(PurchaseOrderPermission::PurchaseOrdersView));

        $this->get('/admin/purchase-orders')->assertOk();
        $this->get('/admin/purchase-orders/create')->assertForbidden();
        $this->get("/admin/purchase-orders/{$record->id}/edit")->assertForbidden();
        $this->put("/admin/purchase-orders/{$record->id}", [])->assertForbidden();
        $this->delete("/admin/purchase-orders/{$record->id}")->assertForbidden();
    }

    public function test_index_renders_records_and_deferred_summary(): void
    {
        $this->record();
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/purchase-orders')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('purchase-order/index')
                ->has('records.data', 2)
                ->where('table.name', 'purchase_orders')
                ->missing('summary')
                ->loadDeferredProps(fn (Assert $reload) => $reload->has('summary', 3)));
    }

    public function test_store_persists_and_flashes_a_toast(): void
    {
        $supplier = Supplier::query()->firstOrCreate(['email' => 'mill@example.test'], ['name' => 'Mill', 'contact_person' => 'A', 'phone' => '0917', 'address' => 'Cavite']);
        $flour = Ingredient::query()->create(['sku' => 'FL', 'name' => 'Flour', 'unit' => 'kg']);

        $this->actingAs($this->admin())
            ->post('/admin/purchase-orders', ['supplier_id' => $supplier->id, 'status' => 'draft', 'tax' => '0', 'items' => [['ingredient_id' => (string) $flour->id, 'qty_ordered' => '25', 'unit_cost' => '48']]])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/purchase-orders')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('purchase_orders', ['supplier_id' => $supplier->id, 'total' => 1200]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/purchase-orders', [])
            ->assertSessionHasErrors(['supplier_id', 'items']);
    }

    public function test_unique_po_number_ignores_soft_deleted_rows(): void
    {
        $supplier = Supplier::query()->firstOrCreate(['email' => 'mill@example.test'], ['name' => 'Mill', 'contact_person' => 'A', 'phone' => '0917', 'address' => 'Cavite']);
        $flour = Ingredient::query()->create(['sku' => 'FL', 'name' => 'Flour', 'unit' => 'kg']);

        $existing = $this->record();
        $payload = array_merge(['supplier_id' => $supplier->id, 'status' => 'draft', 'tax' => '0', 'items' => [['ingredient_id' => (string) $flour->id, 'qty_ordered' => '25', 'unit_cost' => '48']]], ['po_number' => $existing->po_number]);

        $this->actingAs($this->admin())->post('/admin/purchase-orders', $payload)->assertSessionHasErrors(['po_number']);

        $existing->delete();
        $this->post('/admin/purchase-orders', $payload)->assertSessionHasNoErrors();
    }

    public function test_edit_renders_and_update_persists(): void
    {
        $record = $this->record();
        $supplier = Supplier::query()->firstOrCreate(['email' => 'mill@example.test'], ['name' => 'Mill', 'contact_person' => 'A', 'phone' => '0917', 'address' => 'Cavite']);
        $flour = Ingredient::query()->create(['sku' => 'FL', 'name' => 'Flour', 'unit' => 'kg']);

        $this->actingAs($this->admin())
            ->get("/admin/purchase-orders/{$record->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('purchase-order/edit')->where('record.id', $record->id));

        $this->put("/admin/purchase-orders/{$record->id}", ['po_number' => 'PO-EDIT', 'supplier_id' => $supplier->id, 'status' => 'sent', 'tax' => '0', 'items' => [['ingredient_id' => (string) $flour->id, 'qty_ordered' => '10', 'unit_cost' => '50']]])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/purchase-orders')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('purchase_orders', array_merge(['id' => $record->id], ['status' => 'sent', 'total' => 500]));
    }

    public function test_delete_soft_deletes(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->delete("/admin/purchase-orders/{$record->id}")->assertRedirect()->assertInertiaFlash('toast.type', 'success');

        $this->assertSoftDeleted($record);
    }

    public function test_index_search_finds_by_keyword(): void
    {
        $this->record(['notes' => 'Rush butter delivery']);
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/purchase-orders?search[keyword]=rush')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1));
    }
}
