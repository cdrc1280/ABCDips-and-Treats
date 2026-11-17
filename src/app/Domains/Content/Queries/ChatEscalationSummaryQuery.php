<?php

namespace App\Domains\Content\Queries;

use App\Domains\Content\Models\ChatEscalation;
use App\Shared\Resources\StatData;

final class ChatEscalationSummaryQuery
{
    /**
     * @return list<StatData>
     */
    public function execute(): array
    {
        return [
            new StatData('open', __('Open'), ChatEscalation::query()->where('status', 'open')->count(), __('Waiting for a reply')),
            new StatData('in_progress', __('In progress'), ChatEscalation::query()->where('status', 'in_progress')->count(), __('Being handled')),
            new StatData('resolved', __('Resolved'), ChatEscalation::query()->where('status', 'resolved')->count(), __('Closed conversations')),
        ];
    }
}
