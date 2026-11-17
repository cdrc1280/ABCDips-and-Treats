<?php

namespace App\Domains\Purchasing\Resources;

use App\Domains\Purchasing\Models\PurchaseOrder;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class PurchaseOrderResource extends Resource
{
    public function __construct(
        public string $id,
        public ?string $poNumber,
        public string $supplierId,
        public ?string $supplierName,
        public string $status,
        public ?string $statusLabel,
        public ?string $expectedDeliveryDate,
        public ?string $paymentTerms,
        public array $items,
        public float $tax,
        public ?string $deliveryReceiptNo,
        public ?string $salesInvoiceNo,
        public ?string $notes,
        public float $subtotal,
        public float $total,
        public ?string $receivedAt,
        public bool $isClosed,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel(PurchaseOrder $purchaseOrder, array $context = []): self
    {
        return new self(
            id: $purchaseOrder->id,
            poNumber: $purchaseOrder->po_number,
            supplierId: $purchaseOrder->supplier_id,
            supplierName: $purchaseOrder->supplier?->name,
            status: (string) $purchaseOrder->status,
            statusLabel: self::option(['draft' => 'Draft', 'sent' => 'Sent', 'cancelled' => 'Cancelled'], $purchaseOrder->status),
            expectedDeliveryDate: self::date($purchaseOrder->expected_delivery_date),
            paymentTerms: $purchaseOrder->payment_terms,
            items: $purchaseOrder->items->map(fn ($row) => ['id' => $row->id, 'ingredient_id' => $row->ingredient_id, 'qty_ordered' => $row->qty_ordered, 'unit_cost' => $row->unit_cost])->values()->all(),
            tax: (float) $purchaseOrder->tax,
            deliveryReceiptNo: $purchaseOrder->delivery_receipt_no,
            salesInvoiceNo: $purchaseOrder->sales_invoice_no,
            notes: $purchaseOrder->notes,
            subtotal: (float) $purchaseOrder->subtotal,
            total: (float) $purchaseOrder->total,
            receivedAt: $purchaseOrder->received_at?->toIso8601String(),
            isClosed: in_array($purchaseOrder->status, ['received', 'cancelled'], true),
            createdAt: $purchaseOrder->created_at?->toIso8601String(),
            updatedAt: $purchaseOrder->updated_at?->toIso8601String(),
        );
    }

    private static function date(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        return $value instanceof \DateTimeInterface ? $value->format('Y-m-d') : substr((string) $value, 0, 10);
    }

    /**
     * @param  array<string, string>  $options
     */
    private static function option(array $options, mixed $value): ?string
    {
        return $value === null ? null : ($options[(string) $value] ?? (string) $value);
    }
}
