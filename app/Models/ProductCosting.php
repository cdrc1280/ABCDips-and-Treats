<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductCosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'yield_qty',
        'yield_unit',
        'overhead_pct',
        'markup_pct',
        'labor_pct',
    ];

    protected $casts = [
        'yield_qty'    => 'decimal:3',
        'overhead_pct' => 'decimal:2',
        'markup_pct'   => 'decimal:2',
        'labor_pct'    => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saved(function (ProductCosting $costing) {
            if ($costing->product_id && $costing->price_per_piece > 0) {
                $newPrice = round($costing->price_per_piece, 2);
                $costing->product()->update(['price' => $newPrice]);
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getNameAttribute(?string $value): string
    {
        if (! empty($value)) {
            return $value;
        }
        return $this->product?->name ?? 'Costing #' . $this->id;
    }

    public function costingItems(): HasMany
    {
        return $this->hasMany(CostingItem::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(CostingItem::class)->where('group', 'ingredient');
    }

    public function packagings(): HasMany
    {
        return $this->hasMany(CostingItem::class)->where('group', 'packaging');
    }

    public function getIngredientsSubtotalAttribute(): float
    {
        $sum = 0.0;
        foreach ($this->costingItems as $item) {
            if ($item->group === 'ingredient') {
                $sum += $item->line_cost;
            }
        }
        return round($sum, 4);
    }

    public function getPackagingSubtotalAttribute(): float
    {
        $sum = 0.0;
        foreach ($this->costingItems as $item) {
            if ($item->group === 'packaging') {
                $sum += $item->line_cost;
            }
        }
        return round($sum, 4);
    }

    public function getRawCostAttribute(): float
    {
        return round($this->ingredients_subtotal + $this->packaging_subtotal, 4);
    }

    public function getOverheadCostAttribute(): float
    {
        $pct = (float) ($this->overhead_pct ?? 40.0);
        return round($this->raw_cost * ($pct / 100), 4);
    }

    public function getTotalCostAttribute(): float
    {
        return round($this->raw_cost + $this->overhead_cost, 4);
    }

    public function getMarkupAttribute(): float
    {
        $pct = (float) ($this->markup_pct ?? 66.0);
        return round($this->total_cost * ($pct / 100), 4);
    }

    public function getLaborChargeAttribute(): float
    {
        $pct = (float) ($this->labor_pct ?? 60.0);
        return round($this->raw_cost * ($pct / 100), 4);
    }

    public function getSellingPriceAttribute(): float
    {
        return round($this->total_cost + $this->markup + $this->labor_charge, 4);
    }

    public function getPricePerPieceAttribute(): float
    {
        $yield = (float) $this->yield_qty;
        if ($yield <= 0) {
            return 0.0;
        }
        return round($this->selling_price / $yield, 4);
    }
}
