<?php

namespace App\Domains\Sales\Models;

use App\Concerns\InteractsWithModelFactory;
use App\Domains\Sales\Data\OrderFilterData;
use App\Domains\User\Models\User;
use App\Services\InventoryDeductionService;
use App\Shared\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes;

    // Status Pipeline Constants per §4
    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_PREPARING = 'preparing';

    public const STATUS_PACKAGING = 'packaging';

    public const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';

    public const STATUS_READY_FOR_PICKUP = 'ready_for_pickup';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_REFUNDED = 'refunded';

    public const STATUS_ARCHIVED = 'archived';

    // Delivery Mode Constants
    public const MODE_PRIORITY = 'priority';

    public const MODE_POOLING = 'pooling';

    /** POS walk-in sales have no customer number; 11 digits so the order stays editable (App\Rules\PhilippinePhone). */
    public const WALK_IN_PHONE = '09000000000';

    // Pooling Status Constants
    public const POOLING_NOT_POOLED = 'not_pooled';

    public const POOLING_AWAITING_ASSIGNMENT = 'awaiting_assignment';

    public const POOLING_POOLED = 'pooled';

    public const POOLING_SETTLED = 'settled';

    protected $fillable = [
        'order_number',
        'tracking_token',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'fulfillment_type',
        'delivery_mode',
        'delivery_pool_id',
        'pooling_status',
        'delivery_address',
        'region',
        'province',
        'city',
        'barangay',
        'street_address',
        'postal_code',
        'scheduled_time',
        'notes',
        'subtotal',
        'discount_amount',
        'coupon_code',
        'delivery_fee',
        'estimated_shared_fee',
        'total',
        'payment_method',
        'payment_status',
        'payment_reference',
        'paid_at',
        'status',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'estimated_shared_fee' => 'decimal:2',
        'total' => 'decimal:2',
        'scheduled_time' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<DeliveryPool, $this> */
    public function deliveryPool(): BelongsTo
    {
        return $this->belongsTo(DeliveryPool::class);
    }

    /** @return HasMany<OrderItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** @return HasMany<OrderStatusHistory, $this> */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    /** A cancelled or refunded order gives its coupon use back. */
    private function releaseCoupon(): void
    {
        if ($this->coupon_code) {
            Coupon::query()->where('code', $this->coupon_code)->where('used_count', '>', 0)->decrement('used_count');
        }
    }

    /** A reopened order takes its coupon use again. */
    private function claimCoupon(): void
    {
        if ($this->coupon_code) {
            Coupon::query()->where('code', $this->coupon_code)->increment('used_count');
        }
    }

    /** Fulfilment pipeline, in order. A status may move forward to any later stage. */
    public const PIPELINE = [self::STATUS_PENDING, self::STATUS_CONFIRMED, self::STATUS_PREPARING, self::STATUS_PACKAGING, self::STATUS_OUT_FOR_DELIVERY, self::STATUS_READY_FOR_PICKUP, self::STATUS_COMPLETED];

    /** E-wallet payments are confirmed online, so these orders aren't prepared before they're paid. */
    public const ONLINE_PAYMENT_METHODS = ['gcash', 'maya', 'qrph'];

    /** Stages that need goods to be made or handed over. */
    private const WORK_STAGES = [self::STATUS_PREPARING, self::STATUS_PACKAGING, self::STATUS_OUT_FOR_DELIVERY, self::STATUS_READY_FOR_PICKUP, self::STATUS_COMPLETED];

    /**
     * Statuses this order may move to next (used by the back office to offer only valid choices).
     *
     * @return list<string>
     */
    public function allowedTransitions(): array
    {
        $status = (string) $this->status;
        $handover = $this->fulfillment_type === 'pickup' ? self::STATUS_READY_FOR_PICKUP : self::STATUS_OUT_FOR_DELIVERY;

        return match (true) {
            $status === self::STATUS_COMPLETED => [self::STATUS_REFUNDED, self::STATUS_ARCHIVED],
            $status === self::STATUS_CANCELLED => [self::STATUS_PENDING, self::STATUS_ARCHIVED],
            $status === self::STATUS_REFUNDED => [self::STATUS_ARCHIVED],
            $status === self::STATUS_ARCHIVED => [],
            default => [
                ...array_values(array_filter(
                    array_slice(self::PIPELINE, (int) array_search($status, self::PIPELINE, true) + 1),
                    fn (string $next) => ! in_array($next, [self::STATUS_OUT_FOR_DELIVERY, self::STATUS_READY_FOR_PICKUP], true) || $next === $handover,
                )),
                self::STATUS_CANCELLED,
            ],
        };
    }

    /**
     * Move the order to a new status: validates the move, locks the row (so concurrent changes can't
     * both restock), records history and keeps ingredient stock in step with cancel/reopen.
     *
     * @throws \DomainException when the move isn't allowed
     */
    public function transitionTo(string $newStatus, ?string $comment = null, ?string $userId = null): void
    {
        DB::transaction(function () use ($newStatus, $comment, $userId) {
            $locked = static::query()->whereKey($this->getKey())->lockForUpdate()->firstOrFail();
            $this->setRawAttributes($locked->getAttributes(), true);
            $oldStatus = (string) $this->status;

            if ($newStatus === $oldStatus) {
                return;
            }
            if (! in_array($newStatus, $this->allowedTransitions(), true)) {
                throw new \DomainException(__('Order #:number can\'t go from :from to :to.', [
                    'number' => $this->order_number, 'from' => str_replace('_', ' ', $oldStatus), 'to' => str_replace('_', ' ', $newStatus),
                ]));
            }

            if ($this->delivery_mode === self::MODE_POOLING) {
                if ($this->pooling_status !== self::POOLING_SETTLED && in_array($newStatus, [self::STATUS_CONFIRMED, ...self::WORK_STAGES], true)) {
                    throw new \DomainException("Order #{$this->order_number} is a Delivery Pooling order awaiting admin assignment. Please assign to a Delivery Pool Batch and Settle the shared shipping fee in 'Delivery Pooling' before advancing status.");
                }
                if ($this->payment_status !== 'paid' && in_array($newStatus, self::WORK_STAGES, true)) {
                    throw new \DomainException("Order #{$this->order_number} cannot proceed to {$newStatus}: Customer has not settled payment for Group Delivery Pooling yet.");
                }
            } elseif (in_array($this->payment_method, self::ONLINE_PAYMENT_METHODS, true) && $this->payment_status !== 'paid' && in_array($newStatus, self::WORK_STAGES, true)) {
                throw new \DomainException(__('Order #:number is waiting for its :method payment; it can be prepared once it is paid.', [
                    'number' => $this->order_number, 'method' => strtoupper((string) $this->payment_method),
                ]));
            }

            $this->update(['status' => $newStatus]);

            $this->statusHistories()->create([
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'comment' => $comment,
                'changed_by_user_id' => $userId,
            ]);

            $wasClosed = in_array($oldStatus, [self::STATUS_CANCELLED, self::STATUS_REFUNDED], true);
            $isClosed = in_array($newStatus, [self::STATUS_CANCELLED, self::STATUS_REFUNDED], true);
            if ($isClosed && ! $wasClosed) {
                app(InventoryDeductionService::class)->revertForOrder($this);
                $this->releaseCoupon();
            } elseif (! $isClosed && $wasClosed && $newStatus !== self::STATUS_ARCHIVED) {
                app(InventoryDeductionService::class)->deductForOrder($this);
                $this->claimCoupon();
            }
        });
    }

    /**
     * string → global search; array → OrderFilterData fields.
     *
     * @param  Builder<Order>  $query
     * @return Builder<Order>
     */
    public function scopeSearch(Builder $query, mixed $search): Builder
    {
        if (is_string($search)) {
            return $query->where(fn (Builder $q) => $q
                ->whereLike('order_number', "%{$search}%")
                ->orWhereLike('customer_name', "%{$search}%")
                ->orWhereLike('customer_email', "%{$search}%")
                ->orWhereLike('customer_phone', "%{$search}%")
                ->orWhereLike('payment_reference', "%{$search}%"));
        }

        $filters = OrderFilterData::from((array) $search);

        return $query
            ->when($filters->keyword, fn (Builder $q, string $keyword) => $q->search($keyword))
            ->when($filters->status !== null && $filters->status !== '', fn (Builder $q) => $q->where('status', $filters->status))
            ->when($filters->paymentStatus !== null && $filters->paymentStatus !== '', fn (Builder $q) => $q->where('payment_status', $filters->paymentStatus))
            ->when($filters->fulfillmentType !== null && $filters->fulfillmentType !== '', fn (Builder $q) => $q->where('fulfillment_type', $filters->fulfillmentType))
            ->when($filters->from, fn (Builder $q, string $v) => $q->whereDate('created_at', '>=', $v))
            ->when($filters->to, fn (Builder $q, string $v) => $q->whereDate('created_at', '<=', $v));
    }
}
