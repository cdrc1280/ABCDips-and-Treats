<?php

namespace Tests\Feature\Catalog\Product;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Catalog\Enums\ProductPermission;
use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Hand-written: the collection fields (variations, flavors, tags, gallery) the generic test doesn't cover. */
class ProductOptionsTest extends TestCase
{
    use RefreshDatabase;

    private function payload(ProductCategory $category, array $overrides = []): array
    {
        return array_merge([
            'name' => 'Brownie Box', 'slug' => 'brownie-box', 'category_id' => $category->id, 'sku' => 'BRW-1',
            'price' => '450', 'stock_qty' => '10', 'min_stock_qty' => '2', 'prep_time_minutes' => '30',
            'variation_type' => 'pieces', 'is_active' => '1',
        ], $overrides);
    }

    public function test_variations_flavors_and_tags_are_cleaned_cast_and_synced(): void
    {
        $category = ProductCategory::query()->create(['name' => 'Brownies', 'slug' => 'brownies']);
        $this->actingAs($this->userWith(AccessPermission::DashboardView, ...ProductPermission::cases()));

        $this->post('/admin/products', $this->payload($category, [
            'variations' => [['label' => 'Box of 6', 'price_modifier' => '0'], ['label' => 'Box of 12', 'price_modifier' => '380.5'], ['label' => '', 'price_modifier' => '']],
            'flavors' => [['name' => 'Fudge', 'price_modifier' => '']],
            'tags' => 'Bestseller, gift ,',
        ]))->assertSessionHasNoErrors()->assertRedirect('/admin/products');

        $product = Product::query()->where('sku', 'BRW-1')->firstOrFail();
        $this->assertEquals([['label' => 'Box of 6', 'price_modifier' => 0.0], ['label' => 'Box of 12', 'price_modifier' => 380.5]], $product->variations);
        $this->assertSame([['name' => 'Fudge', 'price_modifier' => null]], $product->flavors);
        $this->assertEqualsCanonicalizing(['Bestseller', 'gift'], $product->tags->pluck('name')->all());
    }

    public function test_variation_rows_are_validated(): void
    {
        $category = ProductCategory::query()->create(['name' => 'Brownies', 'slug' => 'brownies']);
        $this->actingAs($this->userWith(AccessPermission::DashboardView, ...ProductPermission::cases()));

        $this->post('/admin/products', $this->payload($category, ['variations' => [['label' => '', 'price_modifier' => 'abc']]]))
            ->assertSessionHasErrors(['variations.0.label', 'variations.0.price_modifier']);
    }

    public function test_gallery_keeps_removes_and_appends_images(): void
    {
        Storage::fake('public');
        $category = ProductCategory::query()->create(['name' => 'Brownies', 'slug' => 'brownies']);
        $this->actingAs($this->userWith(AccessPermission::DashboardView, ...ProductPermission::cases()));

        $this->post('/admin/products', $this->payload($category, ['gallery' => [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.jpg')]]))
            ->assertSessionHasNoErrors();
        $product = Product::query()->where('sku', 'BRW-1')->firstOrFail();
        [$first, $second] = $product->gallery;

        $this->post("/admin/products/{$product->id}", $this->payload($category, [
            '_method' => 'put', 'keep_gallery' => [$second], 'gallery' => [UploadedFile::fake()->image('c.jpg')],
        ]))->assertSessionHasNoErrors();

        $gallery = $product->refresh()->gallery;
        $this->assertCount(2, $gallery);
        $this->assertSame($second, $gallery[0]);
        Storage::disk('public')->assertMissing($first);
        Storage::disk('public')->assertExists($gallery[1]);
    }
}
