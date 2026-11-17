<?php

namespace Tests\Feature\Inventory\PackagingMaterial;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Inventory\Enums\PackagingMaterialPermission;
use App\Domains\Inventory\Models\PackagingMaterial;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Generated from the Category reference module (tests/Feature/Catalog/Category). */
class PackagingMaterialTest extends TestCase
{
    use RefreshDatabase;

    private function admin(PackagingMaterialPermission ...$permissions): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: PackagingMaterialPermission::cases()));
    }

    /** @param  array<string, mixed>  $attributes */
    private function record(array $attributes = []): PackagingMaterial
    {
        static $n = 0;
        $n++;

        return PackagingMaterial::query()->create(array_merge(['name' => "Box {$n}", 'sku' => "PKG-{$n}", 'type' => 'box', 'unit' => 'pcs', 'cost_per_unit' => 12, 'stock_qty' => 50, 'min_stock_qty' => 10, 'is_active' => true], $attributes));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/packaging-materials')->assertRedirect('/login');
        $this->post('/admin/packaging-materials', [])->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/packaging-materials')->assertForbidden();
    }

    public function test_view_only_user_cannot_change_anything(): void
    {
        $record = $this->record();
        $this->actingAs($this->admin(PackagingMaterialPermission::PackagingMaterialsView));

        $this->get('/admin/packaging-materials')->assertOk();
        $this->get('/admin/packaging-materials/create')->assertForbidden();
        $this->get("/admin/packaging-materials/{$record->id}/edit")->assertForbidden();
        $this->put("/admin/packaging-materials/{$record->id}", [])->assertForbidden();
        $this->delete("/admin/packaging-materials/{$record->id}")->assertForbidden();
    }

    public function test_index_renders_records_and_deferred_summary(): void
    {
        $this->record();
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/packaging-materials')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('packaging-material/index')
                ->has('records.data', 2)
                ->where('table.name', 'packaging_materials')
                ->missing('summary')
                ->loadDeferredProps(fn (Assert $reload) => $reload->has('summary', 3)));
    }

    public function test_store_persists_and_flashes_a_toast(): void
    {

        $this->actingAs($this->admin())
            ->post('/admin/packaging-materials', ['name' => 'Loaf box', 'sku' => 'pkg-loaf', 'type' => 'box', 'unit' => 'pcs', 'cost_per_unit' => '15.5', 'stock_qty' => '100', 'min_stock_qty' => '20', 'is_active' => '1'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/packaging-materials')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('packaging_materials', ['sku' => 'PKG-LOAF', 'name' => 'Loaf box']);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/packaging-materials', [])
            ->assertSessionHasErrors(['name', 'sku']);
    }

    public function test_unique_sku_ignores_soft_deleted_rows(): void
    {

        $existing = $this->record();
        $payload = array_merge(['name' => 'Loaf box', 'sku' => 'pkg-loaf', 'type' => 'box', 'unit' => 'pcs', 'cost_per_unit' => '15.5', 'stock_qty' => '100', 'min_stock_qty' => '20', 'is_active' => '1'], ['sku' => $existing->sku]);

        $this->actingAs($this->admin())->post('/admin/packaging-materials', $payload)->assertSessionHasErrors(['sku']);

        $existing->delete();
        $this->post('/admin/packaging-materials', $payload)->assertSessionHasNoErrors();
    }

    public function test_edit_renders_and_update_persists(): void
    {
        $record = $this->record();

        $this->actingAs($this->admin())
            ->get("/admin/packaging-materials/{$record->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('packaging-material/edit')->where('record.id', $record->id));

        $this->put("/admin/packaging-materials/{$record->id}", ['name' => 'Ribbon', 'sku' => 'PKG-R', 'type' => 'ribbon', 'unit' => 'm', 'cost_per_unit' => '2', 'stock_qty' => '5', 'min_stock_qty' => '1', 'is_active' => '0'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/packaging-materials')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('packaging_materials', array_merge(['id' => $record->id], ['name' => 'Ribbon', 'type' => 'ribbon', 'is_active' => false]));
    }

    public function test_delete_soft_deletes(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->delete("/admin/packaging-materials/{$record->id}")->assertRedirect()->assertInertiaFlash('toast.type', 'success');

        $this->assertSoftDeleted($record);
    }

    public function test_restock(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->from('/admin/packaging-materials')->patch("/admin/packaging-materials/{$record->id}/restock", ['add_qty' => 20])->assertSessionHasNoErrors()->assertRedirect('/admin/packaging-materials');

        $this->assertEquals(70, (float) $record->refresh()->stock_qty);
    }

    public function test_index_search_finds_by_keyword(): void
    {
        $this->record(['name' => 'Kraft bag']);
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/packaging-materials?search[keyword]=kraft')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1));
    }
}
