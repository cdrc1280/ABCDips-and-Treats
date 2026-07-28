<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

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
        'value'        => 'decimal:2',
        'min_spend'    => 'decimal:2',
        'max_discount' => 'decimal:2',
        'used_count'   => 'integer',
        'usage_limit'  => 'integer',
        'starts_at'    => 'datetime',
        'expires_at'   => 'datetime',
        'is_active'    => 'boolean',
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

        if ($this->type === 'percentage') {
            $discount = ($cartSubtotal * $this->value) / 100;
            if ($this->max_discount !== null && $this->max_discount > 0) {
                $discount = min($discount, (float) $this->max_discount);
            }
            return round($discount, 2);
        }

        // Fixed
        return min((float) $this->value, $cartSubtotal);
    }
}
