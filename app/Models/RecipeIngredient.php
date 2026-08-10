<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeIngredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipe_id',
        'ingredient_id',
        'qty_required',
        'unit',
    ];

    protected $casts = [
        'qty_required' => 'decimal:3',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function getLineCostAttribute(): float
    {
        if (! $this->ingredient) {
            return 0.0;
        }

        $costPerUnit = (float) $this->ingredient->cost_per_unit;
        $qtyRequired = (float) $this->qty_required;
        $convertedQty = \App\Services\UnitConverterService::convert($qtyRequired, $this->unit, $this->ingredient->unit);

        return round($convertedQty * $costPerUnit, 4);
    }
}
