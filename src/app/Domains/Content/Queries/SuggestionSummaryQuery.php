<?php

namespace App\Domains\Content\Queries;

use App\Domains\Content\Models\Suggestion;
use App\Shared\Resources\StatData;

final class SuggestionSummaryQuery
{
    /**
     * @return list<StatData>
     */
    public function execute(): array
    {
        return [
            new StatData('new', __('New'), Suggestion::query()->where('status', 'new')->count(), __('Not reviewed yet')),
            new StatData('reviewing', __('Reviewing'), Suggestion::query()->where('status', 'reviewing')->count(), __('Being considered')),
            new StatData('implemented', __('Implemented'), Suggestion::query()->where('status', 'implemented')->count(), __('Ideas we shipped')),
        ];
    }
}
