<?php

namespace App\Domains\Costing\Queries;

use App\Domains\Costing\Models\ProductCosting;
use App\Shared\Data\FilterData;
use App\Shared\Queries\Concerns\SortsQueries;
use App\Shared\Queries\OptionsQuery;
use Illuminate\Pagination\LengthAwarePaginator;

final class ProductCostingQueries
{
    use SortsQueries;

    /**
     * @return LengthAwarePaginator<int, ProductCosting>
     */
    public function paginate(FilterData $filters): LengthAwarePaginator
    {
        $query = ProductCosting::query()
            ->search($filters->search)
            ->with(['product', 'costingItems.ingredient', 'costingItems.packagingMaterial']);

        $this->applySort($query, $filters->sortField ?? 'created_at', $filters->sortDir ?? 'desc');

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function getAllAsOptions(): array
    {
        return OptionsQuery::for(ProductCosting::class, 'name');
    }
}
