<?php

namespace App\Domains\Sales\Queries;

use App\Domains\Sales\Models\CustomOrder;
use App\Shared\Data\FilterData;
use App\Shared\Queries\Concerns\SortsQueries;
use App\Shared\Queries\OptionsQuery;
use Illuminate\Pagination\LengthAwarePaginator;

final class CustomOrderQueries
{
    use SortsQueries;

    /**
     * @return LengthAwarePaginator<int, CustomOrder>
     */
    public function paginate(FilterData $filters): LengthAwarePaginator
    {
        $query = CustomOrder::query()
            ->search($filters->search);

        $this->applySort($query, $filters->sortField ?? 'event_date', $filters->sortDir ?? 'asc');

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function getAllAsOptions(): array
    {
        return OptionsQuery::for(CustomOrder::class, 'reference_number');
    }
}
