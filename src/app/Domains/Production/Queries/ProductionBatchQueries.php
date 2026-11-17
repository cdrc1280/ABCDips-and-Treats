<?php

namespace App\Domains\Production\Queries;

use App\Domains\Production\Models\ProductionBatch;
use App\Shared\Data\FilterData;
use App\Shared\Queries\Concerns\SortsQueries;
use App\Shared\Queries\OptionsQuery;
use Illuminate\Pagination\LengthAwarePaginator;

final class ProductionBatchQueries
{
    use SortsQueries;

    /**
     * @return LengthAwarePaginator<int, ProductionBatch>
     */
    public function paginate(FilterData $filters): LengthAwarePaginator
    {
        $query = ProductionBatch::query()
            ->search($filters->search)
            ->with(['product', 'recipe', 'baker']);

        $this->applySort($query, $filters->sortField ?? 'created_at', $filters->sortDir ?? 'desc');

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function getAllAsOptions(): array
    {
        return OptionsQuery::for(ProductionBatch::class, 'batch_number');
    }
}
