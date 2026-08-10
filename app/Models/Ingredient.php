<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingredient extends Model
{
    use HasFactory;

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
        'item_unit'     => 'decimal:3',
        'item_price'    => 'decimal:2',
        'cost_per_unit' => 'decimal:4',
        'stock_qty'     => 'decimal:3',
        'min_stock_qty' => 'decimal:3',
        'reorder_qty'   => 'decimal:3',
        'supplier_id'   => 'integer',
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

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

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
}
