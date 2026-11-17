<?php

namespace App\Domains\Sales\Queries;

use App\Domains\Sales\Models\Order;
use App\Shared\Data\FilterData;
use App\Shared\Queries\Concerns\SortsQueries;
use App\Shared\Queries\OptionsQuery;
use Illuminate\Pagination\LengthAwarePaginator;

final class OrderQueries
{
    use SortsQueries;

    /**
     * @return LengthAwarePaginator<int, Order>
     */
    public function paginate(FilterData $filters): LengthAwarePaginator
    {
        $query = Order::query()
            ->search($filters->search)
            ->withCount(['items']);

        $this->applySort($query, $filters->sortField ?? 'created_at', $filters->sortDir ?? 'desc');

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function getAllAsOptions(): array
    {
        return OptionsQuery::for(Order::class, 'order_number');
    }
}
