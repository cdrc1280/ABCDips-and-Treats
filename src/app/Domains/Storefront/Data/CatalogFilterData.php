<?php

namespace App\Domains\Storefront\Data;

use Spatie\LaravelData\Data;

/** Shop listing query string: ?search=&category=&sort=&page= */
final class CatalogFilterData extends Data
{
    public const SORTS = ['latest', 'popular', 'rating_high', 'price_low', 'price_high', 'name_asc'];

    public function __construct(
        public ?string $search = null,
        public ?string $category = null,
        public string $sort = 'latest',
    ) {}

    public static function fromQuery(array $query): self
    {
        $search = trim((string) ($query['search'] ?? ''));
        $category = trim((string) ($query['category'] ?? ''));
        $sort = (string) ($query['sort'] ?? 'latest');

        return new self(
            search: $search !== '' ? mb_substr($search, 0, 100) : null,
            category: $category !== '' ? mb_substr($category, 0, 120) : null,
            sort: in_array($sort, self::SORTS, true) ? $sort : 'latest',
        );
    }
}
