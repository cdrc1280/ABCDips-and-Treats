<?php

namespace App\Domains\Sales\Actions;

use App\Domains\Sales\Models\CustomOrder;
use Illuminate\Support\Facades\DB;

final class DeleteCustomOrderAction
{
    public function execute(CustomOrder $customOrder): void
    {
        DB::transaction(fn () => $customOrder->delete());
    }
}
