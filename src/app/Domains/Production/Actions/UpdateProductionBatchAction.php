<?php

namespace App\Domains\Production\Actions;

use App\Domains\Production\Data\ProductionBatchData;
use App\Domains\Production\Models\ProductionBatch;
use Illuminate\Support\Facades\DB;

final class UpdateProductionBatchAction
{
    public function execute(ProductionBatch $productionBatch, ProductionBatchData $data): ProductionBatch
    {
        return DB::transaction(function () use ($productionBatch, $data) {
            $productionBatch->update([
                'batch_number' => $data->batchNumber ?? $productionBatch->batch_number,
                'recipe_id' => $data->recipeId,
                'product_id' => $data->productId,
                'planned_qty' => $data->plannedQty,
                'actual_yield_qty' => $data->actualYieldQty,
                'status' => $data->status,
                'baker_user_id' => $data->bakerUserId,
                'notes' => $data->notes,
            ]);

            return $productionBatch;
        });
    }
}
