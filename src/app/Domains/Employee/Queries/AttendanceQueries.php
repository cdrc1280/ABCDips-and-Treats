<?php

namespace App\Domains\Employee\Queries;

use App\Domains\Employee\Models\Attendance;
use App\Shared\Data\FilterData;
use App\Shared\Queries\Concerns\SortsQueries;
use App\Shared\Queries\OptionsQuery;
use Illuminate\Pagination\LengthAwarePaginator;

final class AttendanceQueries
{
    use SortsQueries;

    /**
     * @return LengthAwarePaginator<int, Attendance>
     */
    public function paginate(FilterData $filters): LengthAwarePaginator
    {
        $query = Attendance::query()
            ->search($filters->search)
            ->with(['employee']);

        $this->applySort($query, $filters->sortField ?? 'date', $filters->sortDir ?? 'desc');

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function getAllAsOptions(): array
    {
        return OptionsQuery::for(Attendance::class, 'date');
    }
}
