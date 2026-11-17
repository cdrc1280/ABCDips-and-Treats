<?php

namespace App\Domains\User\Queries;

use App\Domains\User\Models\User;
use App\Shared\Data\FilterData;
use App\Shared\Queries\Concerns\SortsQueries;
use App\Shared\Queries\OptionsQuery;
use Illuminate\Pagination\LengthAwarePaginator;

final class UserQueries
{
    use SortsQueries;

    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function paginate(FilterData $filters): LengthAwarePaginator
    {
        $query = User::query()
            ->whereHas('roles', fn ($r) => $r->where('name', '!=', 'customer'))
            ->search($filters->search)
            ->with(['roles']);

        $this->applySort($query, $filters->sortField ?? 'name', $filters->sortDir ?? 'asc');

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function getAllAsOptions(): array
    {
        return OptionsQuery::for(User::class, 'name', fn ($query) => $query->whereHas('roles', fn ($r) => $r->where('name', '!=', 'customer')));
    }
}
