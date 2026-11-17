<?php

namespace App\Domains\Inventory\Actions;

use App\Domains\Inventory\Models\Supplier;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\DB;

final class DeleteSupplierAction
{
    public function execute(Supplier $supplier): void
    {
        if ($supplier->ingredients()->exists() || $supplier->purchaseOrders()->exists()) {
            throw new DomainRuleViolation(__(':name is still linked to ingredients or purchase orders.', ['name' => $supplier->name]));
        }

        DB::transaction(fn () => $supplier->delete());
    }
}
