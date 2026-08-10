<?php

namespace App\Services;

use App\Models\CustomOrder;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\PackagingMaterial;
use App\Models\PackagingMovement;
use App\Models\ProductCosting;
use App\Models\Recipe;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class InventoryDeductionService
{
    /**
     * Check if there is sufficient raw ingredient and packaging stock for an order.
     * Throws an Exception if any stock is insufficient.
     */
    public function checkStockAvailability(Order $order): void
    {
        $order->load(['items.product']);
        $requiredIngredients = [];
        $requiredPackagings   = [];

        foreach ($order->items as $item) {
            if (! $item->product_id || ! empty($item->options['is_custom'])) {
                continue;
            }

            $recipe = Recipe::where('product_id', $item->product_id)
                ->with(['recipeIngredients.ingredient', 'recipePackagings.packagingMaterial'])
                ->first();

            if ($recipe) {
                $yield = max(1.0, (float) $recipe->yield_qty);
                $scaleFactor = ((float) $item->qty) / $yield;

                foreach ($recipe->recipeIngredients as $ri) {
                    if ($ri->ingredient) {
                        $qtyNeeded = (float) $ri->qty_required * $scaleFactor;
                        $deductQty = UnitConverterService::convert($qtyNeeded, $ri->unit, $ri->ingredient->unit);
                        $id = $ri->ingredient_id;
                        if (! isset($requiredIngredients[$id])) {
                            $requiredIngredients[$id] = [
                                'name'      => $ri->ingredient->name,
                                'unit'      => $ri->ingredient->unit,
                                'required'  => 0.0,
                                'available' => (float) $ri->ingredient->stock_qty,
                            ];
                        }
                        $requiredIngredients[$id]['required'] += $deductQty;
                    }
                }

                foreach ($recipe->recipePackagings as $rp) {
                    if ($rp->packagingMaterial) {
                        $qtyNeeded = (float) $rp->qty_used * $scaleFactor;
                        $deductQty = UnitConverterService::convert($qtyNeeded, $rp->unit, $rp->packagingMaterial->unit);
                        $id = $rp->packaging_material_id;
                        if (! isset($requiredPackagings[$id])) {
                            $requiredPackagings[$id] = [
                                'name'      => $rp->packagingMaterial->name,
                                'unit'      => $rp->packagingMaterial->unit,
                                'required'  => 0.0,
                                'available' => (float) $rp->packagingMaterial->stock_qty,
                            ];
                        }
                        $requiredPackagings[$id]['required'] += $deductQty;
                    }
                }
                continue;
            }

            $costing = ProductCosting::where('product_id', $item->product_id)
                ->with(['costingItems.ingredient', 'costingItems.packagingMaterial'])
                ->first();

            if ($costing) {
                $yield = max(1.0, (float) $costing->yield_qty);
                $scaleFactor = ((float) $item->qty) / $yield;

                foreach ($costing->costingItems as $ci) {
                    $qtyNeeded = (float) $ci->qty_used * $scaleFactor;
                    if ($ci->group === 'ingredient' && $ci->ingredient) {
                        $deductQty = UnitConverterService::convert($qtyNeeded, $ci->unit, $ci->ingredient->unit);
                        $id = $ci->ingredient_id;
                        if (! isset($requiredIngredients[$id])) {
                            $requiredIngredients[$id] = [
                                'name'      => $ci->ingredient->name,
                                'unit'      => $ci->ingredient->unit,
                                'required'  => 0.0,
                                'available' => (float) $ci->ingredient->stock_qty,
                            ];
                        }
                        $requiredIngredients[$id]['required'] += $deductQty;
                    } elseif ($ci->group === 'packaging' && $ci->packagingMaterial) {
                        $deductQty = UnitConverterService::convert($qtyNeeded, $ci->unit, $ci->packagingMaterial->unit);
                        $id = $ci->packaging_material_id;
                        if (! isset($requiredPackagings[$id])) {
                            $requiredPackagings[$id] = [
                                'name'      => $ci->packagingMaterial->name,
                                'unit'      => $ci->packagingMaterial->unit,
                                'required'  => 0.0,
                                'available' => (float) $ci->packagingMaterial->stock_qty,
                            ];
                        }
                        $requiredPackagings[$id]['required'] += $deductQty;
                    }
                }
            }
        }

        foreach ($requiredIngredients as $ing) {
            if ($ing['available'] < $ing['required']) {
                $reqFormatted = number_format($ing['required'], 2);
                $availFormatted = number_format($ing['available'], 2);
                throw new \Exception("Insufficient stock for raw ingredient '{$ing['name']}'. Required: {$reqFormatted} {$ing['unit']}, Available: {$availFormatted} {$ing['unit']}. Please restock before placing/processing this order.");
            }
        }

        foreach ($requiredPackagings as $pkg) {
            if ($pkg['available'] < $pkg['required']) {
                $reqFormatted = number_format($pkg['required'], 2);
                $availFormatted = number_format($pkg['available'], 2);
                throw new \Exception("Insufficient stock for packaging material '{$pkg['name']}'. Required: {$reqFormatted} {$pkg['unit']}, Available: {$availFormatted} {$pkg['unit']}. Please restock before placing/processing this order.");
            }
        }
    }

    /**
     * Deduct raw ingredients and packaging materials in real time for an order.
     */
    public function deductForOrder(Order $order): void
    {
        $this->checkStockAvailability($order);

        DB::transaction(function () use ($order) {
            $order->load(['items.product']);

            foreach ($order->items as $item) {
                if (! $item->product_id || ! empty($item->options['is_custom'])) {
                    continue;
                }

                $recipe = Recipe::where('product_id', $item->product_id)
                    ->with(['recipeIngredients.ingredient', 'recipePackagings.packagingMaterial'])
                    ->first();

                if ($recipe) {
                    $this->deductFromRecipe($recipe, (float) $item->qty, $order, $item->product_name);
                    continue;
                }

                $costing = ProductCosting::where('product_id', $item->product_id)
                    ->with(['costingItems.ingredient', 'costingItems.packagingMaterial'])
                    ->first();

                if ($costing) {
                    $this->deductFromCosting($costing, (float) $item->qty, $order, $item->product_name);
                }
            }
        });
    }

    /**
     * Restore ingredients and packaging stock when an order is cancelled.
     */
    public function revertForOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->load(['items.product']);

            foreach ($order->items as $item) {
                if (! $item->product_id || ! empty($item->options['is_custom'])) {
                    continue;
                }

                $recipe = Recipe::where('product_id', $item->product_id)
                    ->with(['recipeIngredients.ingredient', 'recipePackagings.packagingMaterial'])
                    ->first();

                if ($recipe) {
                    $this->revertFromRecipe($recipe, (float) $item->qty, $order, $item->product_name);
                    continue;
                }

                $costing = ProductCosting::where('product_id', $item->product_id)
                    ->with(['costingItems.ingredient', 'costingItems.packagingMaterial'])
                    ->first();

                if ($costing) {
                    $this->revertFromCosting($costing, (float) $item->qty, $order, $item->product_name);
                }
            }
        });
    }

    private function deductFromRecipe(Recipe $recipe, float $orderQty, Order $order, string $productName): void
    {
        $yield = max(1.0, (float) $recipe->yield_qty);
        $scaleFactor = $orderQty / $yield;

        foreach ($recipe->recipeIngredients as $ri) {
            if ($ri->ingredient) {
                $qtyNeeded  = (float) $ri->qty_required * $scaleFactor;
                $deductQty  = UnitConverterService::convert($qtyNeeded, $ri->unit, $ri->ingredient->unit);
                $ingredient = Ingredient::where('id', $ri->ingredient_id)->lockForUpdate()->first();

                if ($ingredient && $deductQty > 0) {
                    $ingredient->decrement('stock_qty', $deductQty);

                    StockMovement::create([
                        'ingredient_id'      => $ingredient->id,
                        'type'               => 'production_usage',
                        'qty'                => -$deductQty,
                        'unit_cost'          => $ingredient->cost_per_unit,
                        'notes'              => "Auto-deducted for Order #{$order->order_number} ({$orderQty}x {$productName})",
                        'created_by_user_id' => $order->user_id,
                    ]);
                }
            }
        }

        foreach ($recipe->recipePackagings as $rp) {
            if ($rp->packagingMaterial) {
                $qtyNeeded = (float) $rp->qty_used * $scaleFactor;
                $deductQty = UnitConverterService::convert($qtyNeeded, $rp->unit, $rp->packagingMaterial->unit);
                $pm        = PackagingMaterial::where('id', $rp->packaging_material_id)->lockForUpdate()->first();

                if ($pm && $deductQty > 0) {
                    $pm->decrement('stock_qty', $deductQty);

                    PackagingMovement::create([
                        'packaging_material_id' => $pm->id,
                        'type'                  => 'out',
                        'qty'                   => -$deductQty,
                        'unit_cost'             => $pm->cost_per_unit,
                        'reference_type'        => Order::class,
                        'reference_id'          => $order->id,
                        'notes'                 => "Auto-deducted packaging for Order #{$order->order_number} ({$orderQty}x {$productName})",
                        'user_id'               => $order->user_id,
                    ]);
                }
            }
        }
    }

    private function deductFromCosting(ProductCosting $costing, float $orderQty, Order $order, string $productName): void
    {
        $yield = max(1.0, (float) $costing->yield_qty);
        $scaleFactor = $orderQty / $yield;

        foreach ($costing->costingItems as $ci) {
            $qtyNeeded = (float) $ci->qty_used * $scaleFactor;

            if ($ci->group === 'ingredient' && $ci->ingredient) {
                $deductQty  = UnitConverterService::convert($qtyNeeded, $ci->unit, $ci->ingredient->unit);
                $ingredient = Ingredient::where('id', $ci->ingredient_id)->lockForUpdate()->first();

                if ($ingredient && $deductQty > 0) {
                    $ingredient->decrement('stock_qty', $deductQty);

                    StockMovement::create([
                        'ingredient_id'      => $ingredient->id,
                        'type'               => 'production_usage',
                        'qty'                => -$deductQty,
                        'unit_cost'          => $ingredient->cost_per_unit,
                        'notes'              => "Auto-deducted for Order #{$order->order_number} ({$orderQty}x {$productName})",
                        'created_by_user_id' => $order->user_id,
                    ]);
                }
            } elseif ($ci->group === 'packaging' && $ci->packagingMaterial) {
                $deductQty = UnitConverterService::convert($qtyNeeded, $ci->unit, $ci->packagingMaterial->unit);
                $pm        = PackagingMaterial::where('id', $ci->packaging_material_id)->lockForUpdate()->first();

                if ($pm && $deductQty > 0) {
                    $pm->decrement('stock_qty', $deductQty);

                    PackagingMovement::create([
                        'packaging_material_id' => $pm->id,
                        'type'                  => 'out',
                        'qty'                   => -$deductQty,
                        'unit_cost'             => $pm->cost_per_unit,
                        'reference_type'        => Order::class,
                        'reference_id'          => $order->id,
                        'notes'                 => "Auto-deducted packaging for Order #{$order->order_number} ({$orderQty}x {$productName})",
                        'user_id'               => $order->user_id,
                    ]);
                }
            }
        }
    }

    private function revertFromRecipe(Recipe $recipe, float $orderQty, Order $order, string $productName): void
    {
        $yield = max(1.0, (float) $recipe->yield_qty);
        $scaleFactor = $orderQty / $yield;

        foreach ($recipe->recipeIngredients as $ri) {
            if ($ri->ingredient) {
                $qtyNeeded  = (float) $ri->qty_required * $scaleFactor;
                $restoreQty = UnitConverterService::convert($qtyNeeded, $ri->unit, $ri->ingredient->unit);
                $ingredient = Ingredient::where('id', $ri->ingredient_id)->lockForUpdate()->first();

                if ($ingredient && $restoreQty > 0) {
                    $ingredient->increment('stock_qty', $restoreQty);

                    StockMovement::create([
                        'ingredient_id'      => $ingredient->id,
                        'type'               => 'adjustment',
                        'qty'                => $restoreQty,
                        'unit_cost'          => $ingredient->cost_per_unit,
                        'notes'              => "Restored stock from cancelled Order #{$order->order_number} ({$orderQty}x {$productName})",
                        'created_by_user_id' => $order->user_id,
                    ]);
                }
            }
        }

        foreach ($recipe->recipePackagings as $rp) {
            if ($rp->packagingMaterial) {
                $qtyNeeded  = (float) $rp->qty_used * $scaleFactor;
                $restoreQty = UnitConverterService::convert($qtyNeeded, $rp->unit, $rp->packagingMaterial->unit);
                $pm         = PackagingMaterial::where('id', $rp->packaging_material_id)->lockForUpdate()->first();

                if ($pm && $restoreQty > 0) {
                    $pm->increment('stock_qty', $restoreQty);

                    PackagingMovement::create([
                        'packaging_material_id' => $pm->id,
                        'type'                  => 'adjustment',
                        'qty'                   => $restoreQty,
                        'unit_cost'             => $pm->cost_per_unit,
                        'reference_type'        => Order::class,
                        'reference_id'          => $order->id,
                        'notes'                 => "Restored packaging stock from cancelled Order #{$order->order_number} ({$orderQty}x {$productName})",
                        'user_id'               => $order->user_id,
                    ]);
                }
            }
        }
    }

    private function revertFromCosting(ProductCosting $costing, float $orderQty, Order $order, string $productName): void
    {
        $yield = max(1.0, (float) $costing->yield_qty);
        $scaleFactor = $orderQty / $yield;

        foreach ($costing->costingItems as $ci) {
            $qtyNeeded = (float) $ci->qty_used * $scaleFactor;

            if ($ci->group === 'ingredient' && $ci->ingredient) {
                $restoreQty = UnitConverterService::convert($qtyNeeded, $ci->unit, $ci->ingredient->unit);
                $ingredient = Ingredient::where('id', $ci->ingredient_id)->lockForUpdate()->first();

                if ($ingredient && $restoreQty > 0) {
                    $ingredient->increment('stock_qty', $restoreQty);

                    StockMovement::create([
                        'ingredient_id'      => $ingredient->id,
                        'type'               => 'adjustment',
                        'qty'                => $restoreQty,
                        'unit_cost'          => $ingredient->cost_per_unit,
                        'notes'              => "Restored stock from cancelled Order #{$order->order_number} ({$orderQty}x {$productName})",
                        'created_by_user_id' => $order->user_id,
                    ]);
                }
            } elseif ($ci->group === 'packaging' && $ci->packagingMaterial) {
                $restoreQty = UnitConverterService::convert($qtyNeeded, $ci->unit, $ci->packagingMaterial->unit);
                $pm         = PackagingMaterial::where('id', $ci->packaging_material_id)->lockForUpdate()->first();

                if ($pm && $restoreQty > 0) {
                    $pm->increment('stock_qty', $restoreQty);

                    PackagingMovement::create([
                        'packaging_material_id' => $pm->id,
                        'type'                  => 'adjustment',
                        'qty'                   => $restoreQty,
                        'unit_cost'             => $pm->cost_per_unit,
                        'reference_type'        => Order::class,
                        'reference_id'          => $order->id,
                        'notes'                 => "Restored packaging stock from cancelled Order #{$order->order_number} ({$orderQty}x {$productName})",
                        'user_id'               => $order->user_id,
                    ]);
                }
            }
        }
    }

    /**
     * Deduct raw ingredients and packaging materials for a custom cake/pastry order.
     */
    public function deductForCustomOrder(CustomOrder $customOrder): void
    {
        DB::transaction(function () use ($customOrder) {
            $recipe = Recipe::whereHas('product', function ($q) {
                $q->where('name', 'like', '%cake%')->orWhere('name', 'like', '%bread%');
            })->with(['recipeIngredients.ingredient', 'recipePackagings.packagingMaterial'])->first();

            if (! $recipe) {
                $recipe = Recipe::with(['recipeIngredients.ingredient', 'recipePackagings.packagingMaterial'])->first();
            }

            if (! $recipe) {
                return;
            }

            $servings = max(15.0, (float) ($customOrder->servings_count ?? 15));
            $yield = max(1.0, (float) $recipe->yield_qty);
            $scaleFactor = $servings / $yield;

            // Check stock availability
            foreach ($recipe->recipeIngredients as $ri) {
                if ($ri->ingredient) {
                    $qtyNeeded = (float) $ri->qty_required * $scaleFactor;
                    $deductQty = UnitConverterService::convert($qtyNeeded, $ri->unit, $ri->ingredient->unit);
                    if ($ri->ingredient->stock_qty < $deductQty) {
                        $reqFmt = number_format($deductQty, 2);
                        $availFmt = number_format($ri->ingredient->stock_qty, 2);
                        throw new \Exception("Insufficient stock for raw ingredient '{$ri->ingredient->name}' for Custom Order #{$customOrder->reference_number}. Required: {$reqFmt} {$ri->ingredient->unit}, Available: {$availFmt} {$ri->ingredient->unit}. Please restock.");
                    }
                }
            }

            foreach ($recipe->recipePackagings as $rp) {
                if ($rp->packagingMaterial) {
                    $qtyNeeded = (float) $rp->qty_used * $scaleFactor;
                    $deductQty = UnitConverterService::convert($qtyNeeded, $rp->unit, $rp->packagingMaterial->unit);
                    if ($rp->packagingMaterial->stock_qty < $deductQty) {
                        $reqFmt = number_format($deductQty, 2);
                        $availFmt = number_format($rp->packagingMaterial->stock_qty, 2);
                        throw new \Exception("Insufficient stock for packaging material '{$rp->packagingMaterial->name}' for Custom Order #{$customOrder->reference_number}. Required: {$reqFmt} {$rp->packagingMaterial->unit}, Available: {$availFmt} {$rp->packagingMaterial->unit}. Please restock.");
                    }
                }
            }

            // Perform decrements
            foreach ($recipe->recipeIngredients as $ri) {
                if ($ri->ingredient) {
                    $qtyNeeded  = (float) $ri->qty_required * $scaleFactor;
                    $deductQty  = UnitConverterService::convert($qtyNeeded, $ri->unit, $ri->ingredient->unit);
                    $ingredient = Ingredient::where('id', $ri->ingredient_id)->lockForUpdate()->first();

                    if ($ingredient && $deductQty > 0) {
                        $ingredient->decrement('stock_qty', $deductQty);

                        StockMovement::create([
                            'ingredient_id'      => $ingredient->id,
                            'type'               => 'production_usage',
                            'qty'                => -$deductQty,
                            'unit_cost'          => $ingredient->cost_per_unit,
                            'notes'              => "Auto-deducted for Custom Order #{$customOrder->reference_number} ({$customOrder->servings_count} servings)",
                            'created_by_user_id' => $customOrder->user_id,
                        ]);
                    }
                }
            }

            foreach ($recipe->recipePackagings as $rp) {
                if ($rp->packagingMaterial) {
                    $qtyNeeded = (float) $rp->qty_used * $scaleFactor;
                    $deductQty = UnitConverterService::convert($qtyNeeded, $rp->unit, $rp->packagingMaterial->unit);
                    $pm        = PackagingMaterial::where('id', $rp->packaging_material_id)->lockForUpdate()->first();

                    if ($pm && $deductQty > 0) {
                        $pm->decrement('stock_qty', $deductQty);

                        PackagingMovement::create([
                            'packaging_material_id' => $pm->id,
                            'type'                  => 'out',
                            'qty'                   => -$deductQty,
                            'unit_cost'             => $pm->cost_per_unit,
                            'reference_type'        => CustomOrder::class,
                            'reference_id'          => $customOrder->id,
                            'notes'                 => "Auto-deducted packaging for Custom Order #{$customOrder->reference_number}",
                            'user_id'               => $customOrder->user_id,
                        ]);
                    }
                }
            }
        });
    }

    /**
     * Restore ingredients and packaging stock when a custom order is cancelled.
     */
    public function revertForCustomOrder(CustomOrder $customOrder): void
    {
        DB::transaction(function () use ($customOrder) {
            $recipe = Recipe::whereHas('product', function ($q) {
                $q->where('name', 'like', '%cake%')->orWhere('name', 'like', '%bread%');
            })->with(['recipeIngredients.ingredient', 'recipePackagings.packagingMaterial'])->first();

            if (! $recipe) {
                $recipe = Recipe::with(['recipeIngredients.ingredient', 'recipePackagings.packagingMaterial'])->first();
            }

            if (! $recipe) {
                return;
            }

            $servings = max(15.0, (float) ($customOrder->servings_count ?? 15));
            $yield = max(1.0, (float) $recipe->yield_qty);
            $scaleFactor = $servings / $yield;

            foreach ($recipe->recipeIngredients as $ri) {
                if ($ri->ingredient) {
                    $qtyNeeded  = (float) $ri->qty_required * $scaleFactor;
                    $restoreQty = UnitConverterService::convert($qtyNeeded, $ri->unit, $ri->ingredient->unit);
                    $ingredient = Ingredient::where('id', $ri->ingredient_id)->lockForUpdate()->first();

                    if ($ingredient && $restoreQty > 0) {
                        $ingredient->increment('stock_qty', $restoreQty);

                        StockMovement::create([
                            'ingredient_id'      => $ingredient->id,
                            'type'               => 'adjustment',
                            'qty'                => $restoreQty,
                            'unit_cost'          => $ingredient->cost_per_unit,
                            'notes'              => "Restored stock from cancelled Custom Order #{$customOrder->reference_number}",
                            'created_by_user_id' => $customOrder->user_id,
                        ]);
                    }
                }
            }

            foreach ($recipe->recipePackagings as $rp) {
                if ($rp->packagingMaterial) {
                    $qtyNeeded  = (float) $rp->qty_used * $scaleFactor;
                    $restoreQty = UnitConverterService::convert($qtyNeeded, $rp->unit, $rp->packagingMaterial->unit);
                    $pm         = PackagingMaterial::where('id', $rp->packaging_material_id)->lockForUpdate()->first();

                    if ($pm && $restoreQty > 0) {
                        $pm->increment('stock_qty', $restoreQty);

                        PackagingMovement::create([
                            'packaging_material_id' => $pm->id,
                            'type'                  => 'adjustment',
                            'qty'                   => $restoreQty,
                            'unit_cost'             => $pm->cost_per_unit,
                            'reference_type'        => CustomOrder::class,
                            'reference_id'          => $customOrder->id,
                            'notes'                 => "Restored packaging stock from cancelled Custom Order #{$customOrder->reference_number}",
                            'user_id'               => $customOrder->user_id,
                        ]);
                    }
                }
            }
        });
    }
}
