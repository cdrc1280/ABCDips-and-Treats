<?php

namespace App\Domains\Sales\Queries;

use App\Domains\Sales\Models\CustomOrder;
use App\Shared\Resources\StatData;

final class CustomOrderSummaryQuery
{
    /**
     * @return list<StatData>
     */
    public function execute(): array
    {
        return [
            new StatData('open', __('Open inquiries'), CustomOrder::query()->whereIn('status', ['inquiry', 'quoted'])->count(), __('Waiting on a quote or reply')),
            new StatData('upcoming', __('Next 7 days'), CustomOrder::query()->whereNotIn('status', ['completed', 'cancelled'])->whereBetween('event_date', [today(), today()->addDays(7)])->count(), __('Events coming up')),
            new StatData('in_production', __('In production'), CustomOrder::query()->where('status', 'in_production')->count(), __('Being baked')),
            new StatData('completed', __('Completed'), CustomOrder::query()->where('status', 'completed')->count(), __('Delivered or picked up')),
        ];
    }
}
