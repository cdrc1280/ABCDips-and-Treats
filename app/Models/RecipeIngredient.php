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
        $recipeUnit  = strtolower(trim($this->unit));
        $stockUnit   = strtolower(trim($this->ingredient->unit));

        $multiplier = match ($recipeUnit) {
            'g', 'gram', 'grams'       => in_array($stockUnit, ['kg', 'kilogram', 'kilograms']) ? 0.001 : 1.0,
            'ml', 'milliliter'         => in_array($stockUnit, ['l', 'liter', 'liters']) ? 0.001 : 1.0,
            'tsp', 'teaspoon'          => in_array($stockUnit, ['kg', 'l']) ? 0.005 : (in_array($stockUnit, ['g', 'ml']) ? 5.0 : 1.0),
            'tbsp', 'tablespoon'       => in_array($stockUnit, ['kg', 'l']) ? 0.015 : (in_array($stockUnit, ['g', 'ml']) ? 15.0 : 1.0),
            'cup', 'cups'              => in_array($stockUnit, ['kg', 'l']) ? 0.240 : (in_array($stockUnit, ['g', 'ml']) ? 240.0 : 1.0),
            default                    => 1.0,
        };

        return round(($qtyRequired * $multiplier) * $costPerUnit, 4);
    }
}
