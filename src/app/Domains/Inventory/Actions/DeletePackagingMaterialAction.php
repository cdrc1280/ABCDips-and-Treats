<?php

namespace App\Domains\Inventory\Actions;

use App\Domains\Inventory\Models\PackagingMaterial;
use Illuminate\Support\Facades\DB;

final class DeletePackagingMaterialAction
{
    public function execute(PackagingMaterial $packagingMaterial): void
    {
        DB::transaction(fn () => $packagingMaterial->delete());
    }
}
