<?php

namespace App\Domains\Storefront\Data;

use App\Domains\Catalog\Models\Product;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

/** Add-to-cart payload. Prices are never accepted: CartPricing derives them server-side. */
#[MapName(SnakeCaseMapper::class)]
final class CartItemData extends Data
{
    /**
     * @param  array<string, mixed>|null  $options
     */
    public function __construct(
        public string $productId,
        public int $qty = 1,
        public ?array $options = null,
    ) {}

    public static function rules(?ValidationContext $context = null): array
    {
        return [
            'product_id' => ['bail', 'required', 'uuid', function (string $attribute, mixed $value, \Closure $fail) {
                // Only products customers can actually buy (active, priced, active category).
                if (! Product::query()->forCustomer()->whereKey($value)->exists()) {
                    $fail(__('This product isn\'t available right now.'));
                }
            }],
            'qty' => ['required', 'integer', 'min:1', 'max:99'],
            'options' => ['nullable', 'array'],
        ];
    }
}
