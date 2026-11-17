<?php

namespace App\Domains\Review\Queries;

use App\Domains\Review\Models\Review;
use App\Shared\Resources\StatData;

final class ReviewSummaryQuery
{
    /**
     * @return list<StatData>
     */
    public function execute(): array
    {
        return [
            new StatData('total', __('Reviews'), Review::query()->count(), __('All time')),
            new StatData('average', __('Average rating'), round((float) Review::query()->where('is_approved', true)->avg('rating'), 1), __('Approved reviews')),
            new StatData('pending', __('Hidden'), Review::query()->where('is_approved', false)->count(), __('Waiting for approval')),
            new StatData('featured', __('Featured'), Review::query()->where('is_featured', true)->count(), __('On the homepage')),
        ];
    }
}
