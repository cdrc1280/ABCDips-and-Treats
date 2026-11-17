<?php

namespace Tests\Feature\Costing\Recipe;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductCategory;
use App\Domains\Costing\Enums\RecipePermission;
use App\Domains\Costing\Models\Recipe;
use App\Domains\Inventory\Models\Ingredient;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Generated from the Category reference module (tests/Feature/Catalog/Category). */
class RecipeTest extends TestCase
{
    use RefreshDatabase;

    private function admin(RecipePermission ...$permissions): User
    {
        return $this->userWith(AccessPermission::DashboardView, ...($permissions ?: RecipePermission::cases()));
    }

    /** @param  array<string, mixed>  $attributes */
    private function record(array $attributes = []): Recipe
    {
        static $n = 0;
        $n++;
        $category = ProductCategory::query()->firstOrCreate(['slug' => 'breads'], ['name' => 'Breads']);
        $product = Product::query()->create(['category_id' => $category->id, 'sku' => "P-{$n}", 'name' => "Loaf {$n}", 'slug' => "loaf-{$n}", 'price' => 100]);

        return Recipe::query()->create(array_merge(['product_id' => $product->id, 'name' => "Recipe {$n}", 'yield_qty' => 10], $attributes));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/recipes')->assertRedirect('/login');
        $this->post('/admin/recipes', [])->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/recipes')->assertForbidden();
    }

    public function test_view_only_user_cannot_change_anything(): void
    {
        $record = $this->record();
        $this->actingAs($this->admin(RecipePermission::RecipesView));

        $this->get('/admin/recipes')->assertOk();
        $this->get('/admin/recipes/create')->assertForbidden();
        $this->get("/admin/recipes/{$record->id}/edit")->assertForbidden();
        $this->put("/admin/recipes/{$record->id}", [])->assertForbidden();
        $this->delete("/admin/recipes/{$record->id}")->assertForbidden();
    }

    public function test_index_renders_records_without_stat_cards(): void
    {
        $this->record();
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/recipes')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('recipe/index')
                ->has('records.data', 2)
                ->where('table.name', 'recipes')
                ->missing('summary'));
    }

    public function test_store_persists_and_flashes_a_toast(): void
    {
        $category = ProductCategory::query()->firstOrCreate(['slug' => 'breads'], ['name' => 'Breads']);
        $product = Product::query()->create(['category_id' => $category->id, 'sku' => 'P-R', 'name' => 'Banana loaf', 'slug' => 'banana-loaf', 'price' => 300]);
        $flour = Ingredient::query()->create(['sku' => 'FL', 'name' => 'Flour', 'unit' => 'grams', 'item_unit' => 1000, 'item_price' => 80]);

        $this->actingAs($this->admin())
            ->post('/admin/recipes', ['name' => 'Banana loaf (batch)', 'product_id' => $product->id, 'yield_qty' => '8', 'prep_time_minutes' => '20', 'baking_time_minutes' => '55', 'overhead_pct' => '40', 'markup_pct' => '66', 'labor_pct' => '60', 'ingredients' => [['ingredient_id' => (string) $flour->id, 'qty_required' => '1', 'unit' => 'kg']], 'packaging' => [['packaging_material_id' => '', 'name' => 'Loaf box', 'unit' => 'pcs', 'package_qty' => '50', 'package_cost' => '500', 'qty_used' => '8']]])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/recipes')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('recipes', ['name' => 'Banana loaf (batch)', 'yield_qty' => 8]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/recipes', [])
            ->assertSessionHasErrors(['name', 'product_id']);
    }

    public function test_edit_renders_and_update_persists(): void
    {
        $record = $this->record();
        $category = ProductCategory::query()->firstOrCreate(['slug' => 'breads'], ['name' => 'Breads']);
        $product = Product::query()->create(['category_id' => $category->id, 'sku' => 'P-R', 'name' => 'Banana loaf', 'slug' => 'banana-loaf', 'price' => 300]);
        $flour = Ingredient::query()->create(['sku' => 'FL', 'name' => 'Flour', 'unit' => 'grams', 'item_unit' => 1000, 'item_price' => 80]);

        $this->actingAs($this->admin())
            ->get("/admin/recipes/{$record->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('recipe/edit')->where('record.id', $record->id));

        $this->put("/admin/recipes/{$record->id}", ['name' => 'Banana loaf v2', 'product_id' => $product->id, 'yield_qty' => '10', 'prep_time_minutes' => '20', 'baking_time_minutes' => '55', 'overhead_pct' => '40', 'markup_pct' => '50', 'labor_pct' => '60', 'ingredients' => [['ingredient_id' => (string) $flour->id, 'qty_required' => '500', 'unit' => 'grams']]])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/recipes')
            ->assertInertiaFlash('toast.type', 'success');

        $this->assertDatabaseHas('recipes', array_merge(['id' => $record->id], ['name' => 'Banana loaf v2', 'markup_pct' => 50]));
    }

    public function test_delete_soft_deletes(): void
    {
        $record = $this->record([]);

        $this->actingAs($this->admin())->delete("/admin/recipes/{$record->id}")->assertRedirect()->assertInertiaFlash('toast.type', 'success');

        $this->assertSoftDeleted($record);
    }

    public function test_index_search_finds_by_keyword(): void
    {
        $this->record(['name' => 'Ube halaya swirl']);
        $this->record();

        $this->actingAs($this->admin())
            ->get('/admin/recipes?search[keyword]=halaya')
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 1));
    }
}
