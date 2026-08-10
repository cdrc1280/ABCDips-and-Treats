<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProductCategory extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_path',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (ProductCategory $category) {
            if (is_null($category->sort_order) || $category->sort_order === 0) {
                $category->sort_order = (static::max('sort_order') ?? 0) + 1;
            }
        });
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image_path) {
            return str_starts_with($this->image_path, 'http')
                ? $this->image_path
                : asset('storage/' . ltrim($this->image_path, '/'));
        }
        $mediaUrl = $this->getFirstMediaUrl('category_image');
        return $mediaUrl ?: null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('category_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }
}
