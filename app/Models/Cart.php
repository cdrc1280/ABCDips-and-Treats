<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'user_id',
        'coupon_code',
        'discount_amount',
        'fee_amount',
        'last_active_at',
        'expires_at',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'fee_amount'      => 'decimal:2',
        'last_active_at'  => 'datetime',
        'expires_at'       => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function getSubtotalAttribute(): float
    {
        return round($this->items->sum(fn (CartItem $item) => $item->subtotal), 2);
    }

    public function getTotalAttribute(): float
    {
        $sub = $this->subtotal;
        $disc = (float) $this->discount_amount;
        $fee = (float) $this->fee_amount;

        return max(0.0, round($sub - $disc + $fee, 2));
    }

    public function getIsExpiredAttribute(): bool
    {
        return now()->gt($this->expires_at);
    }
}
