<?php

namespace App\Domains\Purchasing\Queries;

use App\Domains\Purchasing\Models\PurchaseOrder;
use App\Shared\Data\FilterData;
use App\Shared\Queries\Concerns\SortsQueries;
use App\Shared\Queries\OptionsQuery;
use Illuminate\Pagination\LengthAwarePaginator;

final class PurchaseOrderQueries
{
    use SortsQueries;

    /**
     * @return LengthAwarePaginator<int, PurchaseOrder>
     */
    public function paginate(FilterData $filters): LengthAwarePaginator
    {
        $query = PurchaseOrder::query()
            ->search($filters->search)
            ->with(['supplier', 'items.ingredient']);

        $this->applySort($query, $filters->sortField ?? 'created_at', $filters->sortDir ?? 'desc');

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function getAllAsOptions(): array
    {
        return OptionsQuery::for(PurchaseOrder::class, 'po_number');
    }
}
