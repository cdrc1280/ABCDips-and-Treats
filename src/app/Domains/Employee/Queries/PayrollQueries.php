<?php

namespace App\Domains\Employee\Queries;

use App\Domains\Employee\Models\Payroll;
use App\Shared\Data\FilterData;
use App\Shared\Queries\Concerns\SortsQueries;
use App\Shared\Queries\OptionsQuery;
use Illuminate\Pagination\LengthAwarePaginator;

final class PayrollQueries
{
    use SortsQueries;

    /**
     * @return LengthAwarePaginator<int, Payroll>
     */
    public function paginate(FilterData $filters): LengthAwarePaginator
    {
        $query = Payroll::query()
            ->search($filters->search)
            ->with(['items.employee'])
            ->withCount(['items']);

        $this->applySort($query, $filters->sortField ?? 'period_start', $filters->sortDir ?? 'desc');

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function getAllAsOptions(): array
    {
        return OptionsQuery::for(Payroll::class, 'payroll_number');
    }
}
