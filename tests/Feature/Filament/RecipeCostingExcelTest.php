<?php

namespace Tests\Feature\Filament;

use App\Models\Ingredient;
use App\Models\PackagingMaterial;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\RecipePackaging;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeCostingExcelTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Product $product;
    protected Recipe $recipe;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@abcdips.test',
        ]);

        $category = ProductCategory::create([
            'name' => 'Pastries',
            'slug' => 'pastries',
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'sku'         => 'SKU-EXCEL-001',
            'name'        => 'Choco Moist Tub',
            'slug'        => 'choco-moist-tub',
            'price'       => 85.00,
            'is_active'   => true,
        ]);

        // Create Flour ingredient: 1000g @ ₱65 -> ₱0.065/g
        $flour = Ingredient::create([
            'sku'           => 'ING-FLOUR',
            'name'          => 'All purpose flour',
            'unit'          => 'g',
            'cost_per_unit' => 0.065, // ₱65 / 1000g
            'stock_qty'     => 10000,
        ]);

        // Create Cocoa ingredient: 500g @ ₱395 -> ₱0.79/g
        $cocoa = Ingredient::create([
            'sku'           => 'ING-COCOA',
            'name'          => 'Cocoa',
            'unit'          => 'g',
            'cost_per_unit' => 0.79, // ₱395 / 500g
            'stock_qty'     => 5000,
        ]);

        // Create Packaging: Box 1 pc @ ₱8
        $boxPkg = PackagingMaterial::create([
            'name'          => 'Box',
            'sku'           => 'PKG-BOX',
            'type'          => 'box',
            'unit'          => 'piece',
            'cost_per_unit' => 8.00,
            'stock_qty'     => 100,
        ]);

        // Create Recipe with Excel parameters: 18 tubs yield, 40% overhead, 66% markup, 60% labor
        $this->recipe = Recipe::create([
            'product_id'   => $this->product->id,
            'name'         => 'Choco Moist Batch Recipe',
            'yield_qty'    => 18,
            'overhead_pct' => 40.00,
            'markup_pct'   => 66.00,
            'labor_pct'    => 60.00,
        ]);

        // Recipe Ingredients: 250g Flour + 90g Cocoa
        // Flour: 250 * 0.065 = 16.25
        // Cocoa: 90 * 0.79 = 71.10
        // Subtotal Ingredient Cost = 87.35
        RecipeIngredient::create([
            'recipe_id'     => $this->recipe->id,
            'ingredient_id' => $flour->id,
            'qty_required'  => 250,
            'unit'          => 'g',
        ]);

        RecipeIngredient::create([
            'recipe_id'     => $this->recipe->id,
            'ingredient_id' => $cocoa->id,
            'qty_required'  => 90,
            'unit'          => 'g',
        ]);

        // Recipe Packaging: 18 Boxes @ ₱8 each = ₱144.00
        RecipePackaging::create([
            'recipe_id'             => $this->recipe->id,
            'packaging_material_id' => $boxPkg->id,
            'name'                  => 'Box',
            'unit'                  => 'piece',
            'package_qty'           => 1,
            'package_cost'          => 8.00,
            'qty_used'              => 18,
        ]);
    }

    public function test_recipe_model_computes_exact_excel_formula_costs(): void
    {
        $this->recipe->refresh();

        // Total Ingredient Cost: 16.25 + 71.10 = 87.35
        $this->assertEquals(87.35, $this->recipe->total_ingredient_cost);

        // Total Packaging Cost: 18 * 8 = 144.00
        $this->assertEquals(144.00, $this->recipe->total_packaging_cost);

        // Total Raw Materials Cost: 87.35 + 144.00 = 231.35
        $this->assertEquals(231.35, $this->recipe->total_raw_materials_cost);

        // Overhead Amount (40% of 231.35) = 92.54
        $this->assertEquals(92.54, round($this->recipe->overhead_amount, 2));

        // Total Cost: 231.35 + 92.54 = 323.89
        $this->assertEquals(323.89, round($this->recipe->total_cost, 2));

        // Mark Up (66% of 323.89) = 213.7674 -> 213.77
        $this->assertEquals(213.77, round($this->recipe->markup_amount, 2));

        // Labor Charge (60% of Raw Materials 231.35) = 138.81
        $this->assertEquals(138.81, round($this->recipe->labor_amount, 2));

        // Total Batch Selling Price: 323.89 + 213.7674 + 138.81 = 676.4674 -> 676.47
        $this->assertEquals(676.47, round($this->recipe->batch_selling_price, 2));

        // Price Per Piece (18 Tubs): 676.4674 / 18 = 37.5815 -> 37.58 per tub
        $this->assertEquals(37.58, round($this->recipe->unit_selling_price, 2));
    }

    public function test_admin_can_access_filament_recipe_resource(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/recipes');
        $response->assertStatus(200);
    }
}
