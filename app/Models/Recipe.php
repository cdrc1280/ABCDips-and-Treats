<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'yield_qty',
        'prep_time_minutes',
        'baking_time_minutes',
        'instructions',
    ];

    protected $casts = [
        'yield_qty'           => 'integer',
        'prep_time_minutes'   => 'integer',
        'baking_time_minutes' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function getCalculatedCostAttribute(): float
    {
        $cost = 0.0;
        foreach ($this->recipeIngredients as $item) {
            if ($item->ingredient) {
                // If recipe ingredient unit matches ingredient unit or converts g -> kg
                $unitCost = (float) $item->ingredient->cost_per_unit;
                $multiplier = match (strtolower($item->unit)) {
                    'g'     => strtolower($item->ingredient->unit) === 'kg' ? 0.001 : 1.0,
                    'ml'    => strtolower($item->ingredient->unit) === 'l'  ? 0.001 : 1.0,
                    default => 1.0,
                };
                $cost += ((float) $item->qty_required * $multiplier) * $unitCost;
            }
        }
        return round($cost, 2);
    }

    public function getUnitCostAttribute(): float
    {
        $yield = max(1, $this->yield_qty);
        return round($this->calculated_cost / $yield, 2);
    }

    public function getGrossMarginPercentageAttribute(): float
    {
        if (! $this->product || $this->product->price <= 0) {
            return 0.0;
        }

        $price = (float) $this->product->price;
        $unitCost = $this->unit_cost;

        $margin = (($price - $unitCost) / $price) * 100;
        return round($margin, 1);
    }
}
