<?php

namespace App\Domains\Preference\Support;

use App\Shared\Data\FilterData;

/**
 * Resolved state of one index table: the query to run plus the saved column layout.
 */
final class TableState
{
    /**
     * @param  array<string, mixed>  $columns  column prefs: hidden, order, widths, pinned
     */
    public function __construct(
        public readonly string $name,
        public readonly FilterData $query,
        public readonly array $columns = [],
    ) {}

    /**
     * @return array{name: string, columns: array<string, mixed>, per_page_options: list<int>}
     */
    public function forFrontend(): array
    {
        return [
            'name' => $this->name,
            'columns' => $this->columns,
            'per_page_options' => FilterData::PER_PAGE_OPTIONS,
        ];
    }
}
