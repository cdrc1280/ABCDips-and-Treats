<?php

namespace App\Domains\Inventory\Actions;

use App\Domains\Inventory\Data\IngredientData;
use App\Domains\Inventory\Models\Ingredient;
use Illuminate\Support\Facades\DB;

final class UpdateIngredientAction
{
    public function execute(Ingredient $ingredient, IngredientData $data): Ingredient
    {
        return DB::transaction(function () use ($ingredient, $data) {
            $ingredient->update([
                'name' => $data->name,
                'sku' => $data->sku,
                'supplier_id' => $data->supplierId,
                'unit' => $data->unit,
                'item_unit' => $data->itemUnit,
                'item_price' => $data->itemPrice,
                'stock_qty' => $data->stockQty,
                'min_stock_qty' => $data->minStockQty,
                'reorder_qty' => $data->reorderQty,
            ]);

            return $ingredient;
        });
    }
}
