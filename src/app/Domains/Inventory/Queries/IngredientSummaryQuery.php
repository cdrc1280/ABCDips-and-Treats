<?php

namespace App\Domains\Inventory\Queries;

use App\Domains\Inventory\Models\Ingredient;
use App\Shared\Resources\StatData;

final class IngredientSummaryQuery
{
    /**
     * @return list<StatData>
     */
    public function execute(): array
    {
        $value = (float) Ingredient::query()->get(['stock_qty', 'cost_per_unit'])->sum(fn ($i) => (float) $i->stock_qty * (float) $i->cost_per_unit);

        return [
            new StatData('total', __('Ingredients'), Ingredient::query()->count(), __('Tracked raw materials')),
            new StatData('low', __('Low stock'), Ingredient::query()->whereColumn('stock_qty', '<=', 'min_stock_qty')->count(), __('At or below minimum')),
            new StatData('value', __('Stock value'), $value, __('Quantity × cost per unit'), '₱'.number_format($value, 2)),
        ];
    }
}
