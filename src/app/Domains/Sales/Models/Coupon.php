<?php

namespace App\Domains\Sales\Models;

use App\Concerns\InteractsWithModelFactory;
use App\Domains\Sales\Data\CouponFilterData;
use App\Shared\Concerns\FreesUniqueValuesOnDelete;
use App\Shared\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use FreesUniqueValuesOnDelete, HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes;

    public const TYPE_FIXED = 'fixed';

    /** Percentage off (`value` = percent), capped by max_discount. The back-office form saves this. */
    public const TYPE_PERCENT = 'percent';

    /** @var list<string> */
    protected array $uniqueOnDelete = ['code'];

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_spend',
        'max_discount',
        'usage_limit',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_spend' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'used_count' => 'integer',
        'usage_limit' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isValid(float $cartSubtotal): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->starts_at && now()->lt($this->starts_at)) {
            return false;
        }

        if ($this->expires_at && now()->gt($this->expires_at)) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        if ($cartSubtotal < $this->min_spend) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $cartSubtotal): float
    {
        if (! $this->isValid($cartSubtotal)) {
            return 0.0;
        }

        if ($this->type === self::TYPE_PERCENT) {
            $discount = ($cartSubtotal * $this->value) / 100;
            if ($this->max_discount !== null && $this->max_discount > 0) {
                $discount = min($discount, (float) $this->max_discount);
            }

            return round($discount, 2);
        }

        // Fixed
        return min((float) $this->value, $cartSubtotal);
    }

    /**
     * string → global search; array → CouponFilterData fields.
     *
     * @param  Builder<Coupon>  $query
     * @return Builder<Coupon>
     */
    public function scopeSearch(Builder $query, mixed $search): Builder
    {
        if (is_string($search)) {
            return $query->where(fn (Builder $q) => $q
                ->whereLike('code', "%{$search}%"));
        }

        $filters = CouponFilterData::from((array) $search);

        return $query
            ->when($filters->keyword, fn (Builder $q, string $keyword) => $q->search($keyword))
            ->when($filters->type !== null && $filters->type !== '', fn (Builder $q) => $q->where('type', $filters->type))
            ->when($filters->isActive !== null, fn (Builder $q) => $q->where('is_active', $filters->isActive));
    }
}
