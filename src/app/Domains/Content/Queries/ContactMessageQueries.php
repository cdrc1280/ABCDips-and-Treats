<?php

namespace App\Domains\Content\Queries;

use App\Domains\Content\Models\ContactMessage;
use App\Shared\Data\FilterData;
use App\Shared\Queries\Concerns\SortsQueries;
use App\Shared\Queries\OptionsQuery;
use Illuminate\Pagination\LengthAwarePaginator;

final class ContactMessageQueries
{
    use SortsQueries;

    /**
     * @return LengthAwarePaginator<int, ContactMessage>
     */
    public function paginate(FilterData $filters): LengthAwarePaginator
    {
        $query = ContactMessage::query()
            ->search($filters->search);

        $this->applySort($query, $filters->sortField ?? 'created_at', $filters->sortDir ?? 'desc');

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function getAllAsOptions(): array
    {
        return OptionsQuery::for(ContactMessage::class, 'subject');
    }
}
