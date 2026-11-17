<?php

namespace App\Domains\Catalog\Models;

use App\Concerns\InteractsWithModelFactory;
use App\Domains\Catalog\Data\CategoryFilterData;
use App\Shared\Concerns\FreesUniqueValuesOnDelete;
use App\Shared\Concerns\LogsModelActivity;
use App\Shared\Services\PublicUploads;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Guarded(['id'])]
class ProductCategory extends Model implements HasMedia
{
    use FreesUniqueValuesOnDelete, HasUuids, InteractsWithMedia, InteractsWithModelFactory, LogsModelActivity, SoftDeletes;

    /** @var list<string> */
    protected array $uniqueOnDelete = ['slug'];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (ProductCategory $category) {
            if (! $category->sort_order) {
                $category->sort_order = (static::withTrashed()->max('sort_order') ?? 0) + 1;
            }
        });
    }

    /** @return HasMany<Product, $this> */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image_path) {
            return PublicUploads::url($this->image_path);
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

    /**
     * string → global search (name, slug, description); array → CategoryFilterData fields.
     *
     * @param  Builder<ProductCategory>  $query
     * @return Builder<ProductCategory>
     */
    public function scopeSearch(Builder $query, mixed $search): Builder
    {
        if (is_string($search)) {
            return $query->where(fn (Builder $q) => $q
                ->whereLike('name', "%{$search}%")
                ->orWhereLike('slug', "%{$search}%")
                ->orWhereLike('description', "%{$search}%"));
        }

        $filters = CategoryFilterData::from((array) $search);

        return $query
            ->when($filters->keyword, fn (Builder $q, string $keyword) => $q->search($keyword))
            ->when($filters->isActive !== null, fn (Builder $q) => $q->where('is_active', $filters->isActive));
    }
}
