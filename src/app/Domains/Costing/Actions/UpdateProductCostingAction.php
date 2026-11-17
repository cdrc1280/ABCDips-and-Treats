<?php

namespace App\Domains\Costing\Actions;

use App\Domains\Costing\Data\ProductCostingData;
use App\Domains\Costing\Models\ProductCosting;
use Illuminate\Support\Facades\DB;

final class UpdateProductCostingAction
{
    public function execute(ProductCosting $productCosting, ProductCostingData $data): ProductCosting
    {
        return DB::transaction(function () use ($productCosting, $data) {
            $productCosting->update([
                'name' => $data->name,
                'product_id' => $data->productId,
                'yield_qty' => $data->yieldQty,
                'yield_unit' => $data->yieldUnit,
                'overhead_pct' => $data->overheadPct,
                'markup_pct' => $data->markupPct,
                'labor_pct' => $data->laborPct,
            ]);
            $productCosting->costingItems()->delete();
            foreach ($data->items as $row) {
                $productCosting->costingItems()->create(['group' => isset($row['group']) && $row['group'] !== '' ? (string) $row['group'] : null, 'ingredient_id' => isset($row['ingredient_id']) && $row['ingredient_id'] !== '' ? (string) $row['ingredient_id'] : null, 'packaging_material_id' => isset($row['packaging_material_id']) && $row['packaging_material_id'] !== '' ? (string) $row['packaging_material_id'] : null, 'name' => isset($row['name']) && $row['name'] !== '' ? (string) $row['name'] : null, 'unit' => isset($row['unit']) && $row['unit'] !== '' ? (string) $row['unit'] : null, 'package_amount' => isset($row['package_amount']) && $row['package_amount'] !== '' ? (float) $row['package_amount'] : null, 'package_price' => isset($row['package_price']) && $row['package_price'] !== '' ? (float) $row['package_price'] : null, 'qty_used' => isset($row['qty_used']) && $row['qty_used'] !== '' ? (float) $row['qty_used'] : null]);
            }

            return $productCosting;
        });
    }
}
