<?php

namespace App\Services;

class UnitConverterService
{
    /**
     * Converts a quantity from one unit to another.
     */
    public static function convert(float $qty, ?string $fromUnit, ?string $toUnit): float
    {
        if ($qty <= 0) {
            return 0.0;
        }

        $fromRaw = strtolower(trim($fromUnit ?? ''));
        $toRaw   = strtolower(trim($toUnit ?? ''));

        if ($fromRaw === '' || $toRaw === '' || $fromRaw === $toRaw) {
            return $qty;
        }

        // Extract multiplier for compound unit strings like "5 cups", "4 cups", "1/2 cup"
        [$fromCoef, $fromNorm] = self::parseUnitString($fromRaw);
        [$toCoef, $toNorm]     = self::parseUnitString($toRaw);

        $effectiveQty = $qty * $fromCoef;

        $fromFactor = self::getBaseFactor($fromNorm);
        $toFactor   = self::getBaseFactor($toNorm);

        if ($fromFactor !== null && $toFactor !== null && $fromFactor['type'] === $toFactor['type']) {
            // Standard conversion within same dimension (mass to mass, volume to volume, count to count)
            $baseVal = $effectiveQty * $fromFactor['value'];
            $result  = $baseVal / ($toFactor['value'] * $toCoef);
            return round($result, 6);
        }

        // Cross-dimension fallback (e.g. volume ml/cup to mass g/kg assuming standard density 1ml = 1g)
        $fromBase = self::toStandardBase($effectiveQty, $fromNorm);
        $toBase   = self::fromStandardBase($fromBase, $toNorm);

        return round($toBase / $toCoef, 6);
    }

    private static function parseUnitString(string $unitStr): array
    {
        $unitStr = trim($unitStr);

        // Matches e.g. "5 cups", "4 cups", "1/2 cup", "0.5 cup"
        if (preg_match('/^(\d+\/\d+|\d+(?:\.\d+)?)\s*(.*)$/', $unitStr, $matches)) {
            $rawNum = $matches[1];
            $normUnit = trim($matches[2]);

            if (str_contains($rawNum, '/')) {
                $parts = explode('/', $rawNum);
                $coef = (float) $parts[0] / max(1.0, (float) $parts[1]);
            } else {
                $coef = (float) $rawNum;
            }

            return [$coef > 0 ? $coef : 1.0, $normUnit !== '' ? $normUnit : 'cup'];
        }

        return [1.0, $unitStr];
    }

    private static function getBaseFactor(string $unit): ?array
    {
        return match ($unit) {
            // Mass -> Grams
            'g', 'gram', 'grams'                         => ['type' => 'mass', 'value' => 1.0],
            'kg', 'kilogram', 'kilograms'                 => ['type' => 'mass', 'value' => 1000.0],
            'mg', 'milligram', 'milligrams'               => ['type' => 'mass', 'value' => 0.001],
            'oz', 'ounce', 'ounces'                       => ['type' => 'mass', 'value' => 28.3495],
            'lb', 'lbs', 'pound', 'pounds'                => ['type' => 'mass', 'value' => 453.592],

            // Volume -> Milliliters
            'ml', 'milliliter', 'milliliters'             => ['type' => 'volume', 'value' => 1.0],
            'l', 'liter', 'liters'                        => ['type' => 'volume', 'value' => 1000.0],

            // Kitchen measures -> Volume ml
            'tsp', 'teaspoon', 'teaspoons'                => ['type' => 'volume', 'value' => 5.0],
            'tbsp', 'tablespoon', 'tablespoons'            => ['type' => 'volume', 'value' => 15.0],
            'cup', 'cups'                                 => ['type' => 'volume', 'value' => 240.0],

            // Count -> Piece
            'pc', 'pcs', 'piece', 'pieces', 'box', 'pack' => ['type' => 'count', 'value' => 1.0],

            default => null,
        };
    }

    private static function toStandardBase(float $qty, string $unit): float
    {
        $factor = self::getBaseFactor($unit);
        if ($factor) {
            return $qty * $factor['value'];
        }
        return $qty;
    }

    private static function fromStandardBase(float $baseVal, string $targetUnit): float
    {
        $factor = self::getBaseFactor($targetUnit);
        if ($factor && $factor['value'] > 0) {
            return $baseVal / $factor['value'];
        }
        return $baseVal;
    }
}
