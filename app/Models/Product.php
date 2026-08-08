<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'category_id',
        'sku',
        'barcode',
        'name',
        'slug',
        'short_description',
        'description',
        'image_path',
        'gallery',
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
        'gallery'           => 'array',
        'price'             => 'decimal:2',
        'sale_price'        => 'decimal:2',
        'sale_ends_at'      => 'datetime',
        'prep_time_minutes' => 'integer',
        'stock_qty'         => 'integer',
        'min_stock_qty'     => 'integer',
        'is_featured'       => 'boolean',
        'is_best_seller'    => 'boolean',
        'is_new_arrival'     => 'boolean',
        'is_seasonal'       => 'boolean',
        'is_limited'        => 'boolean',
        'is_active'         => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->sku)) {
                $product->sku = 'SKU-' . strtoupper(Str::random(6));
            }
            if (empty($product->barcode)) {
                $product->barcode = '200' . sprintf('%09d', rand(100000000, 999999999));
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tag');
    }

    public function allergens(): HasMany
    {
        return $this->hasMany(ProductAllergen::class);
    }

    public function nutrition(): HasOne
    {
        return $this->hasOne(ProductNutrition::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function recipe(): HasOne
    {
        return $this->hasOne(Recipe::class);
    }

    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->sale_price ?? $this->price);
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
        if (!empty($this->attributes['is_best_seller'])) {
            return true;
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
        if (!empty($this->attributes['is_new_arrival'])) {
            return true;
        }
        return $this->created_at && $this->created_at->gte(now()->subDays(30));
    }

    public function getPrimaryImageUrlAttribute(): string
    {
        if ($this->image_path) {
            return str_starts_with($this->image_path, 'http')
                ? $this->image_path
                : asset('storage/' . ltrim($this->image_path, '/'));
        }
        $media = $this->getFirstMediaUrl('primary_image');
        return $media ?: asset('images/logo.png');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('primary_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }
}
