<?php

namespace App\Domains\Purchasing\Actions;

use App\Domains\Purchasing\Data\QuotationData;
use App\Domains\Purchasing\Models\Quotation;
use Illuminate\Support\Facades\DB;

final class CreateQuotationAction
{
    public function execute(QuotationData $data): Quotation
    {
        return DB::transaction(function () use ($data) {
            $quotation = Quotation::query()->create([
                'quotation_number' => $data->quotationNumber,
                'supplier_id' => $data->supplierId,
                'client_name' => $data->clientName,
                'status' => $data->status,
                'quotation_date' => $data->quotationDate,
                'valid_until' => $data->validUntil,
                'tax' => $data->tax,
                'notes' => $data->notes,
            ]);
            $quotation->items()->delete();
            foreach ($data->items as $row) {
                $quotation->items()->create(['ingredient_id' => isset($row['ingredient_id']) && $row['ingredient_id'] !== '' ? (string) $row['ingredient_id'] : null, 'item_description' => isset($row['item_description']) && $row['item_description'] !== '' ? (string) $row['item_description'] : null, 'qty' => isset($row['qty']) && $row['qty'] !== '' ? (float) $row['qty'] : null, 'unit' => isset($row['unit']) && $row['unit'] !== '' ? (string) $row['unit'] : null, 'unit_price' => isset($row['unit_price']) && $row['unit_price'] !== '' ? (float) $row['unit_price'] : null, 'subtotal' => round((float) ($row['qty'] ?? 0) * (float) ($row['unit_price'] ?? 0), 2)]);
            }
            $quotation->update(['subtotal' => round((float) $quotation->items()->sum('subtotal'), 2), 'total' => round((float) $quotation->items()->sum('subtotal') + (float) $quotation->tax, 2)]);

            return $quotation;
        });
    }
}
