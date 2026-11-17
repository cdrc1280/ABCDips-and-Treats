<?php

namespace App\Domains\Content\Queries;

use App\Domains\Content\Models\ContactMessage;
use App\Shared\Resources\StatData;

final class ContactMessageSummaryQuery
{
    /**
     * @return list<StatData>
     */
    public function execute(): array
    {
        return [
            new StatData('unread', __('Unread'), ContactMessage::query()->where('status', 'unread')->count(), __('Waiting for someone to read')),
            new StatData('replied', __('Replied'), ContactMessage::query()->where('status', 'replied')->count(), __('Answered')),
            new StatData('week', __('This week'), ContactMessage::query()->where('created_at', '>=', now()->startOfWeek())->count(), __('New messages')),
        ];
    }
}
