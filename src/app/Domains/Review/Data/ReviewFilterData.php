<?php

namespace App\Domains\Review\Data;

use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
final class ReviewFilterData extends Data
{
    public function __construct(
        public ?string $keyword = null,
        public ?string $productId = null,
        public ?int $rating = null,
        public ?bool $isApproved = null,
        public ?bool $isFeatured = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        if (array_key_exists('product_id', $properties)) {
            $properties['product_id'] = Str::isUuid((string) $properties['product_id']) ? (string) $properties['product_id'] : null;
        }
        if (array_key_exists('rating', $properties)) {
            $properties['rating'] = is_numeric($properties['rating']) ? (int) $properties['rating'] : null;
        }
        if (array_key_exists('is_approved', $properties) && ! is_bool($properties['is_approved'])) {
            $properties['is_approved'] = in_array($properties['is_approved'], ['', 'all', null], true) ? null : filter_var($properties['is_approved'], FILTER_VALIDATE_BOOLEAN);
        }
        if (array_key_exists('is_featured', $properties) && ! is_bool($properties['is_featured'])) {
            $properties['is_featured'] = in_array($properties['is_featured'], ['', 'all', null], true) ? null : filter_var($properties['is_featured'], FILTER_VALIDATE_BOOLEAN);
        }

        return $properties;
    }
}
