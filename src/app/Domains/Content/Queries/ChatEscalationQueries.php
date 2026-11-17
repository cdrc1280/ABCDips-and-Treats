<?php

namespace App\Domains\Content\Queries;

use App\Domains\Content\Models\ChatEscalation;
use App\Shared\Data\FilterData;
use App\Shared\Queries\Concerns\SortsQueries;
use App\Shared\Queries\OptionsQuery;
use Illuminate\Pagination\LengthAwarePaginator;

final class ChatEscalationQueries
{
    use SortsQueries;

    /**
     * @return LengthAwarePaginator<int, ChatEscalation>
     */
    public function paginate(FilterData $filters): LengthAwarePaginator
    {
        $query = ChatEscalation::query()
            ->search($filters->search)
            ->with(['user']);

        $this->applySort($query, $filters->sortField ?? 'updated_at', $filters->sortDir ?? 'desc');

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function getAllAsOptions(): array
    {
        return OptionsQuery::for(ChatEscalation::class, 'guest_name');
    }
}
