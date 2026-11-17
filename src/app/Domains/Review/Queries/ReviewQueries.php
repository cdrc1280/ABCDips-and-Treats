<?php

namespace App\Domains\Review\Queries;

use App\Domains\Review\Models\Review;
use App\Shared\Data\FilterData;
use App\Shared\Queries\Concerns\SortsQueries;
use App\Shared\Queries\OptionsQuery;
use Illuminate\Pagination\LengthAwarePaginator;

final class ReviewQueries
{
    use SortsQueries;

    /**
     * @return LengthAwarePaginator<int, Review>
     */
    public function paginate(FilterData $filters): LengthAwarePaginator
    {
        $query = Review::query()
            ->search($filters->search)
            ->with(['product']);

        $this->applySort($query, $filters->sortField ?? 'created_at', $filters->sortDir ?? 'desc');

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function getAllAsOptions(): array
    {
        return OptionsQuery::for(Review::class, 'reviewer_name');
    }
}
