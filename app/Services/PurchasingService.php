<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Str;

class PurchasingService
{
    public function __construct(
        private readonly InventoryService $inventoryService
    ) {}

    public function createPurchaseOrder(
        Supplier $supplier,
        array $items,
        ?\DateTimeInterface $expectedDeliveryDate = null,
        ?User $user = null,
        ?string $notes = null
    ): PurchaseOrder {
        $poNumber = 'PO-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $subtotal = 0.0;

        $po = PurchaseOrder::create([
            'po_number'              => $poNumber,
            'supplier_id'            => $supplier->id,
            'status'                 => PurchaseOrder::STATUS_DRAFT,
            'expected_delivery_date' => $expectedDeliveryDate,
            'created_by_user_id'     => $user?->id,
            'notes'                  => $notes,
        ]);

        foreach ($items as $itemData) {
            $ingredient = Ingredient::findOrFail($itemData['ingredient_id']);
            $qty = (float) $itemData['qty_ordered'];
            $unitCost = (float) ($itemData['unit_cost'] ?? $ingredient->cost_per_unit);
            $lineSub = round($qty * $unitCost, 2);
            $subtotal += $lineSub;

            PurchaseOrderItem::create([
                'purchase_order_id' => $po->id,
                'ingredient_id'     => $ingredient->id,
                'qty_ordered'       => $qty,
                'qty_received'      => 0,
                'unit_cost'         => $unitCost,
                'subtotal'          => $lineSub,
            ]);
        }

        $tax = round($subtotal * 0.12, 2); // 12% VAT
        $total = round($subtotal + $tax, 2);

        $po->update([
            'subtotal' => $subtotal,
            'tax'      => $tax,
            'total'    => $total,
        ]);

        return $po->fresh(['supplier', 'items.ingredient']);
    }

    public function receivePurchaseOrder(PurchaseOrder $po, ?User $user = null): PurchaseOrder
    {
        if ($po->status === PurchaseOrder::STATUS_RECEIVED) {
            return $po;
        }

        $po->load(['items.ingredient']);

        foreach ($po->items as $item) {
            $qtyReceived = $item->qty_ordered;
            $item->update(['qty_received' => $qtyReceived]);

            if ($item->ingredient) {
                $this->inventoryService->addStockMovement(
                    $item->ingredient,
                    'purchase',
                    (float) $qtyReceived,
                    (float) $item->unit_cost,
                    "Received PO #{$po->po_number} from {$po->supplier->name}",
                    $user
                );
            }
        }

        $po->update([
            'status'      => PurchaseOrder::STATUS_RECEIVED,
            'received_at' => now(),
        ]);

        return $po->fresh();
    }
}
