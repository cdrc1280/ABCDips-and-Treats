<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'ingredient_id',
        'qty_ordered',
        'qty_received',
        'unit_cost',
        'subtotal',
    ];

    protected $casts = [
        'qty_ordered'  => 'decimal:3',
        'qty_received' => 'decimal:3',
        'unit_cost'    => 'decimal:2',
        'subtotal'     => 'decimal:2',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
