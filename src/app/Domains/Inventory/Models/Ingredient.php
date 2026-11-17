<?php

namespace App\Domains\Inventory\Models;

use App\Concerns\InteractsWithModelFactory;
use App\Domains\Costing\Models\RecipeIngredient;
use App\Domains\Inventory\Data\IngredientFilterData;
use App\Shared\Concerns\FreesUniqueValuesOnDelete;
use App\Shared\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ingredient extends Model
{
    use FreesUniqueValuesOnDelete, HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes;

    /** @var list<string> */
    protected array $uniqueOnDelete = ['sku'];

    protected $fillable = [
        'sku',
        'name',
        'unit',
        'item_unit',
        'item_price',
        'cost_per_unit',
        'stock_qty',
        'min_stock_qty',
        'reorder_qty',
        'supplier_id',
        'supplier_name',
    ];

    protected $casts = [
        'item_unit' => 'decimal:3',
        'item_price' => 'decimal:2',
        'cost_per_unit' => 'decimal:4',
        'stock_qty' => 'decimal:3',
        'min_stock_qty' => 'decimal:3',
        'reorder_qty' => 'decimal:3',
        'supplier_id' => 'string',
    ];

    protected static function booted(): void
    {
        static::saving(function (Ingredient $ingredient) {
            $itemUnit = (float) ($ingredient->item_unit ?? 1);
            $itemPrice = (float) ($ingredient->item_price ?? 0);
            if ($itemUnit > 0 && $itemPrice > 0) {
                $ingredient->cost_per_unit = round($itemPrice / $itemUnit, 4);
            }

            if ($ingredient->supplier_id && $ingredient->supplier) {
                $ingredient->supplier_name = $ingredient->supplier->name;
            }
        });
    }

    /** @return BelongsTo<Supplier, $this> */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /** @return HasMany<StockMovement, $this> */
    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /** @return HasMany<RecipeIngredient, $this> */
    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock_qty <= $this->min_stock_qty;
    }

    public function getPriceUnitAttribute(): float
    {
        return (float) $this->cost_per_unit;
    }

    /**
     * string → global search; array → IngredientFilterData fields.
     *
     * @param  Builder<Ingredient>  $query
     * @return Builder<Ingredient>
     */
    public function scopeSearch(Builder $query, mixed $search): Builder
    {
        if (is_string($search)) {
            return $query->where(fn (Builder $q) => $q
                ->whereLike('name', "%{$search}%")
                ->orWhereLike('sku', "%{$search}%")
                ->orWhereHas('supplier', fn (Builder $r) => $r->whereLike('name', "%{$search}%")));
        }

        $filters = IngredientFilterData::from((array) $search);

        return $query
            ->when($filters->keyword, fn (Builder $q, string $keyword) => $q->search($keyword))
            ->when($filters->supplierId !== null, fn (Builder $q) => $q->where('supplier_id', $filters->supplierId))
            ->when($filters->stock !== null && $filters->stock !== '', fn (Builder $q) => $filters->stock === 'low' ? $q->whereColumn('stock_qty', '<=', 'min_stock_qty') : $q->whereColumn('stock_qty', '>', 'min_stock_qty'));
    }
}
