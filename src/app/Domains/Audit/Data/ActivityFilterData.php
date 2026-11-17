<?php

namespace App\Domains\Audit\Data;

use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
final class ActivityFilterData extends Data
{
    public function __construct(
        public ?string $keyword = null,
        public ?string $logName = null,
        public ?string $event = null,
        public ?string $causerId = null,
        public ?string $from = null,
        public ?string $to = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        if (array_key_exists('causer_id', $properties)) {
            $properties['causer_id'] = Str::isUuid((string) $properties['causer_id']) ? (string) $properties['causer_id'] : null;
        }

        return $properties;
    }
}
