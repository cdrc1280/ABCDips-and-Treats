<?php

namespace App\Domains\Inventory\Queries;

use App\Domains\Inventory\Models\Supplier;
use App\Shared\Data\FilterData;
use App\Shared\Queries\Concerns\SortsQueries;
use App\Shared\Queries\OptionsQuery;
use Illuminate\Pagination\LengthAwarePaginator;

final class SupplierQueries
{
    use SortsQueries;

    /**
     * @return LengthAwarePaginator<int, Supplier>
     */
    public function paginate(FilterData $filters): LengthAwarePaginator
    {
        $query = Supplier::query()
            ->search($filters->search)
            ->withCount(['ingredients', 'purchaseOrders']);

        $this->applySort($query, $filters->sortField ?? 'name', $filters->sortDir ?? 'asc');

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function getAllAsOptions(): array
    {
        return OptionsQuery::for(Supplier::class, 'name');
    }
}
