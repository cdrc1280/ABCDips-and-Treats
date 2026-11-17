<?php

namespace Tests\Feature\Costing\ProductCosting;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Costing\Enums\ProductCostingPermission;
use App\Domains\Costing\Models\ProductCosting;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Generated from the Category reference module (tests/Feature/Catalog/Category). */
class ProductCostingTest extends TestCase
{
    use RefreshDatabase;

    private function admin(ProductCostingPermission ...$permissions): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: ProductCostingPermission::cases()));
    }

    /** @param  array<string, mixed>  $attributes */
    private function record(array $attributes = []): ProductCosting
    {
        static $n = 0;
        $n++;

        return ProductCosting::query()->create(array_merge(['name' => "Sheet {$n}", 'yield_qty' => 12, 'yield_unit' => 'pcs'], $attributes));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/product-costings')->assertRedirect('/login');
        $this->post('/admin/product-costings', [])->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/product-costings')->assertForbidden();
    }

    public function test_view_only_user_cannot_change_anything(): void
    {
        $record = $this->record();
        $this->actingAs($this->admin(ProductCostingPermission::ProductCostingsView));

        $this->get('/admin/product-costings')->assertOk();
        $this->get('/admin/product-costings/create')->assertForbidden();
        $this->get("/admin/product-costings/{$record->id}/edit")->assertForbidden();
        $this->put("/admin/product-costings/{$record->id}", [])->assertForbidden();
        $this->delete("/admin/product-costings/{$record->id}")->assertForbidden();
    }

    public function test_index_renders_records_without_stat_cards(): void
    {
        $this->record();
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/product-costings')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('product-costing/index')
                ->has('records.data', 2)
                ->where('table.name', 'product_costings')
                ->missing('summary'));
    }

    public function test_store_persists_and_flashes_a_toast(): void
    {

        $this->actingAs($this->admin())
            ->post('/admin/product-costings', ['name' => 'Leche flan tubs', 'yield_qty' => '20', 'yield_unit' => 'tub (70g)', 'overhead_pct' => '40', 'markup_pct' => '66', 'labor_pct' => '60', 'items' => [['group' => 'ingredient', 'name' => 'Eggs', 'unit' => 'pcs', 'package_amount' => '30', 'package_price' => '270', 'qty_used' => '12']]])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/product-costings')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('product_costings', ['name' => 'Leche flan tubs', 'yield_unit' => 'tub (70g)']);
    }

    public function test_edit_renders_and_update_persists(): void
    {
        $record = $this->record();

        $this->actingAs($this->admin())
            ->get("/admin/product-costings/{$record->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('product-costing/edit')->where('record.id', $record->id));

        $this->put("/admin/product-costings/{$record->id}", ['name' => 'Leche flan', 'yield_qty' => '24', 'yield_unit' => 'tub', 'overhead_pct' => '30', 'markup_pct' => '60', 'labor_pct' => '50', 'items' => []])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/product-costings')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('product_costings', array_merge(['id' => $record->id], ['name' => 'Leche flan', 'overhead_pct' => 30]));
    }

    public function test_delete_soft_deletes(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->delete("/admin/product-costings/{$record->id}")->assertRedirect()->assertInertiaFlash('toast.type', 'success');

        $this->assertSoftDeleted($record);
    }

    public function test_index_search_finds_by_keyword(): void
    {
        $this->record(['name' => 'Cassava cake']);
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/product-costings?search[keyword]=cassava')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1));
    }
}
