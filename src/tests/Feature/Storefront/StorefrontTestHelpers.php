<?php

namespace Tests\Feature\Storefront;

use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductCategory;

trait StorefrontTestHelpers
{
    protected function category(string $slug = 'brownies'): ProductCategory
    {
        return ProductCategory::query()->firstOrCreate(['slug' => $slug], ['name' => ucfirst($slug), 'is_active' => true]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function product(array $attributes = []): Product
    {
        static $n = 0;
        $n++;

        return Product::query()->create([
            'category_id' => $this->category()->id,
            'sku' => "SKU-{$n}",
            'name' => "Treat {$n}",
            'slug' => "treat-{$n}",
            'price' => 300,
            'stock_qty' => 20,
            'min_stock_qty' => 2,
            'is_active' => true,
            ...$attributes,
        ]);
    }
}
