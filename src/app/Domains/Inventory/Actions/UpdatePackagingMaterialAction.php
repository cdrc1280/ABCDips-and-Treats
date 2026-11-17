<?php

namespace App\Domains\Inventory\Actions;

use App\Domains\Inventory\Data\PackagingMaterialData;
use App\Domains\Inventory\Models\PackagingMaterial;
use Illuminate\Support\Facades\DB;

final class UpdatePackagingMaterialAction
{
    public function execute(PackagingMaterial $packagingMaterial, PackagingMaterialData $data): PackagingMaterial
    {
        return DB::transaction(function () use ($packagingMaterial, $data) {
            $packagingMaterial->update([
                'name' => $data->name,
                'sku' => $data->sku,
                'type' => $data->type,
                'unit' => $data->unit,
                'cost_per_unit' => $data->costPerUnit,
                'stock_qty' => $data->stockQty,
                'min_stock_qty' => $data->minStockQty,
                'is_active' => $data->isActive,
                'notes' => $data->notes,
            ]);

            return $packagingMaterial;
        });
    }
}
