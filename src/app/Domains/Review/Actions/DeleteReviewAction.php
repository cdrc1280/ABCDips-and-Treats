<?php

namespace App\Domains\Review\Actions;

use App\Domains\Review\Models\Review;
use Illuminate\Support\Facades\DB;

final class DeleteReviewAction
{
    public function execute(Review $review): void
    {
        DB::transaction(fn () => $review->delete());
    }
}
