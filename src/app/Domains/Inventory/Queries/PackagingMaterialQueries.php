<?php

namespace App\Domains\Inventory\Queries;

use App\Domains\Inventory\Models\PackagingMaterial;
use App\Shared\Data\FilterData;
use App\Shared\Queries\Concerns\SortsQueries;
use App\Shared\Queries\OptionsQuery;
use Illuminate\Pagination\LengthAwarePaginator;

final class PackagingMaterialQueries
{
    use SortsQueries;

    /**
     * @return LengthAwarePaginator<int, PackagingMaterial>
     */
    public function paginate(FilterData $filters): LengthAwarePaginator
    {
        $query = PackagingMaterial::query()
            ->search($filters->search);

        $this->applySort($query, $filters->sortField ?? 'name', $filters->sortDir ?? 'asc');

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function getAllAsOptions(): array
    {
        return OptionsQuery::for(PackagingMaterial::class, 'name');
    }
}
