<?php

namespace App\Domains\Catalog\Resources;

use App\Domains\Catalog\Models\Product;
use App\Shared\Services\PublicUploads;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class ProductResource extends Resource
{
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public string $categoryId,
        public ?string $categoryName,
        public string $sku,
        public ?string $barcode,
        public ?string $shortDescription,
        public ?string $description,
        public array $tags,
        public ?string $imagePath,
        public ?string $imagePathUrl,
        public array $gallery,
        public array $galleryUrls,
        public float $price,
        public ?float $salePrice,
        public ?string $saleEndsAt,
        public int $stockQty,
        public int $minStockQty,
        public int $prepTimeMinutes,
        public string $variationType,
        public ?string $variationTypeLabel,
        public string $inventoryMode,
        public array $variations,
        public bool $isAssorted,
        public ?int $maxFlavors,
        public ?string $flavor,
        public array $flavors,
        public bool $isFeatured,
        public bool $isBestSeller,
        public bool $isNewArrival,
        public bool $isSeasonal,
        public bool $isLimited,
        public bool $isActive,
        public ?string $seoTitle,
        public ?string $seoDescription,
        public bool $isLowStock,
        public bool $isOnSale,
        public bool $hasOrders,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel(Product $product, array $context = []): self
    {
        return new self(
            id: $product->id,
            name: $product->name,
            slug: $product->slug,
            categoryId: $product->category_id,
            categoryName: $product->category?->name,
            sku: $product->sku,
            barcode: $product->barcode,
            shortDescription: $product->short_description,
            description: $product->description,
            tags: $product->tags->pluck('name')->values()->all(),
            imagePath: $product->image_path,
            imagePathUrl: self::url($product->image_path),
            gallery: array_values((array) ($product->gallery ?? [])),
            galleryUrls: array_map(fn ($path) => self::url($path), array_values((array) ($product->gallery ?? []))),
            price: (float) $product->price,
            salePrice: $product->sale_price === null ? null : (float) $product->sale_price,
            saleEndsAt: self::dateTime($product->sale_ends_at),
            stockQty: (int) $product->stock_qty,
            minStockQty: (int) $product->min_stock_qty,
            prepTimeMinutes: (int) $product->prep_time_minutes,
            variationType: (string) $product->variation_type,
            inventoryMode: (string) $product->inventory_mode,
            variationTypeLabel: self::option(['none' => 'No variations', 'assorted' => 'Assorted (multi-flavor box)', 'weight' => 'Weight / grams', 'pieces' => 'Pieces', 'size' => 'Size', 'packaging' => 'Packaging', 'custom' => 'Custom'], $product->variation_type),
            variations: array_values((array) ($product->variations ?? [])),
            isAssorted: (bool) $product->is_assorted,
            maxFlavors: $product->max_flavors === null ? null : (int) $product->max_flavors,
            flavor: $product->flavor,
            flavors: array_values((array) ($product->flavors ?? [])),
            isFeatured: (bool) $product->is_featured,
            isBestSeller: (bool) ($product->getAttributes()['is_best_seller'] ?? false),
            isNewArrival: (bool) ($product->getAttributes()['is_new_arrival'] ?? false),
            isSeasonal: (bool) $product->is_seasonal,
            isLimited: (bool) $product->is_limited,
            isActive: (bool) $product->is_active,
            seoTitle: $product->seo_title,
            seoDescription: $product->seo_description,
            isLowStock: $product->stock_qty <= $product->min_stock_qty,
            isOnSale: (bool) $product->is_on_sale,
            hasOrders: $product->orderItems()->exists(),
            createdAt: $product->created_at?->toIso8601String(),
            updatedAt: $product->updated_at?->toIso8601String(),
        );
    }

    private static function dateTime(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        return ($value instanceof \DateTimeInterface ? Carbon::instance($value) : Carbon::parse((string) $value))->toIso8601String();
    }

    private static function url(?string $path): ?string
    {
        return PublicUploads::url($path);
    }

    /**
     * @param  array<string, string>  $options
     */
    private static function option(array $options, mixed $value): ?string
    {
        return $value === null ? null : ($options[(string) $value] ?? (string) $value);
    }
}
