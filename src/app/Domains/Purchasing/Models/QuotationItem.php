<?php

namespace App\Domains\Purchasing\Models;

use App\Concerns\InteractsWithModelFactory;
use App\Domains\Inventory\Models\Ingredient;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationItem extends Model
{
    use HasUuids, InteractsWithModelFactory;

    protected $fillable = [
        'quotation_id',
        'ingredient_id',
        'item_description',
        'qty',
        'unit',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'qty' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /** @return BelongsTo<Quotation, $this> */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    /** @return BelongsTo<Ingredient, $this> */
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
