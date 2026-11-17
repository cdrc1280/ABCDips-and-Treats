<?php

namespace App\Domains\Purchasing\Actions;

use App\Domains\Purchasing\Models\PurchaseOrder;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\DB;

final class DeletePurchaseOrderAction
{
    public function execute(PurchaseOrder $purchaseOrder): void
    {
        if ($purchaseOrder->status === 'received') {
            throw new DomainRuleViolation(__('Received purchase orders already moved stock and cannot be deleted.'));
        }

        DB::transaction(fn () => $purchaseOrder->delete());
    }
}
