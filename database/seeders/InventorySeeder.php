<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\PackagingMaterial;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\RecipePackaging;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ingredients from PDF Reference
        $pdfIngredients = [
            ['sku' => 'ING-FLR-01', 'name' => 'All purpose flour', 'unit' => 'grams', 'item_unit' => 1000, 'item_price' => 65.00, 'stock_qty' => 10000.000, 'min_stock_qty' => 1000.000, 'supplier_name' => 'San Miguel Mills'],
            ['sku' => 'ING-CCA-02', 'name' => 'Cocoa', 'unit' => 'grams', 'item_unit' => 500, 'item_price' => 395.00, 'stock_qty' => 5000.000, 'min_stock_qty' => 500.000, 'supplier_name' => 'Dutche Cocoa'],
            ['sku' => 'ING-BKP-03', 'name' => 'Baking powder', 'unit' => 'grams', 'item_unit' => 100, 'item_price' => 22.00, 'stock_qty' => 1000.000, 'min_stock_qty' => 100.000, 'supplier_name' => 'Calumet'],
            ['sku' => 'ING-BKS-04', 'name' => 'Baking Soda', 'unit' => 'grams', 'item_unit' => 100, 'item_price' => 25.00, 'stock_qty' => 1000.000, 'min_stock_qty' => 100.000, 'supplier_name' => 'Arm & Hammer'],
            ['sku' => 'ING-WTR-05', 'name' => 'Water', 'unit' => 'ml', 'item_unit' => 1000, 'item_price' => 30.00, 'stock_qty' => 50000.000, 'min_stock_qty' => 5000.000, 'supplier_name' => 'Purified Water'],
            ['sku' => 'ING-EGG-06', 'name' => 'Eggs', 'unit' => 'piece', 'item_unit' => 1, 'item_price' => 9.00, 'stock_qty' => 300.000, 'min_stock_qty' => 30.000, 'supplier_name' => 'Bounty Fresh'],
            ['sku' => 'ING-VNG-07', 'name' => 'White vinegar', 'unit' => 'ml', 'item_unit' => 350, 'item_price' => 30.00, 'stock_qty' => 3500.000, 'min_stock_qty' => 350.000, 'supplier_name' => 'Datu Puti'],
            ['sku' => 'ING-OIL-08', 'name' => 'Canola oil', 'unit' => 'ml', 'item_unit' => 1000, 'item_price' => 170.00, 'stock_qty' => 10000.000, 'min_stock_qty' => 1000.000, 'supplier_name' => 'Golden Fiesta'],
            ['sku' => 'ING-SLT-09', 'name' => 'Salt', 'unit' => 'grams', 'item_unit' => 1000, 'item_price' => 40.00, 'stock_qty' => 5000.000, 'min_stock_qty' => 500.000, 'supplier_name' => 'Fidel Salt'],
            ['sku' => 'ING-EVP-10', 'name' => 'Evap', 'unit' => 'grams', 'item_unit' => 380, 'item_price' => 62.00, 'stock_qty' => 3800.000, 'min_stock_qty' => 380.000, 'supplier_name' => 'Alaska Evap'],
            ['sku' => 'ING-STR-11', 'name' => 'Cornstarch', 'unit' => 'grams', 'item_unit' => 1000, 'item_price' => 52.00, 'stock_qty' => 5000.000, 'min_stock_qty' => 500.000, 'supplier_name' => 'Liwayway'],
            ['sku' => 'ING-SGR-12', 'name' => 'Brown sugar', 'unit' => 'grams', 'item_unit' => 1000, 'item_price' => 90.00, 'stock_qty' => 10000.000, 'min_stock_qty' => 1000.000, 'supplier_name' => 'Victoria Milling'],
            ['sku' => 'ING-YLK-13', 'name' => 'Yolk egg', 'unit' => 'piece', 'item_unit' => 1, 'item_price' => 9.00, 'stock_qty' => 100.000, 'min_stock_qty' => 10.000, 'supplier_name' => 'Bounty Fresh'],
            ['sku' => 'ING-BTR-14', 'name' => 'Butter', 'unit' => 'grams', 'item_unit' => 225, 'item_price' => 65.00, 'stock_qty' => 4500.000, 'min_stock_qty' => 450.000, 'supplier_name' => 'Anchor Dairy'],
            ['sku' => 'ING-CRM-15', 'name' => 'Heavy cream', 'unit' => 'ml', 'item_unit' => 1000, 'item_price' => 225.00, 'stock_qty' => 5000.000, 'min_stock_qty' => 1000.000, 'supplier_name' => 'Elle & Vire'],
            ['sku' => 'ING-CHO-16', 'name' => 'Dark choco', 'unit' => 'grams', 'item_unit' => 1000, 'item_price' => 375.00, 'stock_qty' => 5000.000, 'min_stock_qty' => 500.000, 'supplier_name' => 'Bensdorp'],
            ['sku' => 'ING-SBT-17', 'name' => 'Salted butter', 'unit' => 'grams', 'item_unit' => 225, 'item_price' => 65.00, 'stock_qty' => 4500.000, 'min_stock_qty' => 450.000, 'supplier_name' => 'Queensland'],
        ];

        $ingMap = [];
        foreach ($pdfIngredients as $iData) {
            $supplier = \App\Models\Supplier::firstOrCreate(
                ['name' => $iData['supplier_name']],
                ['payment_terms' => 'Net 30']
            );

            $iData['supplier_id'] = $supplier->id;

            $ingMap[$iData['name']] = Ingredient::updateOrCreate(
                ['sku' => $iData['sku']],
                $iData
            );
        }

        // 2. Packaging Materials from PDF Reference
        $pdfPackaging = [
            ['sku' => 'PKG-BOX-01', 'name' => 'Box', 'type' => 'box', 'unit' => 'piece', 'cost_per_unit' => 8.00, 'stock_qty' => 500, 'min_stock_qty' => 50],
            ['sku' => 'PKG-NTE-02', 'name' => 'Note', 'type' => 'other', 'unit' => 'piece', 'cost_per_unit' => 12.00, 'stock_qty' => 200, 'min_stock_qty' => 20],
            ['sku' => 'PKG-PLC-03', 'name' => 'Plastic large', 'type' => 'bag', 'unit' => 'piece', 'cost_per_unit' => 2.00, 'stock_qty' => 500, 'min_stock_qty' => 50],
            ['sku' => 'PKG-PXL-04', 'name' => 'Plastic xl', 'type' => 'bag', 'unit' => 'piece', 'cost_per_unit' => 1.60, 'stock_qty' => 500, 'min_stock_qty' => 50],
            ['sku' => 'PKG-LGO-05', 'name' => 'Logo', 'type' => 'sticker', 'unit' => 'piece', 'cost_per_unit' => 1.87, 'stock_qty' => 1000, 'min_stock_qty' => 100],
        ];

        $pkgMap = [];
        foreach ($pdfPackaging as $pData) {
            $pkgMap[$pData['name']] = PackagingMaterial::updateOrCreate(
                ['sku' => $pData['sku']],
                $pData
            );
        }

        // 3. Link Choco Moist / Brownie Product with PDF Recipe
        $chocoProduct = Product::where('name', 'like', '%Choco%')
            ->orWhere('name', 'like', '%Brownie%')
            ->first();

        if (! $chocoProduct) {
            $chocoProduct = Product::first();
        }

        if ($chocoProduct) {
            $recipe = Recipe::updateOrCreate(
                ['product_id' => $chocoProduct->id],
                [
                    'name'                => 'Choco Moist Batch Recipe (PDF Standard)',
                    'yield_qty'           => 18,
                    'prep_time_minutes'   => 30,
                    'baking_time_minutes' => 45,
                    'instructions'        => 'Mix dry ingredients (flour, cocoa, baking powder, baking soda, salt). Combine wet ingredients. Bake and add natilla filling & chocolate ganache topping. Yields 18 tubs.',
                    'overhead_pct'        => 40.00,
                    'markup_pct'          => 66.00,
                    'labor_pct'           => 60.00,
                ]
            );

            // BOM Recipe Ingredients (from PDF breakdown)
            $bomItems = [
                ['name' => 'All purpose flour', 'qty' => 250, 'unit' => 'grams'],
                ['name' => 'Cocoa', 'qty' => 90, 'unit' => 'grams'],
                ['name' => 'Baking powder', 'qty' => 8, 'unit' => 'grams'],
                ['name' => 'Baking Soda', 'qty' => 8, 'unit' => 'grams'],
                ['name' => 'Water', 'qty' => 500, 'unit' => 'ml'],
                ['name' => 'Eggs', 'qty' => 1, 'unit' => 'piece'],
                ['name' => 'White vinegar', 'qty' => 30, 'unit' => 'ml'],
                ['name' => 'Canola oil', 'qty' => 108, 'unit' => 'ml'],
                ['name' => 'Salt', 'qty' => 6, 'unit' => 'grams'],
                ['name' => 'Evap', 'qty' => 760, 'unit' => 'grams'],
                ['name' => 'Cornstarch', 'qty' => 30, 'unit' => 'grams'],
                ['name' => 'Brown sugar', 'qty' => 125, 'unit' => 'grams'],
                ['name' => 'Yolk egg', 'qty' => 4, 'unit' => 'piece'],
                ['name' => 'Butter', 'qty' => 113, 'unit' => 'grams'],
                ['name' => 'Heavy cream', 'qty' => 1000, 'unit' => 'ml'],
                ['name' => 'Dark choco', 'qty' => 1000, 'unit' => 'grams'],
                ['name' => 'Salted butter', 'qty' => 225, 'unit' => 'grams'],
            ];

            RecipeIngredient::where('recipe_id', $recipe->id)->delete();
            foreach ($bomItems as $item) {
                if (isset($ingMap[$item['name']])) {
                    RecipeIngredient::create([
                        'recipe_id'     => $recipe->id,
                        'ingredient_id' => $ingMap[$item['name']]->id,
                        'qty_required'  => $item['qty'],
                        'unit'          => $item['unit'],
                    ]);
                }
            }

            // Recipe Packaging Items (from PDF breakdown)
            RecipePackaging::where('recipe_id', $recipe->id)->delete();
            RecipePackaging::create([
                'recipe_id'             => $recipe->id,
                'packaging_material_id' => $pkgMap['Box']->id,
                'name'                  => 'Box',
                'unit'                  => 'piece',
                'package_qty'           => 1,
                'package_cost'          => 8.00,
                'qty_used'              => 18,
            ]);
            RecipePackaging::create([
                'recipe_id'             => $recipe->id,
                'packaging_material_id' => $pkgMap['Plastic large']->id,
                'name'                  => 'Plastic large',
                'unit'                  => 'piece',
                'package_qty'           => 1,
                'package_cost'          => 2.00,
                'qty_used'              => 18,
            ]);
        }
    }
}
