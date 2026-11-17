<?php

namespace App\Domains\Sales\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
final class CustomOrderFilterData extends Data
{
    public function __construct(
        public ?string $keyword = null,
        public ?string $status = null,
        public ?string $from = null,
        public ?string $to = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {

        return $properties;
    }
}
