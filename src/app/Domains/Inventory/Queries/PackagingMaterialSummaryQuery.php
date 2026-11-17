<?php

namespace App\Domains\Inventory\Queries;

use App\Domains\Inventory\Models\PackagingMaterial;
use App\Shared\Resources\StatData;

final class PackagingMaterialSummaryQuery
{
    /**
     * @return list<StatData>
     */
    public function execute(): array
    {
        $value = (float) PackagingMaterial::query()->get(['stock_qty', 'cost_per_unit'])->sum(fn ($i) => (float) $i->stock_qty * (float) $i->cost_per_unit);

        return [
            new StatData('total', __('Packaging items'), PackagingMaterial::query()->count(), __('On file')),
            new StatData('low', __('Low stock'), PackagingMaterial::query()->where('is_active', true)->whereColumn('stock_qty', '<=', 'min_stock_qty')->count(), __('Active items at or below minimum')),
            new StatData('value', __('Stock value'), $value, __('Quantity × cost per unit'), '₱'.number_format($value, 2)),
        ];
    }
}
