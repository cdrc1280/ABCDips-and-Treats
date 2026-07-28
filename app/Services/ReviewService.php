<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Review;
use App\Models\ReviewVote;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReviewService
{
    public function getApprovedProductReviews(int $productId, int $perPage = 10): LengthAwarePaginator
    {
        return Review::query()
            ->with(['media'])
            ->where('product_id', $productId)
            ->where('is_approved', true)
            ->orderByDesc('is_featured')
            ->orderByDesc('helpful_votes')
            ->latest()
            ->paginate($perPage);
    }

    public function createReview(array $data, ?User $user = null): Review
    {
        $isVerified = false;

        if ($user) {
            // Check if user has a completed order containing this product
            $isVerified = Order::where('user_id', $user->id)
                ->where('status', Order::STATUS_COMPLETED)
                ->whereHas('items', fn ($q) => $q->where('product_id', $data['product_id']))
                ->exists();
        }

        $review = Review::create([
            'product_id'        => $data['product_id'],
            'user_id'           => $user?->id,
            'reviewer_name'     => $data['reviewer_name'] ?? $user?->name ?? 'Anonymous Baker',
            'reviewer_email'    => $data['reviewer_email'] ?? $user?->email ?? 'anonymous@abcdips.test',
            'rating'            => (int) $data['rating'],
            'title'             => $data['title'] ?? null,
            'comment'           => $data['comment'],
            'is_verified_buyer' => $isVerified,
            'is_approved'       => true, // Auto-approved for demo; admin can reject/feature via Filament
        ]);

        if (!empty($data['photos'])) {
            foreach ($data['photos'] as $photo) {
                if ($photo instanceof \Illuminate\Http\UploadedFile) {
                    $review->addMedia($photo)->toMediaCollection('review_photos');
                }
            }
        }

        return $review;
    }

    public function voteHelpful(int $reviewId, string $ip, ?User $user = null): bool
    {
        $existing = ReviewVote::where('review_id', $reviewId)
            ->where('ip_address', $ip)
            ->first();

        if ($existing) {
            return false;
        }

        ReviewVote::create([
            'review_id'  => $reviewId,
            'user_id'    => $user?->id,
            'ip_address' => $ip,
        ]);

        Review::where('id', $reviewId)->increment('helpful_votes');

        return true;
    }
}
