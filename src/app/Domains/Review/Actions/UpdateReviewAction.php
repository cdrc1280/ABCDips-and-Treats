<?php

namespace App\Domains\Review\Actions;

use App\Domains\Review\Data\ReviewData;
use App\Domains\Review\Models\Review;
use Illuminate\Support\Facades\DB;

final class UpdateReviewAction
{
    public function execute(Review $review, ReviewData $data): Review
    {
        return DB::transaction(function () use ($review, $data) {
            $review->update([
                'product_id' => $data->productId,
                'rating' => $data->rating,
                'reviewer_name' => $data->reviewerName,
                'reviewer_email' => $data->reviewerEmail,
                'title' => $data->title,
                'comment' => $data->comment,
                'is_approved' => $data->isApproved,
                'is_featured' => $data->isFeatured,
                'is_verified_buyer' => $data->isVerifiedBuyer,
                'is_anonymous' => $data->isAnonymous,
            ]);

            return $review;
        });
    }
}
