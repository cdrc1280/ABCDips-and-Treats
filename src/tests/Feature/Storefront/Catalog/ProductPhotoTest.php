<?php

namespace Tests\Feature\Storefront\Catalog;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Feature\Storefront\StorefrontTestHelpers;
use Tests\TestCase;

/** Storefront product media: a real photo or null (never the logo), and the opt-in 3D flag. */
class ProductPhotoTest extends TestCase
{
    use RefreshDatabase, StorefrontTestHelpers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_product_without_a_photo_has_a_null_image_on_the_storefront(): void
    {
        $product = $this->product(['name' => 'Plain Brownie', 'is_featured' => true]);

        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->where('featured.0.name', 'Plain Brownie')
            ->where('featured.0.image_url', null));

        $this->get("/products/{$product->slug}")->assertOk()->assertInertia(fn (Assert $page) => $page
            ->where('product.image_url', null)
            ->where('product.images', []));

        // Back office, POS and the basket still get the logo so their thumbnails never break.
        $this->assertSame(asset('images/logo.png'), $product->primary_image_url);
    }

    public function test_bundled_photo_paths_resolve_to_public_images(): void
    {
        $product = $this->product([
            'image_path' => 'images/textures/authentic_banana_bread_box.jpg',
            'gallery' => ['images/textures/real_loaf_vertical.jpg', 'products/upload.jpg'],
        ]);

        $this->get("/products/{$product->slug}")->assertOk()->assertInertia(fn (Assert $page) => $page
            ->where('product.image_url', asset('images/textures/authentic_banana_bread_box.jpg'))
            ->where('product.images', [
                asset('images/textures/authentic_banana_bread_box.jpg'),
                asset('images/textures/real_loaf_vertical.jpg'),
                asset('storage/products/upload.jpg'),
            ]));
    }

    public function test_only_flagged_products_offer_the_3d_view(): void
    {
        $loaf = $this->product(['slug' => 'classic-banana-bread-loaf']);
        $brownie = $this->product();

        $this->get("/products/{$loaf->slug}")->assertInertia(fn (Assert $page) => $page->where('product.has_3d', true));
        $this->get("/products/{$brownie->slug}")->assertInertia(fn (Assert $page) => $page->where('product.has_3d', false));
    }
}
