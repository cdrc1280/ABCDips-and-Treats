<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Review extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

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
        'rating'            => 'integer',
        'is_anonymous'      => 'boolean',
        'is_verified_buyer' => 'boolean',
        'is_approved'       => 'boolean',
        'is_featured'       => 'boolean',
        'helpful_votes'     => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ReviewVote::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('review_photos')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }
}
