<?php

namespace App\Domains\Purchasing\Data;

use App\Shared\Support\RouteModel;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class PurchaseOrderData extends Data
{
    public function __construct(
        public string $supplierId,
        public array $items = [],
        public ?string $poNumber = null,
        public ?string $status = 'draft',
        public ?string $expectedDeliveryDate = null,
        public ?string $paymentTerms = null,
        public ?float $tax = 0,
        public ?string $deliveryReceiptNo = null,
        public ?string $salesInvoiceNo = null,
        public ?string $notes = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['supplier_id'] = ($properties['supplier_id'] ?? '') === '' ? null : (string) $properties['supplier_id'];
        $properties['items'] = array_values(array_filter((array) ($properties['items'] ?? []), fn ($row) => is_array($row) && array_filter($row, fn ($v) => $v !== null && $v !== '') !== []));
        $properties['po_number'] = strtoupper(trim((string) ($properties['po_number'] ?? ''))) ?: null;
        $properties['status'] = trim((string) ($properties['status'] ?? '')) ?: 'draft';
        $properties['expected_delivery_date'] = trim((string) ($properties['expected_delivery_date'] ?? '')) ?: null;
        $properties['payment_terms'] = trim((string) ($properties['payment_terms'] ?? '')) ?: null;
        $properties['tax'] = ($properties['tax'] ?? '') === '' ? 0 : (float) $properties['tax'];
        $properties['delivery_receipt_no'] = trim((string) ($properties['delivery_receipt_no'] ?? '')) ?: null;
        $properties['sales_invoice_no'] = trim((string) ($properties['sales_invoice_no'] ?? '')) ?: null;
        $properties['notes'] = trim((string) ($properties['notes'] ?? '')) ?: null;

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $id = RouteModel::key('purchaseOrder');

        return [
            'supplier_id' => ['bail', 'required', 'uuid', Rule::exists('suppliers', 'id')->whereNull('deleted_at')],
            'items' => ['required', 'array', 'max:100'],
            'items.*.ingredient_id' => ['bail', 'required', 'uuid', Rule::exists('ingredients', 'id')],
            'items.*.qty_ordered' => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
            'po_number' => ['nullable', 'string', 'max:40', Rule::unique('purchase_orders', 'po_number')->ignore($id)->whereNull('deleted_at')],
            'status' => ['required', Rule::in(['draft', 'sent', 'cancelled'])],
            'expected_delivery_date' => ['nullable', 'date'],
            'payment_terms' => ['nullable', 'string', 'max:100'],
            'tax' => ['required', 'numeric', 'min:0'],
            'delivery_receipt_no' => ['nullable', 'string', 'max:100'],
            'sales_invoice_no' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public static function messages(mixed ...$args): array
    {
        return [
            'items.required' => __('Add at least one item.'),
        ];
    }

    public static function attributes(mixed ...$args): array
    {
        return [
            'po_number' => __('po number'),
            'supplier_id' => __('supplier'),
            'status' => __('status'),
            'expected_delivery_date' => __('expected delivery'),
            'payment_terms' => __('payment terms'),
            'items' => __('items'),
            'tax' => __('tax'),
            'delivery_receipt_no' => __('delivery receipt no.'),
            'sales_invoice_no' => __('sales invoice no.'),
            'notes' => __('notes'),
        ];
    }
}
