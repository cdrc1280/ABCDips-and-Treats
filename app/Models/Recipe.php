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
        'overhead_pct',
        'markup_pct',
        'labor_pct',
    ];

    protected $casts = [
        'yield_qty'           => 'integer',
        'prep_time_minutes'   => 'integer',
        'baking_time_minutes' => 'integer',
        'overhead_pct'        => 'decimal:2',
        'markup_pct'          => 'decimal:2',
        'labor_pct'           => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function recipePackagings(): HasMany
    {
        return $this->hasMany(RecipePackaging::class);
    }

    public function getTotalIngredientCostAttribute(): float
    {
        $total = 0.0;
        foreach ($this->recipeIngredients as $item) {
            $total += $item->line_cost;
        }
        return round($total, 4);
    }

    public function getTotalPackagingCostAttribute(): float
    {
        $total = 0.0;
        foreach ($this->recipePackagings as $pkg) {
            $total += $pkg->line_cost;
        }
        return round($total, 4);
    }

    public function getTotalRawMaterialsCostAttribute(): float
    {
        return round($this->total_ingredient_cost + $this->total_packaging_cost, 4);
    }

    public function getOverheadAmountAttribute(): float
    {
        $pct = (float) ($this->overhead_pct ?? 40.0);
        return round($this->total_raw_materials_cost * ($pct / 100), 4);
    }

    public function getTotalCostAttribute(): float
    {
        return round($this->total_raw_materials_cost + $this->overhead_amount, 4);
    }

    public function getMarkupAmountAttribute(): float
    {
        $pct = (float) ($this->markup_pct ?? 66.0);
        return round($this->total_cost * ($pct / 100), 4);
    }

    public function getLaborAmountAttribute(): float
    {
        $pct = (float) ($this->labor_pct ?? 60.0);
        return round($this->total_raw_materials_cost * ($pct / 100), 4);
    }

    public function getBatchSellingPriceAttribute(): float
    {
        return round($this->total_cost + $this->markup_amount + $this->labor_amount, 4);
    }

    public function getUnitSellingPriceAttribute(): float
    {
        $yield = max(1, (int) $this->yield_qty);
        return round($this->batch_selling_price / $yield, 4);
    }

    public function getCalculatedCostAttribute(): float
    {
        return round($this->total_cost, 2);
    }

    public function getUnitCostAttribute(): float
    {
        $yield = max(1, $this->yield_qty);
        return round($this->total_cost / $yield, 2);
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
