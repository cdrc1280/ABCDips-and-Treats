<?php

namespace Tests\Feature\Flows;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Catalog\Models\Product;
use App\Domains\Costing\Models\Recipe;
use App\Domains\Inventory\Models\Ingredient;
use App\Domains\Production\Enums\ProductionBatchPermission;
use App\Domains\Production\Models\ProductionBatch;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Feature\Storefront\StorefrontTestHelpers;
use Tests\TestCase;

/** Ingredients are consumed exactly once: at production for baked-ahead products, at sale for made-to-order ones. */
class ProductionToSaleFlowTest extends TestCase
{
    use RefreshDatabase, StorefrontTestHelpers;

    private Ingredient $flour;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Http::preventStrayRequests();
        $this->flour = Ingredient::query()->create(['sku' => 'FLR', 'name' => 'Flour', 'unit' => 'g', 'cost_per_unit' => 0.05, 'stock_qty' => 1000, 'min_stock_qty' => 100]);
    }

    /** A loaf whose recipe yields 4 loaves from 200 g of flour (50 g per loaf). */
    private function loaf(string $mode): Product
    {
        $product = $this->product(['price' => 300, 'stock_qty' => 0, 'inventory_mode' => $mode]);
        $recipe = Recipe::query()->create(['product_id' => $product->id, 'name' => 'Banana loaf', 'yield_qty' => 4]);
        $recipe->recipeIngredients()->create(['ingredient_id' => $this->flour->id, 'qty_required' => 200, 'unit' => 'g']);

        return $product;
    }

    private function buy(Product $product, int $qty): void
    {
        $customer = User::factory()->customer()->create(['email_verified_at' => now()]);
        $this->actingAs($customer)->post('/cart/items', ['product_id' => $product->id, 'qty' => $qty])->assertSessionHasNoErrors();
        $this->post('/checkout', ['customer_name' => 'Ana Cruz', 'customer_email' => $customer->email, 'customer_phone' => '09171234567',
            'fulfillment_type' => 'pickup', 'delivery_mode' => 'pooling', 'payment_method' => 'bank_transfer'])->assertRedirect();
    }

    public function test_baked_ahead_ingredients_are_used_at_production_not_again_at_sale(): void
    {
        $product = $this->loaf(Product::MODE_BAKED_AHEAD);
        $baker = $this->userWith(AccessPermission::DashboardView, ...ProductionBatchPermission::cases());

        $this->actingAs($baker)->post('/admin/production-batches', ['recipe_id' => $product->recipe->id ?? Recipe::query()->sole()->id, 'product_id' => $product->id, 'planned_qty' => 1, 'status' => 'planned'])
            ->assertSessionHasNoErrors();
        $batch = ProductionBatch::query()->sole();
        $this->patch("/admin/production-batches/{$batch->id}/complete")->assertSessionHasNoErrors();

        $this->assertEquals(800, (float) $this->flour->fresh()->stock_qty); // 200 g used by the batch
        $this->assertSame(4, $product->fresh()->stock_qty);                  // 4 loaves made

        $this->app['auth']->forgetGuards();
        $this->buy($product, 2);

        $this->assertSame(2, $product->fresh()->stock_qty);
        $this->assertEquals(800, (float) $this->flour->fresh()->stock_qty, 'Selling baked-ahead loaves must not use flour a second time.');
    }

    public function test_made_to_order_ingredients_are_used_when_sold_and_cannot_be_batch_produced(): void
    {
        $product = $this->loaf(Product::MODE_MADE_TO_ORDER);
        $product->update(['stock_qty' => 10]);

        $this->buy($product, 2); // 2 loaves × (200 g / 4) = 100 g
        $this->assertEquals(900, (float) $this->flour->fresh()->stock_qty);

        $baker = $this->userWith(AccessPermission::DashboardView, ...ProductionBatchPermission::cases());
        $this->actingAs($baker)->post('/admin/production-batches', ['recipe_id' => Recipe::query()->sole()->id, 'product_id' => $product->id, 'planned_qty' => 1, 'status' => 'planned'])
            ->assertSessionHasErrors('product_id');
    }
}
