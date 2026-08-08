<?php

namespace Tests\Feature\Api;

use App\Models\Ingredient;
use App\Models\Product;
use App\Models\ProductionBatch;
use App\Models\Recipe;
use App\Models\User;

use App\Services\ProductionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class M5InventoryAndPosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->seed(\Database\Seeders\ProductSeeder::class);
        $this->seed(\Database\Seeders\InventorySeeder::class);
    }

    public function test_can_calculate_recipe_cost_and_margin(): void
    {
        $recipe = Recipe::with('recipeIngredients.ingredient', 'product')->first();

        $this->assertNotNull($recipe);
        $this->assertGreaterThan(0, $recipe->calculated_cost);
        $this->assertGreaterThan(0, $recipe->gross_margin_percentage);
    }

    public function test_can_complete_production_batch_and_update_stock(): void
    {
        $recipe = Recipe::first();
        $product = $recipe->product;
        $flour = Ingredient::where('sku', 'ING-FLR-01')->first();

        $initialProductStock = $product->stock_qty;
        $initialFlourStock   = (float) $flour->stock_qty;

        $prodService = app(ProductionService::class);
        $batch = $prodService->createBatch($recipe, 2); // 2 batches

        $prodService->completeBatch($batch);

        $this->assertEquals(ProductionBatch::STATUS_COMPLETED, $batch->fresh()->status);
        $this->assertGreaterThan($initialProductStock, $product->fresh()->stock_qty);
        $this->assertLessThan($initialFlourStock, (float) $flour->fresh()->stock_qty);
    }

    public function test_can_fetch_pos_products(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/pos/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'sku', 'name', 'price', 'effective_price', 'stock_qty']
                ]
            ]);
    }

    public function test_can_checkout_pos_walkin_order(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $product = Product::first();

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/pos/checkout', [
            'customer_name'  => 'Walk-in Customer 01',
            'payment_method' => 'cash',
            'cash_tendered'  => 500,
            'items'          => [
                ['id' => $product->id, 'qty' => 1]
            ]
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'cash_tendered',
                'change_due',
                'data' => ['id', 'order_number', 'status', 'total']
            ]);

        $this->assertDatabaseHas('orders', ['customer_name' => 'Walk-in Customer 01']);
    }
}
