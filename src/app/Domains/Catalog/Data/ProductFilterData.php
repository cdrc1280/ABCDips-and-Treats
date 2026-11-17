<?php

namespace App\Domains\Catalog\Data;

use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
final class ProductFilterData extends Data
{
    public function __construct(
        public ?string $keyword = null,
        public ?string $categoryId = null,
        public ?bool $isActive = null,
        public ?bool $isFeatured = null,
        public ?string $stock = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        if (array_key_exists('category_id', $properties)) {
            $properties['category_id'] = Str::isUuid((string) $properties['category_id']) ? (string) $properties['category_id'] : null;
        }
        if (array_key_exists('is_active', $properties) && ! is_bool($properties['is_active'])) {
            $properties['is_active'] = in_array($properties['is_active'], ['', 'all', null], true) ? null : filter_var($properties['is_active'], FILTER_VALIDATE_BOOLEAN);
        }
        if (array_key_exists('is_featured', $properties) && ! is_bool($properties['is_featured'])) {
            $properties['is_featured'] = in_array($properties['is_featured'], ['', 'all', null], true) ? null : filter_var($properties['is_featured'], FILTER_VALIDATE_BOOLEAN);
        }

        return $properties;
    }
}
