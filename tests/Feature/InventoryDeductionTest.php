<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PackagingMaterial;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\RecipePackaging;
use App\Services\InventoryDeductionService;
use App\Services\UnitConverterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryDeductionTest extends TestCase
{
    use RefreshDatabase;

    public function test_unit_converter_service_conversions(): void
    {
        // Mass
        $this->assertEquals(0.25, UnitConverterService::convert(250, 'g', 'kg'));
        $this->assertEquals(2500, UnitConverterService::convert(2.5, 'kg', 'g'));

        // Volume & Kitchen Measures
        $this->assertEquals(1.2, UnitConverterService::convert(5, 'cups', 'l')); // 5 * 240ml = 1200ml = 1.2L
        $this->assertEquals(1200, UnitConverterService::convert(1, '5 cups', 'ml')); // 5 cups = 1200ml
        $this->assertEquals(120, UnitConverterService::convert(1, '1/2 cup', 'ml')); // 1/2 cup = 120ml

        // Pieces
        $this->assertEquals(4, UnitConverterService::convert(4, 'piece', 'pcs'));
    }

    public function test_ingredient_auto_computes_cost_per_unit_from_item_price_and_item_unit(): void
    {
        $flour = Ingredient::create([
            'sku'        => 'ING-TEST-01',
            'name'       => 'All Purpose Flour',
            'unit'       => 'grams',
            'item_unit'  => 1000,
            'item_price' => 65.00,
            'stock_qty'  => 5000,
        ]);

        $this->assertEquals(0.065, $flour->cost_per_unit);
        $this->assertEquals(0.065, $flour->price_unit);
    }

    public function test_realtime_ingredient_and_packaging_stock_deduction_on_order(): void
    {
        $flour = Ingredient::create([
            'sku'        => 'ING-FLR-TEST',
            'name'       => 'Flour',
            'unit'       => 'grams',
            'item_unit'  => 1000,
            'item_price' => 65.00,
            'stock_qty'  => 10000.000,
        ]);

        $egg = Ingredient::create([
            'sku'        => 'ING-EGG-TEST',
            'name'       => 'Egg',
            'unit'       => 'piece',
            'item_unit'  => 1,
            'item_price' => 9.00,
            'stock_qty'  => 100.000,
        ]);

        $box = PackagingMaterial::create([
            'sku'           => 'PKG-BOX-TEST',
            'name'          => 'Box',
            'type'          => 'box',
            'unit'          => 'piece',
            'cost_per_unit' => 8.00,
            'stock_qty'     => 500.000,
        ]);

        $category = \App\Models\ProductCategory::create([
            'name' => 'Pastries',
            'slug' => 'pastries-test',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Brownies',
            'slug'        => 'brownies-test',
            'sku'         => 'SKU-BRN-TEST',
            'price'       => 150.00,
            'is_active'   => true,
        ]);

        $recipe = Recipe::create([
            'product_id' => $product->id,
            'name'       => 'Brownies Recipe',
            'yield_qty'  => 10, // 1 batch = 10 brownies
        ]);

        RecipeIngredient::create(['recipe_id' => $recipe->id, 'ingredient_id' => $flour->id, 'qty_required' => 500, 'unit' => 'grams']); // 50g per brownie
        RecipeIngredient::create(['recipe_id' => $recipe->id, 'ingredient_id' => $egg->id, 'qty_required' => 5, 'unit' => 'piece']); // 0.5 egg per brownie
        RecipePackaging::create(['recipe_id' => $recipe->id, 'packaging_material_id' => $box->id, 'name' => 'Box', 'unit' => 'piece', 'package_qty' => 1, 'package_cost' => 8.00, 'qty_used' => 10]); // 1 box per brownie

        $order = Order::create([
            'order_number'     => 'POS-TEST-001',
            'tracking_token'   => \Illuminate\Support\Str::random(40),
            'customer_name'    => 'Test Customer',
            'customer_email'   => 'test@example.com',
            'customer_phone'   => '09170000000',
            'fulfillment_type' => 'pickup',
            'payment_method'   => 'cash',
            'subtotal'         => 300.00,
            'total'            => 300.00,
            'status'           => Order::STATUS_COMPLETED,
        ]);

        OrderItem::create([
            'order_id'     => $order->id,
            'product_id'   => $product->id,
            'product_name' => $product->name,
            'product_sku'  => $product->sku,
            'qty'          => 2, // Customer ordered 2 brownies (2/10th of batch)
            'unit_price'   => 150.00,
            'subtotal'     => 300.00,
        ]);

        // Trigger deduction
        $service = new InventoryDeductionService();
        $service->deductForOrder($order);

        // 2 brownies * (500g / 10) = 100g flour deducted -> 9900g remaining
        $this->assertEquals(9900.000, $flour->fresh()->stock_qty);

        // 2 brownies * (5 eggs / 10) = 1 egg deducted -> 99 eggs remaining
        $this->assertEquals(99.000, $egg->fresh()->stock_qty);

        // 2 brownies * (10 boxes / 10) = 2 boxes deducted -> 498 boxes remaining
        $this->assertEquals(498.000, $box->fresh()->stock_qty);

        // Test Revert / Order Cancellation
        $service->revertForOrder($order);

        $this->assertEquals(10000.000, $flour->fresh()->stock_qty);
        $this->assertEquals(100.000, $egg->fresh()->stock_qty);
        $this->assertEquals(500.000, $box->fresh()->stock_qty);
    }
}
