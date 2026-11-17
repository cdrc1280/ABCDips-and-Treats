<?php

namespace App\Domains\Costing\Models;

use App\Concerns\InteractsWithModelFactory;
use App\Domains\Catalog\Models\Product;
use App\Domains\Costing\Data\RecipeFilterData;
use App\Domains\Production\Models\ProductionBatch;
use App\Shared\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipe extends Model
{
    use HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes;

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
        'yield_qty' => 'integer',
        'prep_time_minutes' => 'integer',
        'baking_time_minutes' => 'integer',
        'overhead_pct' => 'decimal:2',
        'markup_pct' => 'decimal:2',
        'labor_pct' => 'decimal:2',
    ];

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** @return HasMany<ProductionBatch, $this> */
    public function productionBatches(): HasMany
    {
        return $this->hasMany(ProductionBatch::class);
    }

    /** @return HasMany<RecipeIngredient, $this> */
    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    /** @return HasMany<RecipePackaging, $this> */
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

    /**
     * string → global search; array → RecipeFilterData fields.
     *
     * @param  Builder<Recipe>  $query
     * @return Builder<Recipe>
     */
    public function scopeSearch(Builder $query, mixed $search): Builder
    {
        if (is_string($search)) {
            return $query->where(fn (Builder $q) => $q
                ->whereLike('name', "%{$search}%")
                ->orWhereHas('product', fn (Builder $r) => $r->whereLike('name', "%{$search}%")));
        }

        $filters = RecipeFilterData::from((array) $search);

        return $query
            ->when($filters->keyword, fn (Builder $q, string $keyword) => $q->search($keyword))
            ->when($filters->productId !== null, fn (Builder $q) => $q->where('product_id', $filters->productId));
    }
}
