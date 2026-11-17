<?php

namespace App\Domains\Storefront\Resources;

use App\Domains\Catalog\Models\Product;

/**
 * Storefront product shapes. Option lists are normalised here (label / name, numeric
 * modifier) so the browser never has to guess at legacy JSON layouts.
 */
final class ProductPresenter
{
    /**
     * @return array<string, mixed>
     */
    public static function card(Product $product): array
    {
        $attributes = $product->getAttributes();
        $reviews = array_key_exists('approved_reviews_count', $attributes) ? (int) $product->getAttribute('approved_reviews_count') : null;
        $rating = $reviews ? round((float) $product->getAttribute('approved_reviews_avg_rating'), 1) : null;

        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'short_description' => $product->short_description,
            'price' => (float) $product->price,
            'effective_price' => $product->effective_price,
            'is_on_sale' => $product->is_on_sale,
            'sale_ends_at' => $product->is_on_sale ? $product->sale_ends_at?->toIso8601String() : null,
            'prep_time_minutes' => $product->prep_time_minutes,
            'image_url' => $product->photo_url,
            'category' => $product->relationLoaded('category') && $product->category
                ? ['name' => $product->category->name, 'slug' => $product->category->slug]
                : null,
            'stock_qty' => (int) $product->stock_qty,
            'is_in_stock' => $product->stock_qty > 0,
            'is_low_stock' => $product->stock_qty > 0 && $product->stock_qty <= $product->min_stock_qty,
            'is_featured' => (bool) $product->is_featured,
            'is_best_seller' => (bool) $product->is_best_seller,
            'is_new_arrival' => (bool) $product->is_new_arrival,
            'is_limited' => (bool) $product->is_limited,
            'is_seasonal' => (bool) $product->is_seasonal,
            'has_options' => self::variations($product) !== [] || self::flavors($product) !== [],
            'avg_rating' => $rating,
            'reviews_count' => $reviews ?? 0,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function detail(Product $product): array
    {
        return [
            ...self::card($product),
            'sku' => $product->sku,
            'description' => $product->description,
            'flavor' => $product->flavor,
            'variation_type' => $product->variation_type ?: 'none',
            'variations' => self::variations($product),
            'flavors' => self::flavors($product),
            'is_assorted' => (bool) $product->is_assorted,
            'has_3d' => in_array($product->slug, Product::SLUGS_WITH_3D_MODEL, true),
            'max_flavors' => $product->max_flavors ? (int) $product->max_flavors : null,
            'images' => array_values(array_unique(array_filter([$product->photo_url, ...$product->gallery_image_urls]))),
            'tags' => $product->relationLoaded('tags') ? $product->tags->pluck('name')->all() : [],
            'allergens' => $product->relationLoaded('allergens')
                ? $product->allergens->map(fn ($a) => ['name' => $a->allergen_name, 'type' => $a->type])->all()
                : [],
            'nutrition' => $product->relationLoaded('nutrition') && $product->nutrition ? [
                'serving_size' => $product->nutrition->serving_size,
                'calories' => $product->nutrition->calories,
                'fat_g' => (float) $product->nutrition->fat_g,
                'carbs_g' => (float) $product->nutrition->carbs_g,
                'protein_g' => (float) $product->nutrition->protein_g,
                'sodium_mg' => (float) $product->nutrition->sodium_mg,
                'sugar_g' => (float) $product->nutrition->sugar_g,
            ] : null,
            'seo' => [
                'title' => $product->seo_title ?: $product->name,
                'description' => $product->seo_description ?: $product->short_description,
            ],
        ];
    }

    /**
     * @return list<array<string, string|float>> label + price_modifier
     */
    private static function variations(Product $product): array
    {
        return self::options((array) $product->variations, 'label');
    }

    /**
     * @return list<array<string, string|float>> name + price_modifier
     */
    private static function flavors(Product $product): array
    {
        return self::options((array) $product->flavors, 'name');
    }

    /**
     * @param  array<int|string, mixed>  $rows
     * @return list<array<string, string|float>>
     */
    private static function options(array $rows, string $key): array
    {
        $out = [];
        foreach ($rows as $row) {
            $value = is_array($row) ? trim((string) ($row[$key] ?? '')) : trim((string) $row);
            if ($value !== '') {
                $out[] = [$key => $value, 'price_modifier' => is_array($row) ? (float) ($row['price_modifier'] ?? 0) : 0.0];
            }
        }

        return $out;
    }
}
