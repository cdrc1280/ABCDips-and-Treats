<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipePackaging extends Model
{
    use HasFactory;

    protected $table = 'recipe_packagings';

    protected $fillable = [
        'recipe_id',
        'packaging_material_id',
        'name',
        'unit',
        'package_qty',
        'package_cost',
        'qty_used',
    ];

    protected $casts = [
        'package_qty'  => 'decimal:3',
        'package_cost' => 'decimal:2',
        'qty_used'     => 'decimal:3',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function packagingMaterial(): BelongsTo
    {
        return $this->belongsTo(PackagingMaterial::class, 'packaging_material_id');
    }

    /**
     * Calculate line cost according to Excel formula: (Package Cost / Package Qty) * Qty Used
     */
    public function getLineCostAttribute(): float
    {
        $pkgQty = (float) $this->package_qty;
        if ($pkgQty <= 0) {
            $pkgQty = 1.0;
        }
        $unitPrice = ((float) $this->package_cost) / $pkgQty;
        return round($unitPrice * ((float) $this->qty_used), 4);
    }
}
