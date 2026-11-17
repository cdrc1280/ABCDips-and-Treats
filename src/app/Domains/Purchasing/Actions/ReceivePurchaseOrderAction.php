<?php

namespace App\Domains\Purchasing\Actions;

use App\Domains\Purchasing\Models\PurchaseOrder;
use App\Services\PurchasingService;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\DB;

/** Receive. */
final class ReceivePurchaseOrderAction
{
    public function execute(PurchaseOrder $purchaseOrder): PurchaseOrder
    {
        if (in_array($purchaseOrder->status, ['received', 'cancelled'], true)) {
            throw new DomainRuleViolation(__('Only open purchase orders can be received.'));
        }

        DB::transaction(function () use ($purchaseOrder) {
            app(PurchasingService::class)->receivePurchaseOrder($purchaseOrder, auth()->user());
        });

        return $purchaseOrder->refresh();
    }
}
