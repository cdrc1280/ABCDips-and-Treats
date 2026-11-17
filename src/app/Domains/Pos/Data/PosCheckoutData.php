<?php

namespace App\Domains\Pos\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class PosCheckoutData extends Data
{
    /**
     * @param  list<array{id: int, qty: int}>  $items
     */
    public function __construct(
        public string $paymentMethod,
        public array $items,
        public ?string $customerName = null,
        public ?float $cashTendered = null,
        public ?string $reference = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['customer_name'] = trim((string) ($properties['customer_name'] ?? '')) ?: null;
        $properties['reference'] = trim((string) ($properties['reference'] ?? '')) ?: null;
        $properties['cash_tendered'] = ($properties['cash_tendered'] ?? '') === '' ? null : (float) $properties['cash_tendered'];

        // Merge duplicate lines for the same product.
        $merged = [];
        foreach ((array) ($properties['items'] ?? []) as $row) {
            $id = (string) ($row['id'] ?? '');
            $merged[$id] = ['id' => $id, 'qty' => ($merged[$id]['qty'] ?? 0) + (int) ($row['qty'] ?? 0)];
        }
        $properties['items'] = array_values($merged);

        return $properties;
    }

    public static function rules(): array
    {
        return [
            'payment_method' => ['required', 'in:cash,gcash,maya,card'],
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*.id' => ['bail', 'required', 'uuid', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:999'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'cash_tendered' => ['nullable', 'required_if:payment_method,cash', 'numeric', 'min:0', 'max:1000000'],
            'reference' => ['nullable', 'string', 'max:100'],
        ];
    }

    public static function messages(mixed ...$args): array
    {
        return [
            'items.required' => __('Add at least one item to the sale.'),
            'cash_tendered.required_if' => __('Enter the cash received.'),
        ];
    }
}
