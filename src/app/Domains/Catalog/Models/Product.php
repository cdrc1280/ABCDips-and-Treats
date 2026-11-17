<?php

namespace App\Domains\Catalog\Models;

use App\Concerns\InteractsWithModelFactory;
use App\Domains\Catalog\Data\ProductFilterData;
use App\Domains\Costing\Models\Recipe;
use App\Domains\Review\Models\Review;
use App\Domains\Sales\Models\OrderItem;
use App\Shared\Concerns\FreesUniqueValuesOnDelete;
use App\Shared\Concerns\LogsModelActivity;
use App\Shared\Services\PublicUploads;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use FreesUniqueValuesOnDelete, HasUuids, InteractsWithMedia, InteractsWithModelFactory, LogsModelActivity, SoftDeletes;

    /** Selling consumes the recipe's ingredients (no production batches). */
    public const MODE_MADE_TO_ORDER = 'made_to_order';

    /** A completed production batch consumes the ingredients; selling only uses finished stock. */
    public const MODE_BAKED_AHEAD = 'baked_ahead';

    public const INVENTORY_MODES = [self::MODE_MADE_TO_ORDER, self::MODE_BAKED_AHEAD];

    public function isBakedAhead(): bool
    {
        return $this->inventory_mode === self::MODE_BAKED_AHEAD;
    }

    /** @var list<string> */
    protected array $uniqueOnDelete = ['slug', 'sku'];

    /** Products with a 3D model on their storefront page ("Turn the loaf"). One today: the signature loaf. */
    public const SLUGS_WITH_3D_MODEL = ['classic-banana-bread-loaf'];

    protected $fillable = [
        'category_id',
        'sku',
        'barcode',
        'name',
        'slug',
        'short_description',
        'flavor',
        'flavors',
        'is_assorted',
        'max_flavors',
        'description',
        'image_path',
        'gallery',
        'variation_type',
        'inventory_mode',
        'variations',
        'price',
        'sale_price',
        'sale_ends_at',
        'prep_time_minutes',
        'stock_qty',
        'min_stock_qty',
        'is_featured',
        'is_best_seller',
        'is_new_arrival',
        'is_seasonal',
        'is_limited',
        'is_active',
        'seo_title',
        'seo_description',
    ];

    protected $casts = [
        'gallery' => 'array',
        'variations' => 'array',
        'flavors' => 'array',
        'is_assorted' => 'boolean',
        'max_flavors' => 'integer',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'sale_ends_at' => 'datetime',
        'prep_time_minutes' => 'integer',
        'stock_qty' => 'integer',
        'min_stock_qty' => 'integer',
        'is_featured' => 'boolean',
        'is_best_seller' => 'boolean',
        'is_new_arrival' => 'boolean',
        'is_seasonal' => 'boolean',
        'is_limited' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->sku)) {
                $product->sku = 'SKU-'.strtoupper(Str::random(6));
            }
            if (empty($product->barcode)) {
                $product->barcode = '200'.sprintf('%09d', rand(100000000, 999999999));
            }
        });
    }

    /** @return BelongsTo<ProductCategory, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /** @return BelongsToMany<Tag, $this> */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tag');
    }

    /** @return HasMany<ProductAllergen, $this> */
    public function allergens(): HasMany
    {
        return $this->hasMany(ProductAllergen::class);
    }

    /** @return HasOne<ProductNutrition, $this> */
    public function nutrition(): HasOne
    {
        return $this->hasOne(ProductNutrition::class);
    }

    /** @return HasMany<Review, $this> */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /** @return HasMany<OrderItem, $this> */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** @return HasOne<Recipe, $this> */
    public function recipe(): HasOne
    {
        return $this->hasOne(Recipe::class);
    }

    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->is_on_sale ? $this->sale_price : $this->price);
    }

    public function getIsOnSaleAttribute(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->price && ($this->sale_ends_at === null || $this->sale_ends_at->isFuture());
    }

    /**
     * Best seller status: Admin force override OR high sales volume
     */
    public function getIsBestSellerAttribute(): bool
    {
        if (! empty($this->attributes['is_best_seller'])) {
            return true;
        }
        if (array_key_exists('order_items_sum_qty', $this->attributes)) {
            return (int) $this->attributes['order_items_sum_qty'] > 0;
        }
        if ($this->relationLoaded('orderItems')) {
            return $this->orderItems->sum('qty') > 0;
        }

        return (int) $this->orderItems()->sum('qty') > 0;
    }

    /**
     * New arrival status: Admin force override OR created within the last 30 days (1 month auto-expiry)
     */
    public function getIsNewArrivalAttribute(): bool
    {
        if (! empty($this->attributes['is_new_arrival'])) {
            return true;
        }

        return $this->created_at && $this->created_at->gte(now()->subDays(30));
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeForCustomer(Builder $query): Builder
    {
        // Sellable = active, priced, and in an active (not deleted) category, so switching a
        // category off hides its products from the shop, collections and product pages too.
        return $query->where('is_active', true)
            ->whereNotNull('price')
            ->where('price', '>', 0)
            ->whereHas('category', fn (Builder $category) => $category->where('is_active', true));
    }

    /**
     * Preload the aggregates ProductResource and is_best_seller need, in the same query,
     * so product listings don't run extra queries per product.
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeWithListingAggregates(Builder $query): Builder
    {
        return $query
            ->withCount(['reviews as approved_reviews_count' => fn ($q) => $q->where('is_approved', true)])
            ->withAvg(['reviews as approved_reviews_avg_rating' => fn ($q) => $q->where('is_approved', true)], 'rating')
            ->withSum('orderItems', 'qty');
    }

    /**
     * Resolve a stored image path: absolute URLs pass through, `images/...` points at a photo
     * bundled in public/, anything else is an upload on the public disk.
     */
    private static function resolveImagePath(string $path): string
    {
        return (string) PublicUploads::url($path);
    }

    /** The product's real photo, or null when it has none (the storefront draws its own fallback). */
    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->image_path) {
            return self::resolveImagePath($this->image_path);
        }

        return $this->getFirstMediaUrl('primary_image') ?: null;
    }

    public function getPrimaryImageUrlAttribute(): string
    {
        return $this->photo_url ?? asset('images/logo.png');
    }

    public function getGalleryImageUrlsAttribute(): array
    {
        $urls = [];
        if (is_array($this->gallery)) {
            foreach ($this->gallery as $path) {
                if ($path) {
                    $urls[] = self::resolveImagePath($path);
                }
            }
        }
        $spatieUrls = $this->getMedia('gallery')->map(fn ($m) => $m->getUrl())->toArray();

        return array_values(array_unique(array_merge($urls, $spatieUrls)));
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('primary_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    /**
     * string → global search; array → ProductFilterData fields.
     *
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeSearch(Builder $query, mixed $search): Builder
    {
        if (is_string($search)) {
            return $query->where(fn (Builder $q) => $q
                ->whereLike('name', "%{$search}%")
                ->orWhereLike('sku', "%{$search}%")
                ->orWhereLike('barcode', "%{$search}%")
                ->orWhereLike('slug', "%{$search}%")
                ->orWhereHas('category', fn (Builder $r) => $r->whereLike('name', "%{$search}%")));
        }

        $filters = ProductFilterData::from((array) $search);

        return $query
            ->when($filters->keyword, fn (Builder $q, string $keyword) => $q->search($keyword))
            ->when($filters->categoryId !== null, fn (Builder $q) => $q->where('category_id', $filters->categoryId))
            ->when($filters->isActive !== null, fn (Builder $q) => $q->where('is_active', $filters->isActive))
            ->when($filters->isFeatured !== null, fn (Builder $q) => $q->where('is_featured', $filters->isFeatured))
            ->when($filters->stock !== null && $filters->stock !== '', fn (Builder $q) => $filters->stock === 'low' ? $q->whereColumn('stock_qty', '<=', 'min_stock_qty') : $q->whereColumn('stock_qty', '>', 'min_stock_qty'));
    }
}
