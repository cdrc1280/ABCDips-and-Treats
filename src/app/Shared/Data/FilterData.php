<?php

namespace App\Shared\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * The query behind every index table: search[], sort_field, sort_dir, per_page, page.
 * `search` holds the module's filter fields; each module shapes it with its own {Thing}FilterData.
 */
#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
final class FilterData extends Data
{
    public const PER_PAGE_OPTIONS = [10, 15, 25, 50, 100];

    /**
     * @param  array<string, mixed>  $search
     */
    public function __construct(
        public array $search = [],
        public ?string $sortField = null,
        public ?string $sortDir = null,
        public int $perPage = 15,
        public int $page = 1,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['search'] = array_filter(
            (array) ($properties['search'] ?? []),
            fn ($value) => $value !== null && $value !== '' && $value !== 'all',
        );

        $dir = strtolower((string) ($properties['sort_dir'] ?? ''));
        $properties['sort_dir'] = in_array($dir, ['asc', 'desc'], true) ? $dir : null;

        $perPage = (int) ($properties['per_page'] ?? 15);
        $properties['per_page'] = in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : 15;
        $properties['page'] = max(1, (int) ($properties['page'] ?? 1));

        return $properties;
    }
}
