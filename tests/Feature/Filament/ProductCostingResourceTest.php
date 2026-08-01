<?php

namespace Tests\Feature\Filament;

use App\Models\CostingItem;
use App\Models\ProductCosting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCostingResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@abcdips.test',
        ]);
    }

    public function test_product_costing_calculates_exact_reference_demo_sample(): void
    {
        $costing = ProductCosting::create([
            'name'         => 'Choco Moist w/ Natilla Filling',
            'yield_qty'    => 18,
            'yield_unit'   => 'tub (70g)',
            'overhead_pct' => 40.00,
            'markup_pct'   => 66.00,
            'labor_pct'    => 60.00,
        ]);

        // Ingredients
        CostingItem::create([
            'product_costing_id' => $costing->id,
            'group'              => 'ingredient',
            'name'               => 'All purpose flour',
            'unit'               => 'grams',
            'package_amount'     => 1000,
            'package_price'      => 65.00,
            'qty_used'           => 250, // 250 * (65/1000) = 16.25
        ]);

        CostingItem::create([
            'product_costing_id' => $costing->id,
            'group'              => 'ingredient',
            'name'               => 'Cocoa',
            'unit'               => 'grams',
            'package_amount'     => 500,
            'package_price'      => 395.00,
            'qty_used'           => 90, // 90 * (395/500) = 71.10
        ]);

        // Packaging
        CostingItem::create([
            'product_costing_id' => $costing->id,
            'group'              => 'packaging',
            'name'               => 'Box',
            'unit'               => 'piece',
            'package_amount'     => 1,
            'package_price'      => 8.00,
            'qty_used'           => 18, // 18 * 8 = 144.00
        ]);

        $costing->refresh();

        // Ingredients subtotal: 16.25 + 71.10 = 87.35
        $this->assertEquals(87.35, $costing->ingredients_subtotal);

        // Packaging subtotal: 144.00
        $this->assertEquals(144.00, $costing->packaging_subtotal);

        // Total Raw Cost: 87.35 + 144.00 = 231.35
        $this->assertEquals(231.35, $costing->raw_cost);

        // Overhead (40% of 231.35) = 92.54
        $this->assertEquals(92.54, round($costing->overhead_cost, 2));

        // Total Cost: 231.35 + 92.54 = 323.89
        $this->assertEquals(323.89, round($costing->total_cost, 2));

        // Markup (66% of 323.89) = 213.77
        $this->assertEquals(213.77, round($costing->markup, 2));

        // Labor Charge (60% of 231.35) = 138.81
        $this->assertEquals(138.81, round($costing->labor_charge, 2));

        // Selling Price: 323.89 + 213.77 + 138.81 = 676.47
        $this->assertEquals(676.47, round($costing->selling_price, 2));

        // Price per piece (18 yield): 676.47 / 18 = 37.58
        $this->assertEquals(37.58, round($costing->price_per_piece, 2));
    }

    public function test_division_by_zero_guards_return_zero_without_error(): void
    {
        $costing = ProductCosting::create([
            'name'      => 'Zero Guard Test',
            'yield_qty' => 0, // 0 yield guard
        ]);

        $item = CostingItem::create([
            'product_costing_id' => $costing->id,
            'group'              => 'ingredient',
            'name'               => 'Test Item',
            'unit'               => 'grams',
            'package_amount'     => 0, // 0 package amount guard
            'package_price'      => 100,
            'qty_used'           => 50,
        ]);

        $this->assertEquals(0.0, $item->price_per_unit);
        $this->assertEquals(0.0, $item->line_cost);
        $this->assertEquals(0.0, $costing->price_per_piece);
    }

    public function test_admin_can_access_product_costing_filament_pages(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/product-costings');
        $response->assertStatus(200);

        $responseCreate = $this->actingAs($this->admin)->get('/admin/product-costings/create');
        $responseCreate->assertStatus(200);
    }

    public function test_saving_product_costing_automatically_updates_product_price_in_database(): void
    {
        $category = \App\Models\ProductCategory::create([
            'name' => 'Pastries',
            'slug' => 'pastries-test',
        ]);

        $product = \App\Models\Product::create([
            'category_id' => $category->id,
            'sku'         => 'SKU-AUTO-PRICE',
            'name'        => 'Auto Price Loaf',
            'slug'        => 'auto-price-loaf',
            'price'       => 100.00,
            'is_active'   => true,
        ]);

        $costing = ProductCosting::create([
            'product_id'   => $product->id,
            'yield_qty'    => 10,
            'overhead_pct' => 40.00,
            'markup_pct'   => 66.00,
            'labor_pct'    => 60.00,
        ]);

        CostingItem::create([
            'product_costing_id' => $costing->id,
            'group'              => 'ingredient',
            'package_amount'     => 1,
            'package_price'      => 100.00,
            'qty_used'           => 1,
        ]);

        // Save costing to trigger auto-sync
        $costing->save();

        $product->refresh();
        $this->assertTrue($product->price > 0);
        $this->assertEquals(round($costing->price_per_piece, 2), (float) $product->price);
    }
}
