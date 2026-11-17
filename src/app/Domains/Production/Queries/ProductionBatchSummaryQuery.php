<?php

namespace App\Domains\Production\Queries;

use App\Domains\Production\Models\ProductionBatch;
use App\Shared\Resources\StatData;

final class ProductionBatchSummaryQuery
{
    /**
     * @return list<StatData>
     */
    public function execute(): array
    {
        return [
            new StatData('planned', __('Planned'), ProductionBatch::query()->where('status', 'planned')->count(), __('Not started')),
            new StatData('active', __('In progress'), ProductionBatch::query()->whereIn('status', ['in_prep', 'baking'])->count(), __('Prepping or in the oven')),
            new StatData('completed_week', __('Completed this week'), ProductionBatch::query()->where('status', 'completed')->where('completed_at', '>=', now()->startOfWeek())->count(), __('Since Monday')),
        ];
    }
}
