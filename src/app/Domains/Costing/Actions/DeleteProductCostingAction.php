<?php

namespace App\Domains\Costing\Actions;

use App\Domains\Costing\Models\ProductCosting;
use Illuminate\Support\Facades\DB;

final class DeleteProductCostingAction
{
    public function execute(ProductCosting $productCosting): void
    {
        DB::transaction(fn () => $productCosting->delete());
    }
}
