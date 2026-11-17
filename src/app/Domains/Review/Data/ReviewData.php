<?php

namespace App\Domains\Review\Data;

use App\Shared\Support\RouteModel;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class ReviewData extends Data
{
    public function __construct(
        public string $reviewerName,
        public string $reviewerEmail,
        public string $comment,
        public ?string $productId = null,
        public ?int $rating = 5,
        public ?string $title = null,
        public bool $isApproved = true,
        public bool $isFeatured = false,
        public bool $isVerifiedBuyer = false,
        public bool $isAnonymous = false,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['reviewer_name'] = trim((string) ($properties['reviewer_name'] ?? '')) ?: null;
        $properties['reviewer_email'] = strtolower(trim((string) ($properties['reviewer_email'] ?? ''))) ?: null;
        $properties['comment'] = trim((string) ($properties['comment'] ?? '')) ?: null;
        $properties['product_id'] = ($properties['product_id'] ?? '') === '' ? null : (string) $properties['product_id'];
        $properties['rating'] = ($properties['rating'] ?? '') === '' ? 5 : (int) $properties['rating'];
        $properties['title'] = trim((string) ($properties['title'] ?? '')) ?: null;
        $properties['is_approved'] = filter_var($properties['is_approved'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $properties['is_featured'] = filter_var($properties['is_featured'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $properties['is_verified_buyer'] = filter_var($properties['is_verified_buyer'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $properties['is_anonymous'] = filter_var($properties['is_anonymous'] ?? false, FILTER_VALIDATE_BOOLEAN);

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $id = RouteModel::key('review');

        return [
            'reviewer_name' => ['required', 'string', 'max:255'],
            'reviewer_email' => ['required', 'email', 'max:255'],
            'comment' => ['required', 'string', 'max:3000'],
            'product_id' => ['bail', 'nullable', 'uuid', Rule::exists('products', 'id')],
            'rating' => ['required', Rule::in(['5', '4', '3', '2', '1'])],
            'title' => ['nullable', 'string', 'max:255'],
            'is_approved' => ['boolean'],
            'is_featured' => ['boolean'],
            'is_verified_buyer' => ['boolean'],
            'is_anonymous' => ['boolean'],
        ];
    }

    public static function messages(mixed ...$args): array
    {
        return [

        ];
    }

    public static function attributes(mixed ...$args): array
    {
        return [
            'product_id' => __('product'),
            'rating' => __('rating'),
            'reviewer_name' => __('reviewer name'),
            'reviewer_email' => __('reviewer email'),
            'title' => __('headline'),
            'comment' => __('review'),
            'is_approved' => __('approved'),
            'is_featured' => __('featured'),
            'is_verified_buyer' => __('verified buyer'),
            'is_anonymous' => __('anonymous'),
        ];
    }
}
