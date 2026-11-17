<?php

namespace App\Domains\Purchasing\Actions;

use App\Domains\Purchasing\Models\PurchaseOrder;
use App\Domains\Purchasing\Models\Quotation;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\DB;

/** Convert to PO. */
final class ConvertQuotationAction
{
    public function execute(Quotation $quotation): Quotation
    {
        if ($quotation->status === 'converted_to_po') {
            throw new DomainRuleViolation(__('This quotation was already converted.'));
        }
        if (! $quotation->supplier_id) {
            throw new DomainRuleViolation(__('Pick a supplier on the quotation before converting it.'));
        }
        if ($quotation->items()->count() === 0 || $quotation->items()->whereNull('ingredient_id')->exists()) {
            throw new DomainRuleViolation(__('Every item needs an ingredient before it can go on a purchase order.'));
        }

        DB::transaction(function () use ($quotation) {
            $po = PurchaseOrder::query()->create([
                'supplier_id' => $quotation->supplier_id,
                'quotation_id' => $quotation->id,
                'status' => PurchaseOrder::STATUS_DRAFT,
                'subtotal' => $quotation->subtotal,
                'tax' => $quotation->tax,
                'total' => $quotation->total,
                'notes' => "Generated from quotation {$quotation->quotation_number}",
            ]);
            foreach ($quotation->items as $item) {
                $po->items()->create([
                    'ingredient_id' => $item->ingredient_id,
                    'qty_ordered' => $item->qty,
                    'qty_received' => 0,
                    'unit_cost' => $item->unit_price,
                    'subtotal' => round((float) $item->qty * (float) $item->unit_price, 2),
                ]);
            }
            $quotation->update(['status' => 'converted_to_po']);
        });

        return $quotation->refresh();
    }
}
