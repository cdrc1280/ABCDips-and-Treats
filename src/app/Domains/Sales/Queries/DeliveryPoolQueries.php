<?php

namespace App\Domains\Sales\Queries;

use App\Domains\Sales\Models\DeliveryPool;
use App\Shared\Data\FilterData;
use App\Shared\Queries\Concerns\SortsQueries;
use App\Shared\Queries\OptionsQuery;
use Illuminate\Pagination\LengthAwarePaginator;

final class DeliveryPoolQueries
{
    use SortsQueries;

    /**
     * @return LengthAwarePaginator<int, DeliveryPool>
     */
    public function paginate(FilterData $filters): LengthAwarePaginator
    {
        $query = DeliveryPool::query()
            ->search($filters->search)
            ->with(['orders'])
            ->withCount(['orders']);

        $this->applySort($query, $filters->sortField ?? 'created_at', $filters->sortDir ?? 'desc');

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function getAllAsOptions(): array
    {
        return OptionsQuery::for(DeliveryPool::class, 'pool_code');
    }
}
