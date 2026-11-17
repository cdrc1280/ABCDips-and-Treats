<?php

namespace App\Domains\Costing\Models;

use App\Concerns\InteractsWithModelFactory;
use App\Domains\Inventory\Models\Ingredient;
use App\Domains\Inventory\Models\PackagingMaterial;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CostingItem extends Model
{
    use HasUuids, InteractsWithModelFactory;

    protected $fillable = [
        'product_costing_id',
        'group',
        'ingredient_id',
        'packaging_material_id',
        'name',
        'unit',
        'package_amount',
        'package_price',
        'qty_used',
    ];

    protected $casts = [
        'package_amount' => 'decimal:3',
        'package_price' => 'decimal:2',
        'qty_used' => 'decimal:3',
    ];

    /** @return BelongsTo<ProductCosting, $this> */
    public function productCosting(): BelongsTo
    {
        return $this->belongsTo(ProductCosting::class);
    }

    /** @return BelongsTo<Ingredient, $this> */
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    /** @return BelongsTo<PackagingMaterial, $this> */
    public function packagingMaterial(): BelongsTo
    {
        return $this->belongsTo(PackagingMaterial::class, 'packaging_material_id');
    }

    public function getNameAttribute(?string $value): string
    {
        if (! empty($value)) {
            return $value;
        }
        if ($this->group === 'ingredient' && $this->ingredient) {
            return $this->ingredient->name;
        }
        if ($this->group === 'packaging' && $this->packagingMaterial) {
            return $this->packagingMaterial->name;
        }

        return 'Item #'.$this->id;
    }

    public function getPricePerUnitAttribute(): float
    {
        $amt = (float) $this->package_amount;
        if ($amt <= 0) {
            return 0.0;
        }

        return (float) $this->package_price / $amt;
    }

    public function getLineCostAttribute(): float
    {
        return round($this->price_per_unit * ((float) $this->qty_used), 4);
    }
}
