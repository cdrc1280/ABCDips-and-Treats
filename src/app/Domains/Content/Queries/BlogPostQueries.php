<?php

namespace App\Domains\Content\Queries;

use App\Domains\Content\Models\BlogPost;
use App\Shared\Data\FilterData;
use App\Shared\Queries\Concerns\SortsQueries;
use App\Shared\Queries\OptionsQuery;
use Illuminate\Pagination\LengthAwarePaginator;

final class BlogPostQueries
{
    use SortsQueries;

    /**
     * @return LengthAwarePaginator<int, BlogPost>
     */
    public function paginate(FilterData $filters): LengthAwarePaginator
    {
        $query = BlogPost::query()
            ->search($filters->search)
            ->with(['author']);

        $this->applySort($query, $filters->sortField ?? 'created_at', $filters->sortDir ?? 'desc');

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function getAllAsOptions(): array
    {
        return OptionsQuery::for(BlogPost::class, 'title');
    }
}
