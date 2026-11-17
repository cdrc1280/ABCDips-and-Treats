<?php

namespace App\Domains\Review\Actions;

use App\Domains\Review\Models\Review;
use Illuminate\Support\Facades\DB;

/** Toggle featured. */
final class FeatureReviewAction
{
    public function execute(Review $review): Review
    {
        DB::transaction(function () use ($review) {
            $review->update(['is_featured' => ! $review->is_featured]);
        });

        return $review->refresh();
    }
}
