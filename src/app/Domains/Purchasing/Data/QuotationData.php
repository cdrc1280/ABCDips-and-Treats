<?php

namespace App\Domains\Purchasing\Data;

use App\Shared\Support\RouteModel;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class QuotationData extends Data
{
    public function __construct(
        public ?string $quotationNumber = null,
        public ?string $supplierId = null,
        public ?string $clientName = null,
        public ?string $status = 'draft',
        public ?string $quotationDate = null,
        public ?string $validUntil = null,
        public array $items = [],
        public ?float $tax = 0,
        public ?string $notes = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['quotation_number'] = strtoupper(trim((string) ($properties['quotation_number'] ?? ''))) ?: null;
        $properties['supplier_id'] = ($properties['supplier_id'] ?? '') === '' ? null : (string) $properties['supplier_id'];
        $properties['client_name'] = trim((string) ($properties['client_name'] ?? '')) ?: null;
        $properties['status'] = trim((string) ($properties['status'] ?? '')) ?: 'draft';
        $properties['quotation_date'] = trim((string) ($properties['quotation_date'] ?? '')) ?: null;
        $properties['valid_until'] = trim((string) ($properties['valid_until'] ?? '')) ?: null;
        $properties['items'] = array_values(array_filter((array) ($properties['items'] ?? []), fn ($row) => is_array($row) && array_filter($row, fn ($v) => $v !== null && $v !== '') !== []));
        $properties['tax'] = ($properties['tax'] ?? '') === '' ? 0 : (float) $properties['tax'];
        $properties['notes'] = trim((string) ($properties['notes'] ?? '')) ?: null;

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $id = RouteModel::key('quotation');

        return [
            'quotation_number' => ['nullable', 'string', 'max:40', Rule::unique('quotations', 'quotation_number')->ignore($id)->whereNull('deleted_at')],
            'supplier_id' => ['bail', 'nullable', 'uuid', Rule::exists('suppliers', 'id')->whereNull('deleted_at')],
            'client_name' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['draft', 'sent', 'accepted', 'rejected', 'converted_to_po'])],
            'quotation_date' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:quotation_date'],
            'items' => ['array', 'max:100'],
            'items.*.ingredient_id' => ['bail', 'nullable', 'uuid', Rule::exists('ingredients', 'id')],
            'items.*.item_description' => ['nullable', 'string', 'max:255'],
            'items.*.qty' => ['required', 'numeric', 'min:0'],
            'items.*.unit' => ['required', 'string', 'max:255'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'tax' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public static function messages(mixed ...$args): array
    {
        return [

        ];
    }

    public static function attributes(mixed ...$args): array
    {
        return [
            'quotation_number' => __('quotation number'),
            'supplier_id' => __('supplier'),
            'client_name' => __('contact / client name'),
            'status' => __('status'),
            'quotation_date' => __('quote date'),
            'valid_until' => __('valid until'),
            'items' => __('items'),
            'tax' => __('tax'),
            'notes' => __('notes'),
        ];
    }
}
