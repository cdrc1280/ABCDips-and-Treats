<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $reviewsQuery = $this->reviews()->where('is_approved', true);
        $count = $reviewsQuery->count();
        $avg = $count > 0 ? round((float) $reviewsQuery->avg('rating'), 1) : null;

        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'name' => $this->name,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'price' => (float) $this->price,
            'variation_type'   => $this->variation_type ?? 'none',
            'variations'       => $this->variations ?? [],
            'sale_price' => $this->sale_price ? (float) $this->sale_price : null,
            'effective_price' => $this->effective_price,
            'is_on_sale' => $this->is_on_sale,
            'sale_ends_at' => $this->sale_ends_at?->toISOString(),
            'prep_time_minutes' => $this->prep_time_minutes,
            'stock_qty' => $this->stock_qty,
            'is_in_stock' => $this->stock_qty > 0,
            'is_low_stock' => $this->stock_qty > 0 && $this->stock_qty <= $this->min_stock_qty,
            'is_featured' => $this->is_featured,
            'is_best_seller' => $this->is_best_seller,
            'is_new_arrival' => $this->is_new_arrival,
            'is_seasonal' => $this->is_seasonal,
            'is_limited' => $this->is_limited,
            'avg_rating' => $avg,
            'reviews_count' => $count,
            'is_highly_rated' => $count > 0 && $avg >= 4.5,
            'primary_image_url' => $this->primary_image_url,
            'gallery_images' => $this->gallery_image_urls,
            'category' => new ProductCategoryResource($this->whenLoaded('category')),
            'tags' => $this->whenLoaded('tags', fn() => $this->tags->pluck('name')),
            'allergens' => $this->whenLoaded('allergens', fn() => $this->allergens->map(fn($a) => [
                'name' => $a->allergen_name,
                'type' => $a->type,
            ])),
            'nutrition' => $this->whenLoaded('nutrition', fn() => [
                'serving_size' => $this->nutrition->serving_size,
                'calories' => $this->nutrition->calories,
                'fat_g' => (float) $this->nutrition->fat_g,
                'carbs_g' => (float) $this->nutrition->carbs_g,
                'protein_g' => (float) $this->nutrition->protein_g,
                'sodium_mg' => (float) $this->nutrition->sodium_mg,
                'sugar_g' => (float) $this->nutrition->sugar_g,
            ]),
            'seo' => [
                'title' => $this->seo_title ?? $this->name,
                'description' => $this->seo_description ?? $this->short_description,
            ],
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
