<?php

namespace App\Domains\Sales\Actions;

use App\Domains\Sales\Models\CustomOrder;
use App\Services\InventoryDeductionService;
use Illuminate\Support\Facades\DB;

/** Start production. */
final class StartProductionCustomOrderAction
{
    public function execute(CustomOrder $customOrder): CustomOrder
    {
        DB::transaction(function () use ($customOrder) {
            app(InventoryDeductionService::class)->deductForCustomOrder($customOrder);
            $customOrder->update(['status' => 'in_production']);
        });

        return $customOrder->refresh();
    }
}
