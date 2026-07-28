<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CustomOrder extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public const STATUS_INQUIRY       = 'inquiry';
    public const STATUS_QUOTED        = 'quoted';
    public const STATUS_DEPOSIT_PAID  = 'deposit_paid';
    public const STATUS_IN_PRODUCTION = 'in_production';
    public const STATUS_READY         = 'ready';
    public const STATUS_COMPLETED     = 'completed';
    public const STATUS_CANCELLED     = 'cancelled';

    protected $fillable = [
        'reference_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'event_date',
        'servings_count',
        'tiers_count',
        'flavor_preference',
        'frosting_type',
        'theme_description',
        'budget_range_min',
        'budget_range_max',
        'quoted_price',
        'staff_notes',
        'status',
    ];

    protected $casts = [
        'event_date'       => 'date',
        'servings_count'   => 'integer',
        'tiers_count'      => 'integer',
        'budget_range_min' => 'decimal:2',
        'budget_range_max' => 'decimal:2',
        'quoted_price'     => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('reference_photos')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }
}
