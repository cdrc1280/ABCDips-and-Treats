<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\PackagingMaterial;
use App\Models\Product;
use App\Models\Recipe;

class CostingService
{
    /**
     * Calculate complete pastry costing breakdown for a given product.
     */
    public function calculateForProduct(Product $product, array $options = []): array
    {
        $laborRatePerHour  = (float) ($options['labor_rate_per_hour'] ?? 75.00); // ₱75/hr default baker rate
        $overheadPct       = (float) ($options['overhead_pct'] ?? 15.0);        // 15% default overhead buffer
        $targetMarkupPct   = (float) ($options['target_markup_pct'] ?? 100.0);   // 100% markup default (2x COGS)
        $targetFoodCostPct = (float) ($options['target_food_cost_pct'] ?? 30.0); // 30% target food cost

        $recipe = $product->recipe ?? Recipe::where('product_id', $product->id)->with('recipeIngredients.ingredient')->first();

        $ingredientCostBatch = 0.0;
        $ingredientItems     = [];
        $yieldQty            = 1;
        $prepTimeMinutes     = (int) ($product->prep_time_minutes ?? 20);
        $bakingTimeMinutes   = 0;

        if ($recipe) {
            $yieldQty          = max(1, (int) $recipe->yield_qty);
            $prepTimeMinutes   = (int) ($recipe->prep_time_minutes ?? $prepTimeMinutes);
            $bakingTimeMinutes = (int) ($recipe->baking_time_minutes ?? 0);

            foreach ($recipe->recipeIngredients as $item) {
                if (! $item->ingredient) {
                    continue;
                }

                $ingredient  = $item->ingredient;
                $costPerUnit = (float) $ingredient->cost_per_unit;
                $qtyRequired = (float) $item->qty_required;
                $recipeUnit  = strtolower(trim($item->unit));
                $stockUnit   = strtolower(trim($ingredient->unit));

                $multiplier = match ($recipeUnit) {
                    'g'     => in_array($stockUnit, ['kg', 'kilogram', 'kilograms']) ? 0.001 : 1.0,
                    'mg'    => in_array($stockUnit, ['kg', 'kilogram']) ? 0.000001 : (in_array($stockUnit, ['g', 'gram']) ? 0.001 : 1.0),
                    'ml'    => in_array($stockUnit, ['l', 'liter', 'liters']) ? 0.001 : 1.0,
                    'tsp'   => in_array($stockUnit, ['kg', 'l']) ? 0.005 : (in_array($stockUnit, ['g', 'ml']) ? 5.0 : 1.0),
                    'tbsp'  => in_array($stockUnit, ['kg', 'l']) ? 0.015 : (in_array($stockUnit, ['g', 'ml']) ? 15.0 : 1.0),
                    'cup'   => in_array($stockUnit, ['kg', 'l']) ? 0.240 : (in_array($stockUnit, ['g', 'ml']) ? 240.0 : 1.0),
                    default => 1.0,
                };

                $itemCost = round(($qtyRequired * $multiplier) * $costPerUnit, 2);
                $ingredientCostBatch += $itemCost;

                $ingredientItems[] = [
                    'ingredient_name' => $ingredient->name,
                    'qty_required'    => $qtyRequired,
                    'unit'            => $item->unit,
                    'unit_cost'       => $costPerUnit,
                    'stock_unit'      => $ingredient->unit,
                    'total_cost'      => $itemCost,
                ];
            }
        }

        $ingredientCostUnit = round($ingredientCostBatch / $yieldQty, 2);

        // Calculate line percentages for ingredient breakdown
        foreach ($ingredientItems as &$ing) {
            $ing['cost_per_product_unit'] = round($ing['total_cost'] / $yieldQty, 2);
            $ing['pct_of_food_cost']      = $ingredientCostBatch > 0 ? round(($ing['total_cost'] / $ingredientCostBatch) * 100, 1) : 0;
        }
        unset($ing);

        // Packaging cost estimation (standard box + sticker + bag or packaging materials in DB)
        $packagingMaterials = PackagingMaterial::where('is_active', true)->get();
        $packagingCostUnit  = 0.0;
        $packagingItems     = [];

        // Estimate standard packaging per product unit (1 box/container + 1 sticker)
        foreach ($packagingMaterials as $pm) {
            if ($pm->cost_per_unit > 0) {
                // If packaging name or type matches product type
                $isMatchingType = false;
                if ($pm->type === 'box' && (str_contains(strtolower($product->name), 'bread') || str_contains(strtolower($product->name), 'cake') || str_contains(strtolower($product->name), 'loaf'))) {
                    $isMatchingType = true;
                } elseif ($pm->type === 'sticker' || $pm->type === 'label') {
                    $isMatchingType = true;
                }

                if ($isMatchingType) {
                    $cost = (float) $pm->cost_per_unit;
                    $packagingCostUnit += $cost;
                    $packagingItems[] = [
                        'name'       => $pm->name,
                        'type'       => $pm->type,
                        'unit_cost'  => $cost,
                    ];
                }
            }
        }
        $packagingCostUnit = round($packagingCostUnit, 2);

        // Direct Labor Cost per unit
        $totalWorkMinutes = $prepTimeMinutes + $bakingTimeMinutes;
        $laborCostBatch   = round(($totalWorkMinutes / 60) * $laborRatePerHour, 2);
        $laborCostUnit    = round($laborCostBatch / $yieldQty, 2);

        // Overhead Allocation (LPG gas, electricity, rent, depreciation)
        $directCostUnit   = $ingredientCostUnit + $packagingCostUnit;
        $overheadCostUnit = round($directCostUnit * ($overheadPct / 100), 2);

        // Total COGS per unit
        $totalCogsUnit = round($ingredientCostUnit + $packagingCostUnit + $laborCostUnit + $overheadCostUnit, 2);

        // Pricing Metrics
        $currentPrice   = (float) ($product->sale_price ?? $product->price);
        $grossProfit    = round($currentPrice - $totalCogsUnit, 2);
        $grossMarginPct = $currentPrice > 0 ? round(($grossProfit / $currentPrice) * 100, 1) : 0.0;
        $foodCostPct    = $currentPrice > 0 ? round(($ingredientCostUnit / $currentPrice) * 100, 1) : 0.0;
        $markupPct      = $totalCogsUnit > 0 ? round(($grossProfit / $totalCogsUnit) * 100, 1) : 0.0;

        // Suggested Retail Prices (SRP)
        $srpBasedOnMarkup   = round($totalCogsUnit * (1 + ($targetMarkupPct / 100)), 2);
        $srpBasedOnFoodCost = $targetFoodCostPct > 0 ? round($ingredientCostUnit / ($targetFoodCostPct / 100), 2) : 0.0;

        // Round SRP to clean commercial pricing ending in 0 or 5 or .00 (e.g. ₱280.00)
        $suggestedSrp = ceil($srpBasedOnMarkup / 5) * 5;

        // Margin Health Classification
        $healthStatus = 'good'; // good (>55%), warning (35-55%), danger (<35%)
        if ($grossMarginPct < 35) {
            $healthStatus = 'danger';
        } elseif ($grossMarginPct < 55) {
            $healthStatus = 'warning';
        }

        return [
            'product_id'             => $product->id,
            'product_name'           => $product->name,
            'sku'                    => $product->sku,
            'current_price'          => $currentPrice,
            'yield_qty'              => $yieldQty,
            'prep_time_minutes'      => $prepTimeMinutes,
            'baking_time_minutes'    => $bakingTimeMinutes,
            'total_work_minutes'     => $totalWorkMinutes,
            'ingredient_cost_batch'  => $ingredientCostBatch,
            'ingredient_cost_unit'   => $ingredientCostUnit,
            'ingredient_items'       => $ingredientItems,
            'packaging_cost_unit'    => $packagingCostUnit,
            'packaging_items'        => $packagingItems,
            'labor_cost_batch'       => $laborCostBatch,
            'labor_cost_unit'        => $laborCostUnit,
            'overhead_cost_unit'     => $overheadCostUnit,
            'total_cogs_unit'        => $totalCogsUnit,
            'gross_profit_unit'      => $grossProfit,
            'gross_margin_pct'       => $grossMarginPct,
            'food_cost_pct'          => $foodCostPct,
            'markup_pct'             => $markupPct,
            'suggested_srp'          => $suggestedSrp,
            'srp_based_on_markup'    => $srpBasedOnMarkup,
            'srp_based_on_food_cost' => $srpBasedOnFoodCost,
            'health_status'          => $healthStatus,
            'labor_rate_per_hour'    => $laborRatePerHour,
            'overhead_pct'           => $overheadPct,
            'target_markup_pct'      => $targetMarkupPct,
            'target_food_cost_pct'   => $targetFoodCostPct,
        ];
    }
}
