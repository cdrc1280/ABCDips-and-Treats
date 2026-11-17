<?php

namespace App\Domains\Storefront\Support;

/**
 * Estimated price of a custom cake — the same formula the custom-order page shows
 * (it receives table() as a prop), enforced here so the cart can't be told
 * a different price.
 */
final class CustomCakePricing
{
    public static function estimate(int $tiers, int $servings): float
    {
        $base = match (true) {
            $tiers >= 4 => 6500,
            $tiers === 3 => 4500,
            $tiers === 2 => 2800,
            default => 1500,
        };

        if ($servings >= 60) {
            $base += 800;
        }
        if ($servings >= 100) {
            $base += 1500;
        }

        return (float) $base;
    }

    /**
     * The same rules as data, for the live estimate on the custom-order page.
     *
     * @return array{tiers: array<int, int>, surcharges: list<array{min_servings: int, amount: int}>}
     */
    public static function table(): array
    {
        return [
            'tiers' => [1 => 1500, 2 => 2800, 3 => 4500, 4 => 6500],
            'surcharges' => [['min_servings' => 60, 'amount' => 800], ['min_servings' => 100, 'amount' => 1500]],
        ];
    }
}
