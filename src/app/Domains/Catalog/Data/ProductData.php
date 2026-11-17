<?php

namespace App\Domains\Catalog\Data;

use App\Domains\Catalog\Models\Product;
use App\Shared\Support\RouteModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class ProductData extends Data
{
    public function __construct(
        public string $name,
        public string $slug,
        public string $categoryId,
        public string $sku,
        public float $price,
        public ?string $barcode = null,
        public ?string $shortDescription = null,
        public ?string $description = null,
        public array $tags = [],
        public ?UploadedFile $imagePath = null,
        public bool $removeImagePath = false,
        public array $gallery = [],
        public array $keepGallery = [],
        public ?float $salePrice = null,
        public ?string $saleEndsAt = null,
        public ?int $stockQty = 50,
        public ?int $minStockQty = 10,
        public ?int $prepTimeMinutes = 30,
        public ?string $variationType = 'none',
        public string $inventoryMode = Product::MODE_MADE_TO_ORDER,
        public array $variations = [],
        public bool $isAssorted = false,
        public ?int $maxFlavors = null,
        public ?string $flavor = null,
        public array $flavors = [],
        public bool $isFeatured = false,
        public bool $isBestSeller = false,
        public bool $isNewArrival = false,
        public bool $isSeasonal = false,
        public bool $isLimited = false,
        public bool $isActive = true,
        public ?string $seoTitle = null,
        public ?string $seoDescription = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['name'] = trim((string) ($properties['name'] ?? '')) ?: null;
        $properties['slug'] = Str::slug(trim((string) ($properties['slug'] ?? '')) ?: (string) ($properties['name'] ?? ''));
        $properties['category_id'] = ($properties['category_id'] ?? '') === '' ? null : (string) $properties['category_id'];
        $properties['sku'] = strtoupper(trim((string) ($properties['sku'] ?? ''))) ?: null;
        $properties['price'] = ($properties['price'] ?? '') === '' ? null : (float) $properties['price'];
        $properties['barcode'] = strtoupper(trim((string) ($properties['barcode'] ?? ''))) ?: null;
        $properties['short_description'] = trim((string) ($properties['short_description'] ?? '')) ?: null;
        $properties['description'] = trim((string) ($properties['description'] ?? '')) ?: null;
        $properties['tags'] = is_array($properties['tags'] ?? null) ? array_values(array_filter(array_map('trim', $properties['tags']))) : array_values(array_filter(array_map('trim', explode(',', (string) ($properties['tags'] ?? '')))));
        $properties['remove_image_path'] = filter_var($properties['remove_image_path'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $properties['gallery'] = array_values(array_filter((array) ($properties['gallery'] ?? [])));
        $properties['keep_gallery'] = array_values(array_filter((array) ($properties['keep_gallery'] ?? [])));
        $properties['sale_price'] = ($properties['sale_price'] ?? '') === '' ? null : (float) $properties['sale_price'];
        $properties['sale_ends_at'] = trim((string) ($properties['sale_ends_at'] ?? '')) ?: null;
        $properties['stock_qty'] = ($properties['stock_qty'] ?? '') === '' ? 50 : (int) $properties['stock_qty'];
        $properties['min_stock_qty'] = ($properties['min_stock_qty'] ?? '') === '' ? 10 : (int) $properties['min_stock_qty'];
        $properties['prep_time_minutes'] = ($properties['prep_time_minutes'] ?? '') === '' ? 30 : (int) $properties['prep_time_minutes'];
        $properties['variation_type'] = trim((string) ($properties['variation_type'] ?? '')) ?: 'none';
        $properties['inventory_mode'] = trim((string) ($properties['inventory_mode'] ?? '')) ?: Product::MODE_MADE_TO_ORDER;
        $properties['variations'] = array_values(array_filter((array) ($properties['variations'] ?? []), fn ($row) => is_array($row) && array_filter($row, fn ($v) => $v !== null && $v !== '') !== []));
        $properties['is_assorted'] = filter_var($properties['is_assorted'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $properties['max_flavors'] = ($properties['max_flavors'] ?? '') === '' ? null : (int) $properties['max_flavors'];
        $properties['flavor'] = trim((string) ($properties['flavor'] ?? '')) ?: null;
        $properties['flavors'] = array_values(array_filter((array) ($properties['flavors'] ?? []), fn ($row) => is_array($row) && array_filter($row, fn ($v) => $v !== null && $v !== '') !== []));
        $properties['is_featured'] = filter_var($properties['is_featured'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $properties['is_best_seller'] = filter_var($properties['is_best_seller'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $properties['is_new_arrival'] = filter_var($properties['is_new_arrival'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $properties['is_seasonal'] = filter_var($properties['is_seasonal'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $properties['is_limited'] = filter_var($properties['is_limited'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $properties['is_active'] = filter_var($properties['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $properties['seo_title'] = trim((string) ($properties['seo_title'] ?? '')) ?: null;
        $properties['seo_description'] = trim((string) ($properties['seo_description'] ?? '')) ?: null;

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $id = RouteModel::key('product');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('products', 'slug')->ignore($id)->whereNull('deleted_at')],
            'category_id' => ['bail', 'required', 'uuid', Rule::exists('product_categories', 'id')->whereNull('deleted_at')],
            'sku' => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($id)->whereNull('deleted_at')],
            'price' => ['required', 'numeric', 'min:0'],
            'barcode' => ['nullable', 'string', 'max:64'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:100000'],
            'tags' => ['array', 'max:20'],
            'tags.*' => ['string', 'max:50'],
            'image_path' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_image_path' => ['boolean'],
            'gallery' => ['array', 'max:12'],
            'gallery.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'keep_gallery' => ['array'],
            'keep_gallery.*' => ['string', 'max:500'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'sale_ends_at' => ['nullable', 'date'],
            'stock_qty' => ['required', 'integer', 'min:0'],
            'min_stock_qty' => ['required', 'integer', 'min:0'],
            'prep_time_minutes' => ['required', 'integer', 'min:0'],
            'variation_type' => ['required', Rule::in(['none', 'assorted', 'weight', 'pieces', 'size', 'packaging', 'custom'])],
            'variations' => ['array', 'max:30'],
            'variations.*.label' => ['required', 'string', 'max:255'],
            'variations.*.price_modifier' => ['nullable', 'numeric'],
            'is_assorted' => ['boolean'],
            'max_flavors' => ['nullable', 'integer', 'min:1', 'max:24'],
            'flavor' => ['nullable', 'string', 'max:100'],
            'flavors' => ['array', 'max:30'],
            'flavors.*.name' => ['required', 'string', 'max:255'],
            'flavors.*.price_modifier' => ['nullable', 'numeric'],
            'is_featured' => ['boolean'],
            'inventory_mode' => ['required', Rule::in(Product::INVENTORY_MODES)],
            'is_best_seller' => ['boolean'],
            'is_new_arrival' => ['boolean'],
            'is_seasonal' => ['boolean'],
            'is_limited' => ['boolean'],
            'is_active' => ['boolean'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'seo_description' => ['nullable', 'string', 'max:160'],
        ];
    }

    public static function messages(mixed ...$args): array
    {
        return [
            'sale_price.lt' => __('The sale price must be lower than the regular price.'),
        ];
    }

    public static function attributes(mixed ...$args): array
    {
        return [
            'name' => __('name'),
            'slug' => __('url slug'),
            'category_id' => __('category'),
            'sku' => __('sku'),
            'barcode' => __('barcode'),
            'short_description' => __('short description'),
            'description' => __('full description'),
            'tags' => __('tags'),
            'price' => __('price'),
            'sale_price' => __('sale price'),
            'sale_ends_at' => __('sale ends'),
            'stock_qty' => __('in stock'),
            'min_stock_qty' => __('low-stock level'),
            'prep_time_minutes' => __('prep time (minutes)'),
            'variation_type' => __('variation type'),
            'variations' => __('variations'),
            'is_assorted' => __('assorted box'),
            'max_flavors' => __('max flavors per box'),
            'flavor' => __('default flavor'),
            'flavors' => __('flavor choices'),
            'is_featured' => __('featured'),
            'inventory_mode' => __('stock mode'),
            'is_best_seller' => __('best seller (manual)'),
            'is_new_arrival' => __('new arrival (manual)'),
            'is_seasonal' => __('seasonal'),
            'is_limited' => __('limited edition'),
            'is_active' => __('active'),
            'seo_title' => __('seo title'),
            'seo_description' => __('seo description'),
        ];
    }
}
