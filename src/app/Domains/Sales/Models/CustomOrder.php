<?php

namespace App\Domains\Sales\Models;

use App\Concerns\InteractsWithModelFactory;
use App\Domains\Sales\Data\CustomOrderFilterData;
use App\Domains\User\Models\User;
use App\Shared\Concerns\FreesUniqueValuesOnDelete;
use App\Shared\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CustomOrder extends Model implements HasMedia
{
    use FreesUniqueValuesOnDelete, HasUuids, InteractsWithMedia, InteractsWithModelFactory, LogsModelActivity, SoftDeletes;

    /** @var list<string> */
    protected array $uniqueOnDelete = ['reference_number'];

    public const STATUS_INQUIRY = 'inquiry';

    public const STATUS_QUOTED = 'quoted';

    public const STATUS_DEPOSIT_PAID = 'deposit_paid';

    public const STATUS_IN_PRODUCTION = 'in_production';

    public const STATUS_READY = 'ready';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

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
        'event_date' => 'date',
        'servings_count' => 'integer',
        'tiers_count' => 'integer',
        'budget_range_min' => 'decimal:2',
        'budget_range_max' => 'decimal:2',
        'quoted_price' => 'decimal:2',
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('reference_photos')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    /**
     * string → global search; array → CustomOrderFilterData fields.
     *
     * @param  Builder<CustomOrder>  $query
     * @return Builder<CustomOrder>
     */
    public function scopeSearch(Builder $query, mixed $search): Builder
    {
        if (is_string($search)) {
            return $query->where(fn (Builder $q) => $q
                ->whereLike('reference_number', "%{$search}%")
                ->orWhereLike('customer_name', "%{$search}%")
                ->orWhereLike('customer_email', "%{$search}%")
                ->orWhereLike('customer_phone', "%{$search}%")
                ->orWhereLike('theme_description', "%{$search}%"));
        }

        $filters = CustomOrderFilterData::from((array) $search);

        return $query
            ->when($filters->keyword, fn (Builder $q, string $keyword) => $q->search($keyword))
            ->when($filters->status !== null && $filters->status !== '', fn (Builder $q) => $q->where('status', $filters->status))
            ->when($filters->from, fn (Builder $q, string $v) => $q->whereDate('event_date', '>=', $v))
            ->when($filters->to, fn (Builder $q, string $v) => $q->whereDate('event_date', '<=', $v));
    }
}
