<?php

namespace App\Domains\Production\Actions;

use App\Domains\Production\Data\ProductionBatchData;
use App\Domains\Production\Models\ProductionBatch;
use Illuminate\Support\Facades\DB;

final class CreateProductionBatchAction
{
    public function execute(ProductionBatchData $data): ProductionBatch
    {
        return DB::transaction(function () use ($data) {
            $productionBatch = ProductionBatch::query()->create([
                'batch_number' => $data->batchNumber ?? 'PB-'.now()->format('Ymd-His'),
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
