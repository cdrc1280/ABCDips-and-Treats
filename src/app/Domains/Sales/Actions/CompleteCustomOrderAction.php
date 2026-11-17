<?php

namespace App\Domains\Sales\Actions;

use App\Domains\Sales\Models\CustomOrder;
use Illuminate\Support\Facades\DB;

/** Mark completed. */
final class CompleteCustomOrderAction
{
    public function execute(CustomOrder $customOrder): CustomOrder
    {
        DB::transaction(function () use ($customOrder) {
            $customOrder->update(['status' => 'completed']);
        });

        return $customOrder->refresh();
    }
}
