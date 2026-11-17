<?php

namespace App\Domains\Review\Models;

use App\Concerns\InteractsWithModelFactory;
use App\Domains\Catalog\Models\Product;
use App\Domains\Review\Data\ReviewFilterData;
use App\Domains\User\Models\User;
use App\Shared\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Review extends Model implements HasMedia
{
    use HasUuids, InteractsWithMedia, InteractsWithModelFactory, LogsModelActivity, SoftDeletes;

    protected $fillable = [
        'product_id',
        'user_id',
        'reviewer_name',
        'reviewer_email',
        'rating',
        'title',
        'comment',
        'is_anonymous',
        'is_verified_buyer',
        'is_approved',
        'is_featured',
        'helpful_votes',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_anonymous' => 'boolean',
        'is_verified_buyer' => 'boolean',
        'is_approved' => 'boolean',
        'is_featured' => 'boolean',
        'helpful_votes' => 'integer',
    ];

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<ReviewVote, $this> */
    public function votes(): HasMany
    {
        return $this->hasMany(ReviewVote::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('review_photos')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    /**
     * string → global search; array → ReviewFilterData fields.
     *
     * @param  Builder<Review>  $query
     * @return Builder<Review>
     */
    public function scopeSearch(Builder $query, mixed $search): Builder
    {
        if (is_string($search)) {
            return $query->where(fn (Builder $q) => $q
                ->whereLike('reviewer_name', "%{$search}%")
                ->orWhereLike('reviewer_email', "%{$search}%")
                ->orWhereLike('title', "%{$search}%")
                ->orWhereLike('comment', "%{$search}%")
                ->orWhereHas('product', fn (Builder $r) => $r->whereLike('name', "%{$search}%")));
        }

        $filters = ReviewFilterData::from((array) $search);

        return $query
            ->when($filters->keyword, fn (Builder $q, string $keyword) => $q->search($keyword))
            ->when($filters->productId !== null, fn (Builder $q) => $q->where('product_id', $filters->productId))
            ->when($filters->rating !== null, fn (Builder $q) => $q->where('rating', $filters->rating))
            ->when($filters->isApproved !== null, fn (Builder $q) => $q->where('is_approved', $filters->isApproved))
            ->when($filters->isFeatured !== null, fn (Builder $q) => $q->where('is_featured', $filters->isFeatured));
    }
}
