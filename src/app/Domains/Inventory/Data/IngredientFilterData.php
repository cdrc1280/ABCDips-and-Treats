<?php

namespace App\Domains\Inventory\Data;

use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
final class IngredientFilterData extends Data
{
    public function __construct(
        public ?string $keyword = null,
        public ?string $supplierId = null,
        public ?string $stock = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        if (array_key_exists('supplier_id', $properties)) {
            $properties['supplier_id'] = Str::isUuid((string) $properties['supplier_id']) ? (string) $properties['supplier_id'] : null;
        }

        return $properties;
    }
}
