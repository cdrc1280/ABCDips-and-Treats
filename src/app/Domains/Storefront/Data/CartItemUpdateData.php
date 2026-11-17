<?php

namespace App\Domains\Storefront\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class CartItemUpdateData extends Data
{
    /**
     * @param  array<string, mixed>|null  $options
     */
    public function __construct(
        public int $qty,
        public ?array $options = null,
    ) {}

    public static function rules(?ValidationContext $context = null): array
    {
        return [
            'qty' => ['required', 'integer', 'min:0', 'max:99'],
            'options' => ['nullable', 'array'],
        ];
    }
}
