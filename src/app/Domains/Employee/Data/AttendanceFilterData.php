<?php

namespace App\Domains\Employee\Data;

use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
final class AttendanceFilterData extends Data
{
    public function __construct(
        public ?string $keyword = null,
        public ?string $employeeId = null,
        public ?string $status = null,
        public ?string $from = null,
        public ?string $to = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        if (array_key_exists('employee_id', $properties)) {
            $properties['employee_id'] = Str::isUuid((string) $properties['employee_id']) ? (string) $properties['employee_id'] : null;
        }

        return $properties;
    }
}
