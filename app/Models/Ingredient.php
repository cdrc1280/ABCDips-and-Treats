<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'unit',
        'cost_per_unit',
        'stock_qty',
        'min_stock_qty',
        'reorder_qty',
        'supplier_name',
    ];

    protected $casts = [
        'cost_per_unit' => 'decimal:2',
        'stock_qty'     => 'decimal:3',
        'min_stock_qty' => 'decimal:3',
        'reorder_qty'   => 'decimal:3',
    ];

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
}
