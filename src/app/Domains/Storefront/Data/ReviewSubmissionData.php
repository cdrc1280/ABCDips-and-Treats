<?php

namespace App\Domains\Storefront\Data;

use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class ReviewSubmissionData extends Data
{
    /**
     * @param  list<UploadedFile>  $photos
     */
    public function __construct(
        public int $rating,
        public string $comment,
        public ?string $productId = null,
        public ?string $title = null,
        public bool $isAnonymous = false,
        public ?string $reviewerName = null,
        public ?string $reviewerEmail = null,
        public array $photos = [],
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['is_anonymous'] = filter_var($properties['is_anonymous'] ?? false, FILTER_VALIDATE_BOOLEAN);
        foreach (['title', 'reviewer_name', 'reviewer_email'] as $key) {
            $properties[$key] = trim(strip_tags((string) ($properties[$key] ?? ''))) ?: null;
        }
        $properties['comment'] = trim(strip_tags((string) ($properties['comment'] ?? '')));
        $properties['photos'] = array_values(array_filter((array) ($properties['photos'] ?? [])));

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $guest = ! request()->user();

        return [
            'product_id' => ['bail', 'nullable', 'uuid', 'exists:products,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:255'],
            'comment' => ['required', 'string', 'min:10', 'max:3000'],
            'is_anonymous' => ['boolean'],
            'reviewer_name' => [$guest ? 'required' : 'nullable', 'string', 'max:120'],
            'reviewer_email' => [$guest ? 'required' : 'nullable', 'email', 'max:255'],
            'photos' => ['array', 'max:4'],
            'photos.*' => ['image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ];
    }

    public static function attributes(mixed ...$args): array
    {
        return ['reviewer_name' => 'name', 'reviewer_email' => 'email'];
    }
}
