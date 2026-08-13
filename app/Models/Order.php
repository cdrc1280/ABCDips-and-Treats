<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    // Status Pipeline Constants per §4
    public const STATUS_PENDING          = 'pending';
    public const STATUS_CONFIRMED        = 'confirmed';
    public const STATUS_PREPARING        = 'preparing';
    public const STATUS_PACKAGING        = 'packaging';
    public const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    public const STATUS_READY_FOR_PICKUP = 'ready_for_pickup';
    public const STATUS_COMPLETED        = 'completed';
    public const STATUS_CANCELLED        = 'cancelled';
    public const STATUS_REFUNDED         = 'refunded';
    public const STATUS_ARCHIVED         = 'archived';

    // Delivery Mode Constants
    public const MODE_PRIORITY = 'priority';
    public const MODE_POOLING  = 'pooling';

    // Pooling Status Constants
    public const POOLING_NOT_POOLED           = 'not_pooled';
    public const POOLING_AWAITING_ASSIGNMENT  = 'awaiting_assignment';
    public const POOLING_POOLED               = 'pooled';
    public const POOLING_SETTLED              = 'settled';

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
        'subtotal'             => 'decimal:2',
        'discount_amount'      => 'decimal:2',
        'delivery_fee'         => 'decimal:2',
        'estimated_shared_fee' => 'decimal:2',
        'total'                => 'decimal:2',
        'scheduled_time'       => 'datetime',
        'paid_at'               => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deliveryPool(): BelongsTo
    {
        return $this->belongsTo(DeliveryPool::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function transitionTo(string $newStatus, ?string $comment = null, ?int $userId = null): void
    {
        if ($this->delivery_mode === self::MODE_POOLING) {
            if ($this->pooling_status !== self::POOLING_SETTLED && in_array($newStatus, [self::STATUS_CONFIRMED, self::STATUS_PREPARING, self::STATUS_PACKAGING, self::STATUS_OUT_FOR_DELIVERY, self::STATUS_COMPLETED])) {
                throw new \DomainException("Order #{$this->order_number} is a Delivery Pooling order awaiting admin assignment. Please assign to a Delivery Pool Batch and Settle the shared shipping fee in 'Delivery Pooling' before advancing status.");
            }

            if ($this->payment_status !== 'paid' && in_array($newStatus, [self::STATUS_PREPARING, self::STATUS_PACKAGING, self::STATUS_OUT_FOR_DELIVERY, self::STATUS_COMPLETED])) {
                throw new \DomainException("Order #{$this->order_number} cannot proceed to {$newStatus}: Customer has not settled payment for Group Delivery Pooling yet.");
            }
        }

        $oldStatus = $this->status;
        $this->update(['status' => $newStatus]);

        $this->statusHistories()->create([
            'from_status'        => $oldStatus,
            'to_status'          => $newStatus,
            'comment'            => $comment,
            'changed_by_user_id' => $userId,
        ]);

        if (in_array($newStatus, [self::STATUS_CANCELLED, self::STATUS_REFUNDED]) && ! in_array($oldStatus, [self::STATUS_CANCELLED, self::STATUS_REFUNDED])) {
            app(\App\Services\InventoryDeductionService::class)->revertForOrder($this);
        } elseif (! in_array($newStatus, [self::STATUS_CANCELLED, self::STATUS_REFUNDED]) && in_array($oldStatus, [self::STATUS_CANCELLED, self::STATUS_REFUNDED])) {
            app(\App\Services\InventoryDeductionService::class)->deductForOrder($this);
        }
    }
}
