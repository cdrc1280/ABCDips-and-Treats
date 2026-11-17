<?php

namespace App\Domains\Purchasing\Models;

use App\Concerns\InteractsWithModelFactory;
use App\Domains\Inventory\Models\Ingredient;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    use HasUuids, InteractsWithModelFactory;

    protected $fillable = [
        'purchase_order_id',
        'ingredient_id',
        'qty_ordered',
        'qty_received',
        'unit_cost',
        'subtotal',
    ];

    protected $casts = [
        'qty_ordered' => 'decimal:3',
        'qty_received' => 'decimal:3',
        'unit_cost' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /** @return BelongsTo<PurchaseOrder, $this> */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /** @return BelongsTo<Ingredient, $this> */
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
