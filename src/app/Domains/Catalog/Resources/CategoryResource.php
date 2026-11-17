<?php

namespace App\Domains\Catalog\Resources;

use App\Domains\Catalog\Models\ProductCategory;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class CategoryResource extends Resource
{
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public ?string $description,
        public int $sortOrder,
        public bool $isActive,
        public ?string $imagePath,
        public ?string $imageUrl,
        public int $productsCount,
        /** The UI hides "Delete" when true (the action would be refused). */
        public bool $hasProducts,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel(ProductCategory $category, array $context = []): self
    {
        $count = (int) ($category->products_count ?? $category->products()->count());

        return new self(
            id: $category->id,
            name: $category->name,
            slug: $category->slug,
            description: $category->description,
            sortOrder: (int) $category->sort_order,
            isActive: (bool) $category->is_active,
            imagePath: $category->image_path,
            imageUrl: $category->image_url,
            productsCount: $count,
            hasProducts: $count > 0,
            createdAt: $category->created_at?->toIso8601String(),
            updatedAt: $category->updated_at?->toIso8601String(),
        );
    }
}
