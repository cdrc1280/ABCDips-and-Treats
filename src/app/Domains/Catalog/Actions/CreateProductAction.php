<?php

namespace App\Domains\Catalog\Actions;

use App\Domains\Catalog\Data\ProductData;
use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\Tag;
use App\Shared\Services\PublicUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class CreateProductAction
{
    public function __construct(private readonly PublicUploads $uploads) {}

    public function execute(ProductData $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = Product::query()->create([
                'name' => $data->name,
                'slug' => $data->slug,
                'category_id' => $data->categoryId,
                'sku' => $data->sku,
                'barcode' => $data->barcode,
                'short_description' => $data->shortDescription,
                'description' => $data->description,
                'image_path' => $data->imagePath ? $this->uploads->store($data->imagePath, 'products') : null,
                'gallery' => array_map(fn ($file) => $this->uploads->store($file, 'products'), $data->gallery),
                'price' => $data->price,
                'sale_price' => $data->salePrice,
                'sale_ends_at' => $data->saleEndsAt,
                'stock_qty' => $data->stockQty,
                'min_stock_qty' => $data->minStockQty,
                'prep_time_minutes' => $data->prepTimeMinutes,
                'variation_type' => $data->variationType,
                'variations' => array_map(fn (array $row) => ['label' => isset($row['label']) && $row['label'] !== '' ? (string) $row['label'] : null, 'price_modifier' => isset($row['price_modifier']) && $row['price_modifier'] !== '' ? (float) $row['price_modifier'] : null], $data->variations),
                'is_assorted' => $data->isAssorted,
                'max_flavors' => $data->maxFlavors,
                'flavor' => $data->flavor,
                'flavors' => array_map(fn (array $row) => ['name' => isset($row['name']) && $row['name'] !== '' ? (string) $row['name'] : null, 'price_modifier' => isset($row['price_modifier']) && $row['price_modifier'] !== '' ? (float) $row['price_modifier'] : null], $data->flavors),
                'is_featured' => $data->isFeatured,
                'inventory_mode' => $data->inventoryMode,
                'is_best_seller' => $data->isBestSeller,
                'is_new_arrival' => $data->isNewArrival,
                'is_seasonal' => $data->isSeasonal,
                'is_limited' => $data->isLimited,
                'is_active' => $data->isActive,
                'seo_title' => $data->seoTitle,
                'seo_description' => $data->seoDescription,
            ]);
            $product->tags()->sync(collect($data->tags)
                ->map(fn (string $name) => Tag::query()->firstOrCreate(['slug' => Str::slug($name)], ['name' => $name])->id)
                ->all());

            return $product;
        });
    }
}
