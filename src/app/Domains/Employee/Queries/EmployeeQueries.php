<?php

namespace App\Domains\Employee\Queries;

use App\Domains\Employee\Models\Employee;
use App\Shared\Data\FilterData;
use App\Shared\Queries\Concerns\SortsQueries;
use App\Shared\Queries\OptionsQuery;
use Illuminate\Pagination\LengthAwarePaginator;

final class EmployeeQueries
{
    use SortsQueries;

    /**
     * @return LengthAwarePaginator<int, Employee>
     */
    public function paginate(FilterData $filters): LengthAwarePaginator
    {
        $query = Employee::query()
            ->search($filters->search)
            ->with(['user']);

        $this->applySort($query, $filters->sortField ?? 'first_name', $filters->sortDir ?? 'asc');

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function getAllAsOptions(): array
    {
        return OptionsQuery::for(Employee::class, 'first_name');
    }
}
