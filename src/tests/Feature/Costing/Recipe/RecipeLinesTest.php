<?php

namespace Tests\Feature\Costing\Recipe;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductCategory;
use App\Domains\Costing\Enums\RecipePermission;
use App\Domains\Costing\Models\Recipe;
use App\Domains\Inventory\Models\Ingredient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Hand-written: recipe line items, computed costs and "use suggested price". */
class RecipeLinesTest extends TestCase
{
    use RefreshDatabase;

    public function test_lines_are_saved_replaced_and_costed(): void
    {
        $category = ProductCategory::query()->create(['name' => 'Breads', 'slug' => 'breads']);
        $product = Product::query()->create(['category_id' => $category->id, 'sku' => 'B1', 'name' => 'Loaf', 'slug' => 'loaf', 'price' => 100]);
        $flour = Ingredient::query()->create(['sku' => 'FL', 'name' => 'Flour', 'unit' => 'grams', 'item_unit' => 1000, 'item_price' => 80]);
        $sugar = Ingredient::query()->create(['sku' => 'SG', 'name' => 'Sugar', 'unit' => 'grams', 'item_unit' => 1000, 'item_price' => 100]);
        $this->actingAs($this->userWith(AccessPermission::DashboardView, ...RecipePermission::cases()));

        $base = ['name' => 'Loaf batch', 'product_id' => $product->id, 'yield_qty' => '10', 'prep_time_minutes' => '10', 'baking_time_minutes' => '40',
            'overhead_pct' => '0', 'markup_pct' => '0', 'labor_pct' => '0'];

        $this->post('/admin/recipes', $base + ['ingredients' => [
            ['ingredient_id' => (string) $flour->id, 'qty_required' => '1', 'unit' => 'kg'],
            ['ingredient_id' => (string) $sugar->id, 'qty_required' => '200', 'unit' => 'grams'],
        ]])->assertSessionHasNoErrors();

        $recipe = Recipe::query()->firstOrFail();
        $this->assertSame(2, $recipe->recipeIngredients()->count());

        // 1 kg flour @ ₱0.08/g = ₱80, 200 g sugar @ ₱0.10/g = ₱20 → ₱100 per batch, ₱10 per piece
        $this->get("/admin/recipes/{$recipe->id}/edit")->assertInertia(fn (Assert $page) => $page
            ->has('record.ingredients', 2)
            ->where('record.ingredients.0.line_cost', 80)
            ->where('record.total_cost', 100)
            ->where('record.unit_cost', 10));

        $this->put("/admin/recipes/{$recipe->id}", $base + ['ingredients' => [['ingredient_id' => (string) $sugar->id, 'qty_required' => '100', 'unit' => 'grams']]])
            ->assertSessionHasNoErrors();
        $this->assertSame([$sugar->id], $recipe->recipeIngredients()->pluck('ingredient_id')->all());

        $this->patch("/admin/recipes/{$recipe->id}/apply-price")->assertSessionHasNoErrors();
        $this->assertEquals(1.0, (float) $product->refresh()->price);
    }
}
