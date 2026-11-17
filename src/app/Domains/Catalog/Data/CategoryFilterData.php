<?php

namespace App\Domains\Catalog\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
final class CategoryFilterData extends Data
{
    public function __construct(
        public ?string $keyword = null,
        public ?bool $isActive = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        if (isset($properties['is_active']) && ! is_bool($properties['is_active'])) {
            $properties['is_active'] = in_array($properties['is_active'], ['', 'all', null], true)
                ? null
                : filter_var($properties['is_active'], FILTER_VALIDATE_BOOLEAN);
        }

        return $properties;
    }
}
