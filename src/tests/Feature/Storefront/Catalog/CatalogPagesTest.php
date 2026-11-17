<?php

namespace Tests\Feature\Storefront\Catalog;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Feature\Storefront\StorefrontTestHelpers;
use Tests\TestCase;

class CatalogPagesTest extends TestCase
{
    use RefreshDatabase, StorefrontTestHelpers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_home_renders_with_collections_and_categories(): void
    {
        $this->product(['name' => 'Fudge Brownies', 'is_featured' => true]);

        $this->get('/')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('storefront/home')
            ->has('content.hero_title')
            ->where('featured.0.name', 'Fudge Brownies')
            ->where('categories.0.slug', 'brownies')
            ->where('categories.0.products_count', 1)
            ->where('cart.item_count', 0));
    }

    public function test_shop_filters_by_search_and_category_and_hides_inactive_products(): void
    {
        $this->product(['name' => 'Ube Loaf', 'category_id' => $this->category('breads')->id]);
        $this->product(['name' => 'Fudge Brownies']);
        $this->product(['name' => 'Hidden Ube', 'is_active' => false]);

        $this->get('/shop?search=ube')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('storefront/shop')
            ->where('filters.search', 'ube')
            ->has('products.data', 1)
            ->where('products.data.0.name', 'Ube Loaf'));

        $this->get('/shop?category=brownies&sort=bogus')->assertInertia(fn (Assert $page) => $page
            ->where('filters.sort', 'latest')
            ->has('products.data', 1)
            ->where('products.data.0.name', 'Fudge Brownies'));
    }

    public function test_search_wildcards_are_literal(): void
    {
        $this->product(['name' => 'Plain Cookie']);

        $this->get('/shop?search=%25')->assertInertia(fn (Assert $page) => $page->has('products.data', 0));
    }

    public function test_category_and_collection_pages(): void
    {
        $this->product(['name' => 'New Thing', 'is_new_arrival' => true]);

        $this->get('/category/brownies')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('storefront/category')->where('category.slug', 'brownies')->has('products.data', 1));
        $this->get('/category/missing')->assertNotFound();

        $this->get('/new-arrivals')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('storefront/collection')
            ->where('collection.key', 'new-arrivals')
            ->where('collection.title', 'New on the menu')
            ->missing('collection.tagline')
            ->where('products.0.name', 'New Thing'));
    }

    public function test_product_page_and_quick_view_normalise_options(): void
    {
        $product = $this->product([
            'slug' => 'brownie-box',
            'variations' => [['label' => 'Box of 6', 'price_modifier' => 0], ['label' => '', 'price_modifier' => 10]],
            'flavors' => [['name' => 'Ube', 'price_modifier' => '40']],
        ]);

        $this->get('/products/brownie-box')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('storefront/product')
            ->where('product.id', $product->id)
            ->has('product.variations', 1)
            ->where('product.flavors.0.price_modifier', 40));

        $this->getJson('/products/brownie-box/quick-view')->assertOk()->assertJsonPath('slug', 'brownie-box');
        $this->get('/products/nope')->assertNotFound();
    }

    public function test_expired_sale_price_is_not_charged(): void
    {
        $product = $this->product(['price' => 300, 'sale_price' => 200, 'sale_ends_at' => now()->subDay()]);

        $this->assertFalse($product->is_on_sale);
        $this->assertSame(300.0, $product->effective_price);
    }
}
