<?php

namespace App\Domains\Catalog\Queries;

use App\Domains\Catalog\Models\Product;
use App\Shared\Data\FilterData;
use App\Shared\Queries\Concerns\SortsQueries;
use App\Shared\Queries\OptionsQuery;
use Illuminate\Pagination\LengthAwarePaginator;

final class ProductQueries
{
    use SortsQueries;

    /**
     * @return LengthAwarePaginator<int, Product>
     */
    public function paginate(FilterData $filters): LengthAwarePaginator
    {
        $query = Product::query()
            ->search($filters->search)
            ->with(['category']);

        $this->applySort($query, $filters->sortField ?? 'created_at', $filters->sortDir ?? 'desc');

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function getAllAsOptions(): array
    {
        return OptionsQuery::for(Product::class, 'name');
    }
}
