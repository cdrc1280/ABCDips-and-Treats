<?php

namespace Tests\Feature\Inventory\Ingredient;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Inventory\Enums\IngredientPermission;
use App\Domains\Inventory\Models\Ingredient;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Generated from the Category reference module (tests/Feature/Catalog/Category). */
class IngredientTest extends TestCase
{
    use RefreshDatabase;

    private function admin(IngredientPermission ...$permissions): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: IngredientPermission::cases()));
    }

    /** @param  array<string, mixed>  $attributes */
    private function record(array $attributes = []): Ingredient
    {
        static $n = 0;
        $n++;

        return Ingredient::query()->create(array_merge(['sku' => "ING-{$n}", 'name' => "Ingredient {$n}", 'unit' => 'grams', 'item_unit' => 1000, 'item_price' => 100, 'stock_qty' => 500, 'min_stock_qty' => 100, 'reorder_qty' => 1000], $attributes));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/ingredients')->assertRedirect('/login');
        $this->post('/admin/ingredients', [])->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/ingredients')->assertForbidden();
    }

    public function test_view_only_user_cannot_change_anything(): void
    {
        $record = $this->record();
        $this->actingAs($this->admin(IngredientPermission::IngredientsView));

        $this->get('/admin/ingredients')->assertOk();
        $this->get('/admin/ingredients/create')->assertForbidden();
        $this->get("/admin/ingredients/{$record->id}/edit")->assertForbidden();
        $this->put("/admin/ingredients/{$record->id}", [])->assertForbidden();
        $this->delete("/admin/ingredients/{$record->id}")->assertForbidden();
    }

    public function test_index_renders_records_and_deferred_summary(): void
    {
        $this->record();
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/ingredients')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('ingredient/index')
                ->has('records.data', 2)
                ->where('table.name', 'ingredients')
                ->missing('summary')
                ->loadDeferredProps(fn (Assert $reload) => $reload->has('summary', 3)));
    }

    public function test_store_persists_and_flashes_a_toast(): void
    {

        $this->actingAs($this->admin())
            ->post('/admin/ingredients', ['sku' => 'ing-flour', 'name' => 'Bread flour', 'unit' => 'grams', 'item_unit' => '25000', 'item_price' => '1250', 'stock_qty' => '5000', 'min_stock_qty' => '2000', 'reorder_qty' => '25000'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/ingredients')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('ingredients', ['sku' => 'ING-FLOUR', 'name' => 'Bread flour', 'cost_per_unit' => 0.05]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/ingredients', [])
            ->assertSessionHasErrors(['name', 'sku']);
    }

    public function test_unique_sku_ignores_soft_deleted_rows(): void
    {

        $existing = $this->record();
        $payload = array_merge(['sku' => 'ing-flour', 'name' => 'Bread flour', 'unit' => 'grams', 'item_unit' => '25000', 'item_price' => '1250', 'stock_qty' => '5000', 'min_stock_qty' => '2000', 'reorder_qty' => '25000'], ['sku' => $existing->sku]);

        $this->actingAs($this->admin())->post('/admin/ingredients', $payload)->assertSessionHasErrors(['sku']);

        $existing->delete();
        $this->post('/admin/ingredients', $payload)->assertSessionHasNoErrors();
    }

    public function test_edit_renders_and_update_persists(): void
    {
        $record = $this->record();

        $this->actingAs($this->admin())
            ->get("/admin/ingredients/{$record->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('ingredient/edit')->where('record.id', $record->id));

        $this->put("/admin/ingredients/{$record->id}", ['sku' => 'ING-X', 'name' => 'Cake flour', 'unit' => 'kg', 'item_unit' => '25', 'item_price' => '1000', 'stock_qty' => '10', 'min_stock_qty' => '5', 'reorder_qty' => '25'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/ingredients')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('ingredients', array_merge(['id' => $record->id], ['name' => 'Cake flour', 'unit' => 'kg']));
    }

    public function test_delete_soft_deletes(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->delete("/admin/ingredients/{$record->id}")->assertRedirect()->assertInertiaFlash('toast.type', 'success');

        $this->assertSoftDeleted($record);
    }

    public function test_restock(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->from('/admin/ingredients')->patch("/admin/ingredients/{$record->id}/restock", ['add_qty' => 250])->assertSessionHasNoErrors()->assertRedirect('/admin/ingredients');

        $this->assertEquals(750, (float) $record->refresh()->stock_qty);
        $this->assertDatabaseHas('stock_movements', ['ingredient_id' => $record->id, 'type' => 'purchase']);
    }

    public function test_index_search_finds_by_keyword(): void
    {
        $this->record(['name' => 'Muscovado sugar']);
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/ingredients?search[keyword]=muscovado')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1));
    }
}
