<?php

namespace App\Domains\Sales\Models;

use App\Concerns\InteractsWithModelFactory;
use App\Domains\Sales\Data\DeliveryPoolFilterData;
use App\Shared\Concerns\FreesUniqueValuesOnDelete;
use App\Shared\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryPool extends Model
{
    use FreesUniqueValuesOnDelete, HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes;

    /** @var list<string> */
    protected array $uniqueOnDelete = ['pool_code'];

    public const STATUS_OPEN = 'open';

    public const STATUS_BUILDING = 'building';

    public const STATUS_SETTLED = 'settled';

    public const STATUS_DISPATCHED = 'dispatched';

    protected $fillable = [
        'pool_code',
        'zone_name',
        'region',
        'province',
        'city',
        'barangay',
        'total_delivery_fee',
        'shared_fee_per_order',
        'status',
        'settled_at',
        'notes',
    ];

    protected $casts = [
        'total_delivery_fee' => 'decimal:2',
        'shared_fee_per_order' => 'decimal:2',
        'settled_at' => 'datetime',
    ];

    /** @return HasMany<Order, $this> */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * string → global search; array → DeliveryPoolFilterData fields.
     *
     * @param  Builder<DeliveryPool>  $query
     * @return Builder<DeliveryPool>
     */
    public function scopeSearch(Builder $query, mixed $search): Builder
    {
        if (is_string($search)) {
            return $query->where(fn (Builder $q) => $q
                ->whereLike('pool_code', "%{$search}%")
                ->orWhereLike('zone_name', "%{$search}%")
                ->orWhereLike('city', "%{$search}%")
                ->orWhereLike('barangay', "%{$search}%")
                ->orWhereLike('province', "%{$search}%"));
        }

        $filters = DeliveryPoolFilterData::from((array) $search);

        return $query
            ->when($filters->keyword, fn (Builder $q, string $keyword) => $q->search($keyword))
            ->when($filters->status !== null && $filters->status !== '', fn (Builder $q) => $q->where('status', $filters->status));
    }
}
