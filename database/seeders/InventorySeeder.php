<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Raw Ingredients
        $ingredients = [
            ['sku' => 'ING-FLR-01', 'name' => 'Unbleached All-Purpose Flour', 'unit' => 'kg', 'cost_per_unit' => 65.00, 'stock_qty' => 150.000, 'min_stock_qty' => 20.000, 'supplier_name' => 'San Miguel Mills'],
            ['sku' => 'ING-BTR-02', 'name' => 'Pure Unsalted Creamery Butter', 'unit' => 'kg', 'cost_per_unit' => 380.00, 'stock_qty' => 45.000, 'min_stock_qty' => 10.000, 'supplier_name' => 'Anchor Dairy'],
            ['sku' => 'ING-SGR-03', 'name' => 'Golden Brown Sugar', 'unit' => 'kg', 'cost_per_unit' => 70.00, 'stock_qty' => 80.000, 'min_stock_qty' => 15.000, 'supplier_name' => 'Victoria Milling'],
            ['sku' => 'ING-EGG-04', 'name' => 'Fresh Farm Eggs (Tray of 30)', 'unit' => 'pcs', 'cost_per_unit' => 8.00, 'stock_qty' => 300.000, 'min_stock_qty' => 60.000, 'supplier_name' => 'Bounty Fresh'],
            ['sku' => 'ING-BAN-05', 'name' => 'Ripe Cavendish Bananas', 'unit' => 'kg', 'cost_per_unit' => 50.00, 'stock_qty' => 60.000, 'min_stock_qty' => 10.000, 'supplier_name' => 'Local Fruit Market'],
            ['sku' => 'ING-[#5C3A22]-06', 'name' => 'Belgian Dark Chocolate 70%', 'unit' => 'kg', 'cost_per_unit' => 620.00, 'stock_qty' => 30.000, 'min_stock_qty' => 5.000, 'supplier_name' => 'Callebaut Philippines'],
            ['sku' => 'ING-UBE-07', 'name' => 'Homemade Ube Halaya Jam', 'unit' => 'kg', 'cost_per_unit' => 350.00, 'stock_qty' => 25.000, 'min_stock_qty' => 5.000, 'supplier_name' => 'Good Shepherd Baguio'],
            ['sku' => 'ING-CRM-08', 'name' => 'Philadelphia Cream Cheese', 'unit' => 'kg', 'cost_per_unit' => 550.00, 'stock_qty' => 35.000, 'min_stock_qty' => 8.000, 'supplier_name' => 'Mondelez Philippines'],
            ['sku' => 'ING-VNL-09', 'name' => 'Madagascar Vanilla Bean Paste', 'unit' => 'ml', 'cost_per_unit' => 4.50, 'stock_qty' => 1000.000, 'min_stock_qty' => 200.000, 'supplier_name' => 'Nielsen-Massey'],
        ];

        $ingMap = [];
        foreach ($ingredients as $iData) {
            $ingMap[$iData['sku']] = Ingredient::create($iData);
        }

        // 2. Sample Recipe for Classic Banana Bread Loaf
        $bbProduct = Product::where('slug', 'classic-banana-bread-loaf')->first();
        if ($bbProduct) {
            $recipe = Recipe::create([
                'product_id'          => $bbProduct->id,
                'name'                => 'Classic Banana Bread Loaf Recipe',
                'yield_qty'           => 1,
                'prep_time_minutes'   => 20,
                'baking_time_minutes' => 45,
                'instructions'        => 'Mash ripe bananas. Cream butter and brown sugar. Add eggs one at a time. Fold in flour and vanilla. Bake at 175°C for 45 mins.',
            ]);

            // Recipe BOM ingredients
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'ingredient_id' => $ingMap['ING-BAN-05']->id, 'qty_required' => 400, 'unit' => 'g']);
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'ingredient_id' => $ingMap['ING-FLR-01']->id, 'qty_required' => 250, 'unit' => 'g']);
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'ingredient_id' => $ingMap['ING-BTR-02']->id, 'qty_required' => 120, 'unit' => 'g']);
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'ingredient_id' => $ingMap['ING-SGR-03']->id, 'qty_required' => 150, 'unit' => 'g']);
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'ingredient_id' => $ingMap['ING-EGG-04']->id, 'qty_required' => 2, 'unit' => 'pcs']);
        }
    }
}
