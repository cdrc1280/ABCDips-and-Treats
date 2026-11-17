<?php

namespace App\Domains\Purchasing\Actions;

use App\Domains\Purchasing\Data\PurchaseOrderData;
use App\Domains\Purchasing\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;

final class CreatePurchaseOrderAction
{
    public function execute(PurchaseOrderData $data): PurchaseOrder
    {
        return DB::transaction(function () use ($data) {
            $purchaseOrder = PurchaseOrder::query()->create([
                'po_number' => $data->poNumber,
                'supplier_id' => $data->supplierId,
                'status' => $data->status,
                'expected_delivery_date' => $data->expectedDeliveryDate,
                'payment_terms' => $data->paymentTerms,
                'tax' => $data->tax,
                'delivery_receipt_no' => $data->deliveryReceiptNo,
                'sales_invoice_no' => $data->salesInvoiceNo,
                'notes' => $data->notes,
            ]);
            $purchaseOrder->items()->delete();
            foreach ($data->items as $row) {
                $purchaseOrder->items()->create(['ingredient_id' => isset($row['ingredient_id']) && $row['ingredient_id'] !== '' ? (string) $row['ingredient_id'] : null, 'qty_ordered' => isset($row['qty_ordered']) && $row['qty_ordered'] !== '' ? (float) $row['qty_ordered'] : null, 'unit_cost' => isset($row['unit_cost']) && $row['unit_cost'] !== '' ? (float) $row['unit_cost'] : null, 'subtotal' => round((float) ($row['qty_ordered'] ?? 0) * (float) ($row['unit_cost'] ?? 0), 2), 'qty_received' => 0]);
            }
            $purchaseOrder->update(['subtotal' => round((float) $purchaseOrder->items()->sum('subtotal'), 2), 'total' => round((float) $purchaseOrder->items()->sum('subtotal') + (float) $purchaseOrder->tax, 2)]);

            return $purchaseOrder;
        });
    }
}
