<?php

namespace App\Services;

use App\Http\Resources\ReviewResource;
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

    public function getStoreServiceReviews(int $perPage = 10): array
    {
        $query = Review::query()->where('is_approved', true);

        $totalReviews = (clone $query)->count();
        $avgRating = $totalReviews > 0 ? round((float) (clone $query)->avg('rating'), 1) : null;

        $distribution = [
            5 => (clone $query)->where('rating', 5)->count(),
            4 => (clone $query)->where('rating', 4)->count(),
            3 => (clone $query)->where('rating', 3)->count(),
            2 => (clone $query)->where('rating', 2)->count(),
            1 => (clone $query)->where('rating', 1)->count(),
        ];

        $reviews = (clone $query)
            ->with(['media', 'product:id,name,slug'])
            ->orderByDesc('is_featured')
            ->orderByDesc('helpful_votes')
            ->latest()
            ->paginate($perPage);

        return [
            'stats' => [
                'total_reviews'   => $totalReviews,
                'avg_rating'      => $avgRating,
                'service_scores'  => $totalReviews > 0 ? [
                    'taste_freshness'  => $avgRating,
                    'delivery_speed'   => $avgRating,
                    'customer_service' => $avgRating,
                ] : null,
                'distribution'    => $distribution,
            ],
            'reviews' => ReviewResource::collection($reviews)->response()->getData(true),
        ];
    }

    public function createReview(array $data, ?User $user = null): Review
    {
        $isVerified = false;

        if ($user && !empty($data['product_id'])) {
            $isVerified = Order::where('user_id', $user->id)
                ->where('status', Order::STATUS_COMPLETED)
                ->whereHas('items', fn ($q) => $q->where('product_id', $data['product_id']))
                ->exists();
        }

        $review = Review::create([
            'product_id'        => $data['product_id'] ?? null,
            'user_id'           => $user?->id,
            'reviewer_name'     => $data['reviewer_name'] ?? $user?->name ?? 'Anonymous Baker',
            'reviewer_email'    => $data['reviewer_email'] ?? $user?->email ?? 'anonymous@abcdips.test',
            'rating'            => (int) $data['rating'],
            'title'             => $data['title'] ?? null,
            'comment'           => $data['comment'],
            'is_verified_buyer' => $isVerified,
            'is_approved'       => true,
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
