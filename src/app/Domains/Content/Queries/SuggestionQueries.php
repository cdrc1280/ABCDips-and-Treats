<?php

namespace App\Domains\Content\Queries;

use App\Domains\Content\Models\Suggestion;
use App\Shared\Data\FilterData;
use App\Shared\Queries\Concerns\SortsQueries;
use App\Shared\Queries\OptionsQuery;
use Illuminate\Pagination\LengthAwarePaginator;

final class SuggestionQueries
{
    use SortsQueries;

    /**
     * @return LengthAwarePaginator<int, Suggestion>
     */
    public function paginate(FilterData $filters): LengthAwarePaginator
    {
        $query = Suggestion::query()
            ->search($filters->search);

        $this->applySort($query, $filters->sortField ?? 'created_at', $filters->sortDir ?? 'desc');

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function getAllAsOptions(): array
    {
        return OptionsQuery::for(Suggestion::class, 'subject');
    }
}
