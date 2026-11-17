<?php

namespace App\Domains\Costing\Models;

use App\Concerns\InteractsWithModelFactory;
use App\Domains\Inventory\Models\Ingredient;
use App\Services\UnitConverterService;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeIngredient extends Model
{
    use HasUuids, InteractsWithModelFactory;

    protected $fillable = [
        'recipe_id',
        'ingredient_id',
        'qty_required',
        'unit',
    ];

    protected $casts = [
        'qty_required' => 'decimal:3',
    ];

    /** @return BelongsTo<Recipe, $this> */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    /** @return BelongsTo<Ingredient, $this> */
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
        $convertedQty = UnitConverterService::convert($qtyRequired, $this->unit, $this->ingredient->unit);

        return round($convertedQty * $costPerUnit, 4);
    }
}
