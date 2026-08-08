<?php

namespace Tests\Feature\Filament;

use App\Models\Ingredient;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\User;
use App\Services\CostingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCostingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Product $product;
    protected Recipe $recipe;

    protected function setUp(): void
    {
        parent::setUp();

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $this->admin = User::factory()->create([
            'email' => 'admin@abcdips.test',
        ]);
        $this->admin->assignRole('admin');

        $category = ProductCategory::create([
            'name' => 'Pastries',
            'slug' => 'pastries',
        ]);

        $this->product = Product::create([
            'category_id'       => $category->id,
            'sku'               => 'SKU-COST-001',
            'name'              => 'Costing Test Loaf',
            'slug'              => 'costing-test-loaf',
            'price'             => 250.00,
            'prep_time_minutes' => 30,
            'stock_qty'         => 10,
            'is_active'         => true,
        ]);

        // Create raw ingredient: Flour @ ₱50 / kg
        $flour = Ingredient::create([
            'sku'           => 'ING-FLOUR',
            'name'          => 'Bread Flour',
            'unit'          => 'kg',
            'cost_per_unit' => 50.00,
            'stock_qty'     => 100,
        ]);

        // Create raw ingredient: Butter @ ₱300 / kg
        $butter = Ingredient::create([
            'sku'           => 'ING-BUTTER',
            'name'          => 'Creamery Butter',
            'unit'          => 'kg',
            'cost_per_unit' => 300.00,
            'stock_qty'     => 50,
        ]);

        // Recipe: 500g Flour + 200g Butter yielding 1 Loaf
        $this->recipe = Recipe::create([
            'product_id'          => $this->product->id,
            'name'                => 'Costing Test Loaf Recipe',
            'yield_qty'           => 1,
            'prep_time_minutes'   => 30,
            'baking_time_minutes' => 30,
        ]);

        RecipeIngredient::create([
            'recipe_id'     => $this->recipe->id,
            'ingredient_id' => $flour->id,
            'qty_required'  => 500, // 500g = 0.5kg @ ₱50/kg = ₱25.00
            'unit'          => 'g',
        ]);

        RecipeIngredient::create([
            'recipe_id'     => $this->recipe->id,
            'ingredient_id' => $butter->id,
            'qty_required'  => 200, // 200g = 0.2kg @ ₱300/kg = ₱60.00
            'unit'          => 'g',
        ]);
    }

    public function test_costing_service_calculates_correct_cogs_and_margins(): void
    {
        $service = app(CostingService::class);
        $costing = $service->calculateForProduct($this->product, [
            'labor_rate_per_hour' => 60.00, // ₱60/hr -> 60 mins work = ₱60.00 labor
            'overhead_pct'        => 10.0,  // 10% overhead on materials
        ]);

        // Ingredient Cost: ₱25 (Flour) + ₱60 (Butter) = ₱85.00
        $this->assertEquals(85.00, $costing['ingredient_cost_unit']);

        // Labor Cost: 60 mins work @ ₱60/hr = ₱60.00
        $this->assertEquals(60.00, $costing['labor_cost_unit']);

        // Direct Material + Packaging Overhead (10% of ₱85) = ₱8.50
        $this->assertEquals(8.50, $costing['overhead_cost_unit']);

        // Total COGS: 85 + 60 + 8.50 = ₱153.50
        $this->assertEquals(153.50, $costing['total_cogs_unit']);

        // Gross Profit @ ₱250 selling price: 250 - 153.50 = ₱96.50
        $this->assertEquals(96.50, $costing['gross_profit_unit']);
        $this->assertEquals(38.6, $costing['gross_margin_pct']);
    }

    public function test_admin_can_access_costing_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/product-costings');
        $response->assertStatus(200);
    }
}
