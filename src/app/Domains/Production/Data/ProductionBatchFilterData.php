<?php

namespace App\Domains\Production\Data;

use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
final class ProductionBatchFilterData extends Data
{
    public function __construct(
        public ?string $keyword = null,
        public ?string $status = null,
        public ?string $productId = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        if (array_key_exists('product_id', $properties)) {
            $properties['product_id'] = Str::isUuid((string) $properties['product_id']) ? (string) $properties['product_id'] : null;
        }

        return $properties;
    }
}
