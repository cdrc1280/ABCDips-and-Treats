<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryPool extends Model
{
    use HasFactory;

    public const STATUS_OPEN       = 'open';
    public const STATUS_BUILDING   = 'building';
    public const STATUS_SETTLED    = 'settled';
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
        'total_delivery_fee'   => 'decimal:2',
        'shared_fee_per_order' => 'decimal:2',
        'settled_at'           => 'datetime',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
