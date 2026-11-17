<?php

namespace App\Shared\Resources;

use Spatie\LaravelData\Resource;

/**
 * One stat card above an index table. `display` is a preformatted value (e.g. "₱1,234.00");
 * when null the frontend formats `value` itself.
 */
final class StatData extends Resource
{
    public function __construct(
        public string $key,
        public string $label,
        public int|float $value,
        public ?string $description = null,
        public ?string $display = null,
    ) {}
}
