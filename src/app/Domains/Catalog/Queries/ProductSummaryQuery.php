<?php

namespace App\Domains\Catalog\Queries;

use App\Domains\Catalog\Models\Product;
use App\Shared\Resources\StatData;

final class ProductSummaryQuery
{
    /**
     * @return list<StatData>
     */
    public function execute(): array
    {
        return [
            new StatData('total', __('Products'), Product::query()->count(), __('On the menu')),
            new StatData('active', __('Active'), Product::query()->where('is_active', true)->count(), __('Sold on the storefront')),
            new StatData('low', __('Low stock'), Product::query()->where('is_active', true)->whereColumn('stock_qty', '<=', 'min_stock_qty')->count(), __('Active products at or below their level')),
            new StatData('on_sale', __('On sale'), Product::query()->whereNotNull('sale_price')->where(fn ($q) => $q->whereNull('sale_ends_at')->orWhere('sale_ends_at', '>', now()))->count(), __('With an active sale price')),
        ];
    }
}
