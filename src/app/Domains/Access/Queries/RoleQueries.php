<?php

namespace App\Domains\Access\Queries;

use App\Domains\Access\Models\Role;
use App\Shared\Data\FilterData;
use App\Shared\Queries\Concerns\SortsQueries;
use App\Shared\Queries\OptionsQuery;
use Illuminate\Pagination\LengthAwarePaginator;

final class RoleQueries
{
    use SortsQueries;

    /**
     * @return LengthAwarePaginator<int, Role>
     */
    public function paginate(FilterData $filters): LengthAwarePaginator
    {
        $query = Role::query()
            ->search($filters->search)
            ->with(['permissions'])
            ->withCount(['users', 'permissions']);

        $this->applySort($query, $filters->sortField ?? 'name', $filters->sortDir ?? 'asc');

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function getAllAsOptions(): array
    {
        return OptionsQuery::for(Role::class, 'name');
    }
}
