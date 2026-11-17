<?php

namespace Tests\Feature\Storefront\Cart;

use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductCategory;
use App\Domains\Sales\Models\CartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Prices are decided on the server; nothing the browser sends can lower them. */
class CartPriceTamperingTest extends TestCase
{
    use RefreshDatabase;

    private Product $box;

    protected function setUp(): void
    {
        parent::setUp();
        $category = ProductCategory::query()->create(['name' => 'Brownies', 'slug' => 'brownies']);
        $this->box = Product::query()->create([
            'category_id' => $category->id, 'sku' => 'BOX', 'name' => 'Brownie box', 'slug' => 'brownie-box', 'price' => 450,
            'variation_type' => 'pieces', 'variations' => [['label' => 'Box of 6', 'price_modifier' => 0], ['label' => 'Box of 12', 'price_modifier' => 380]],
            'is_assorted' => true, 'max_flavors' => 2,
            'flavors' => [['name' => 'Fudge', 'price_modifier' => 0], ['name' => 'Ube', 'price_modifier' => 40], ['name' => 'Matcha', 'price_modifier' => 60]],
        ]);
    }

    public function test_client_unit_price_and_modifiers_are_ignored(): void
    {
        $this->post('/cart/items', ['product_id' => $this->box->id, 'qty' => 1, 'options' => [
            'variation' => 'Box of 12', 'unit_price' => 1, 'price_modifier' => -999, 'flavor_price_modifier' => -500,
        ]])->assertRedirect();

        $this->assertEquals(830, (float) CartItem::query()->firstOrFail()->unit_price); // 450 + 380
    }

    public function test_flavor_modifiers_come_from_the_product_and_respect_max_flavors(): void
    {
        $this->post('/cart/items', ['product_id' => $this->box->id, 'qty' => 1, 'options' => [
            'variation' => 'Box of 6', 'flavors' => ['Ube', 'Matcha', 'Fudge'],
        ]])->assertRedirect();

        $item = CartItem::query()->firstOrFail();
        $this->assertEquals(550, (float) $item->unit_price); // 450 + 40 (Ube) + 60 (Matcha); 3rd flavor dropped (max 2)
        $this->assertSame(['Ube', 'Matcha'], $item->options['flavors']);
    }

    public function test_unknown_variation_gets_no_discount_and_stored_modifiers_cannot_be_replayed(): void
    {
        $this->post('/cart/items', ['product_id' => $this->box->id, 'qty' => 1, 'options' => ['variation' => 'FREE BOX']])->assertRedirect();
        $item = CartItem::query()->firstOrFail();
        $this->assertEquals(450, (float) $item->unit_price);

        // Even if a stored line were edited to carry a negative modifier, recalculation re-derives the price.
        $item->forceFill(['options' => ['price_modifier' => -400], 'unit_price' => 50])->save();
        $this->withoutVite()->get('/cart')->assertOk();
        $this->assertEquals(450, (float) $item->refresh()->unit_price);
    }

    public function test_custom_cake_price_comes_from_tiers_and_servings(): void
    {
        $this->post('/cart/items', ['product_id' => $this->box->id, 'qty' => 1, 'options' => [
            'is_custom' => true, 'tiers_count' => 3, 'servings_count' => 80, 'unit_price' => 10,
        ]])->assertRedirect();

        $this->assertEquals(5300, (float) CartItem::query()->firstOrFail()->unit_price); // 4500 + 800
    }
}
