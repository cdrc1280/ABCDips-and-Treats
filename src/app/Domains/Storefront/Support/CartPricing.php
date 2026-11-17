<?php

namespace App\Domains\Storefront\Support;

use App\Domains\Catalog\Models\Product;

/**
 * The only place a cart line's price is decided — always from the product's own data,
 * never from values the browser sends (unit_price / price_modifier / flavor_price_modifier
 * in a request are ignored and overwritten).
 *
 *   unit price = effective price (sale price while on sale)
 *              + modifier of the chosen variation (matched by label)
 *              + modifiers of the chosen flavor(s) (matched by name)
 *
 * Custom cakes are priced by CustomCakePricing from tiers and servings.
 */
final class CartPricing
{
    /** Option keys a customer may set; everything else is dropped. */
    private const ALLOWED = [
        'variation', 'flavor', 'flavors', 'is_assorted', 'note',
        // custom cake brief
        'is_custom', 'custom_title', 'tiers_count', 'servings_count', 'flavor_preference', 'frosting_type',
        'cake_inscription', 'event_date', 'theme_description', 'budget_range_min', 'budget_range_max',
    ];

    /**
     * @param  array<string, mixed>|null  $options
     * @return array<string, mixed>|null sanitised options with server-computed modifiers
     */
    public static function normalize(Product $product, ?array $options): ?array
    {
        $options = array_intersect_key((array) $options, array_flip(self::ALLOWED));

        if (! empty($options['is_custom'])) {
            $tiers = max(1, min(10, (int) ($options['tiers_count'] ?? 1)));
            $servings = max(1, min(1000, (int) ($options['servings_count'] ?? 20)));
            $options['tiers_count'] = $tiers;
            $options['servings_count'] = $servings;
            $options['custom_title'] = mb_substr((string) ($options['custom_title'] ?? "Custom {$tiers}-tier cake"), 0, 120);
            $options['custom_unit_price'] = CustomCakePricing::estimate($tiers, $servings);

            return $options;
        }

        $variation = self::find((array) $product->variations, 'label', $options['variation'] ?? null);
        if ($variation) {
            $options['variation'] = (string) $variation['label'];
            $options['price_modifier'] = (float) ($variation['price_modifier'] ?? 0);
        } else {
            unset($options['variation']); // price_modifier never survives the ALLOWED whitelist
        }

        $flavorDefs = (array) $product->flavors;
        if (! empty($options['flavors']) && is_array($options['flavors'])) {
            $chosen = array_values(array_filter(array_map(fn ($name) => self::find($flavorDefs, 'name', $name), $options['flavors'])));
            if ($product->max_flavors) {
                $chosen = array_slice($chosen, 0, (int) $product->max_flavors);
            }
            $options['flavors'] = array_map(fn ($f) => (string) $f['name'], $chosen);
            $options['flavor'] = implode(', ', $options['flavors']);
            $options['flavor_price_modifier'] = round(array_sum(array_map(fn ($f) => (float) ($f['price_modifier'] ?? 0), $chosen)), 2);
            $options['is_assorted'] = true;
        } elseif (! empty($options['flavor'])) {
            $flavor = self::find($flavorDefs, 'name', $options['flavor']);
            $options['flavor'] = $flavor ? (string) $flavor['name'] : ($product->flavor ?: null);
            $options['flavor_price_modifier'] = $flavor ? (float) ($flavor['price_modifier'] ?? 0) : 0.0;
            unset($options['flavors'], $options['is_assorted']);
        } elseif ($product->flavor) {
            $options['flavor'] = $product->flavor;
        }

        $options = array_filter($options, fn ($v) => $v !== null && $v !== '' && $v !== []);

        return $options === [] ? null : $options;
    }

    /**
     * @param  array<string, mixed>|null  $options  already normalised
     */
    public static function unitPrice(Product $product, ?array $options): float
    {
        if (! empty($options['is_custom'])) {
            return (float) ($options['custom_unit_price'] ?? CustomCakePricing::estimate((int) ($options['tiers_count'] ?? 1), (int) ($options['servings_count'] ?? 20)));
        }

        $normalized = self::normalize($product, $options) ?? [];

        return round((float) $product->effective_price + (float) ($normalized['price_modifier'] ?? 0) + (float) ($normalized['flavor_price_modifier'] ?? 0), 2);
    }

    /**
     * @param  array<mixed>  $definitions
     * @return array<string, mixed>|null
     */
    private static function find(array $definitions, string $key, mixed $value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }
        foreach ($definitions as $definition) {
            if (is_array($definition) && isset($definition[$key]) && strcasecmp((string) $definition[$key], (string) $value) === 0) {
                return $definition;
            }
        }

        return null;
    }
}
