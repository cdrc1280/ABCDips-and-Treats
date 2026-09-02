<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationItem extends Model
{
    use HasFactory;

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
        'qty'        => 'decimal:3',
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
    ];

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
