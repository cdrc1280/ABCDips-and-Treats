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

    protected $fillable = [
        'order_number',
        'tracking_token',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'fulfillment_type',
        'delivery_address',
        'city',
        'postal_code',
        'scheduled_time',
        'notes',
        'subtotal',
        'discount_amount',
        'coupon_code',
        'delivery_fee',
        'total',
        'payment_method',
        'payment_status',
        'payment_reference',
        'paid_at',
        'status',
    ];

    protected $casts = [
        'subtotal'        => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'delivery_fee'    => 'decimal:2',
        'total'           => 'decimal:2',
        'scheduled_time'  => 'datetime',
        'paid_at'          => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
        $oldStatus = $this->status;
        $this->update(['status' => $newStatus]);

        $this->statusHistories()->create([
            'from_status'        => $oldStatus,
            'to_status'          => $newStatus,
            'comment'            => $comment,
            'changed_by_user_id' => $userId,
        ]);
    }
}
