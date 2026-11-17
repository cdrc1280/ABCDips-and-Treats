<?php

namespace App\Domains\Inventory\Actions;

use App\Domains\Inventory\Models\PackagingMaterial;
use App\Domains\Inventory\Models\PackagingMovement;
use Illuminate\Support\Facades\DB;

/** Add stock. */
final class RestockPackagingMaterialAction
{
    public function execute(PackagingMaterial $packagingMaterial, mixed $value): PackagingMaterial
    {
        DB::transaction(function () use ($packagingMaterial, $value) {
            PackagingMovement::query()->create([
                'packaging_material_id' => $packagingMaterial->id,
                'type' => 'in',
                'qty' => (float) $value,
                'unit_cost' => $packagingMaterial->cost_per_unit,
                'notes' => 'Restocked from the back office',
                'user_id' => auth()->id(),
            ]);
            $packagingMaterial->increment('stock_qty', (float) $value);
        });

        return $packagingMaterial->refresh();
    }
}
