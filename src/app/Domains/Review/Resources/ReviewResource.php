<?php

namespace App\Domains\Review\Resources;

use App\Domains\Review\Models\Review;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class ReviewResource extends Resource
{
    public function __construct(
        public string $id,
        public ?string $productId,
        public ?string $productName,
        public int $rating,
        public ?string $ratingLabel,
        public string $reviewerName,
        public string $reviewerEmail,
        public ?string $title,
        public string $comment,
        public bool $isApproved,
        public bool $isFeatured,
        public bool $isVerifiedBuyer,
        public bool $isAnonymous,
        public int $helpfulVotes,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel(Review $review, array $context = []): self
    {
        return new self(
            id: $review->id,
            productId: $review->product_id === null ? null : $review->product_id,
            productName: $review->product?->name,
            rating: (int) $review->rating,
            ratingLabel: self::option(['5' => '★★★★★ 5', '4' => '★★★★ 4', '3' => '★★★ 3', '2' => '★★ 2', '1' => '★ 1'], $review->rating),
            reviewerName: $review->reviewer_name,
            reviewerEmail: $review->reviewer_email,
            title: $review->title,
            comment: $review->comment,
            isApproved: (bool) $review->is_approved,
            isFeatured: (bool) $review->is_featured,
            isVerifiedBuyer: (bool) $review->is_verified_buyer,
            isAnonymous: (bool) $review->is_anonymous,
            helpfulVotes: (int) $review->helpful_votes,
            createdAt: $review->created_at?->toIso8601String(),
            updatedAt: $review->updated_at?->toIso8601String(),
        );
    }

    /**
     * @param  array<int|string, string>  $options
     */
    private static function option(array $options, mixed $value): ?string
    {
        return $value === null ? null : ($options[(string) $value] ?? (string) $value);
    }
}
