<?php

namespace App\Domains\Purchasing\Resources;

use App\Domains\Purchasing\Models\Quotation;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class QuotationResource extends Resource
{
    public function __construct(
        public string $id,
        public ?string $quotationNumber,
        public ?string $supplierId,
        public ?string $supplierName,
        public ?string $clientName,
        public string $status,
        public ?string $statusLabel,
        public ?string $quotationDate,
        public ?string $validUntil,
        public array $items,
        public float $tax,
        public ?string $notes,
        public float $subtotal,
        public float $total,
        public bool $isConverted,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel(Quotation $quotation, array $context = []): self
    {
        return new self(
            id: $quotation->id,
            quotationNumber: $quotation->quotation_number,
            supplierId: $quotation->supplier_id === null ? null : $quotation->supplier_id,
            supplierName: $quotation->supplier?->name,
            clientName: $quotation->client_name,
            status: (string) $quotation->status,
            statusLabel: self::option(['draft' => 'Draft', 'sent' => 'Sent', 'accepted' => 'Accepted', 'rejected' => 'Rejected', 'converted_to_po' => 'Converted to PO'], $quotation->status),
            quotationDate: self::date($quotation->quotation_date),
            validUntil: self::date($quotation->valid_until),
            items: $quotation->items->map(fn ($row) => ['id' => $row->id, 'ingredient_id' => $row->ingredient_id, 'item_description' => $row->item_description, 'qty' => $row->qty, 'unit' => $row->unit, 'unit_price' => $row->unit_price])->values()->all(),
            tax: (float) $quotation->tax,
            notes: $quotation->notes,
            subtotal: (float) $quotation->subtotal,
            total: (float) $quotation->total,
            isConverted: $quotation->status === 'converted_to_po',
            createdAt: $quotation->created_at?->toIso8601String(),
            updatedAt: $quotation->updated_at?->toIso8601String(),
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
