<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'image_url'   => $this->getFirstMediaUrl('category_image') ?: '/images/placeholder-category.png',
            'sort_order'  => $this->sort_order,
            'products_count' => $this->whenCounted('products'),
        ];
    }
}
