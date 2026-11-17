<?php

namespace App\Domains\Storefront\Support;

use App\Domains\Setting\Models\Setting;
use App\Services\GeocoderService;
use App\Services\LalamoveService;
use Illuminate\Support\Facades\Cache;

/**
 * Priority-delivery fee from the store to a drop-off point: a live Lalamove quote, or a
 * distance estimate (₱80 + ₱18/km, ₱80–₱500) when Lalamove is not configured. The fee charged
 * at checkout always comes from here — never from the browser.
 */
final class DeliveryQuoter
{
    private const FALLBACK = ['lat' => 14.4597, 'lng' => 120.9640]; // Bacoor, Cavite

    public function __construct(
        private readonly GeocoderService $geocoder,
        private readonly LalamoveService $lalamove,
    ) {}

    /**
     * @return array{lat: float, lng: float}
     */
    public function store(): array
    {
        $lat = (float) Setting::get('store_lat', 0);
        $lng = (float) Setting::get('store_lng', 0);
        if ($lat && $lng) {
            return ['lat' => $lat, 'lng' => $lng];
        }

        return Cache::remember('storefront.store_coords', 86400, function () {
            $coords = $this->geocoder->geocode((string) Setting::get('store_address', 'Bacoor, Cavite, Philippines'));

            return $coords ? ['lat' => (float) $coords['lat'], 'lng' => (float) $coords['lng']] : self::FALLBACK;
        });
    }

    /**
     * @return array{fee: float, provider: string, service: ?string, distance_km: ?float, lat: float, lng: float, note: ?string}
     */
    public function quote(?float $lat, ?float $lng, string $address): array
    {
        if (! $lat || ! $lng) {
            $coords = $this->geocoder->geocode($address);
            [$lat, $lng] = $coords ? [(float) $coords['lat'], (float) $coords['lng']] : [self::FALLBACK['lat'], self::FALLBACK['lng']];
        }

        $lat = round($lat, 6);
        $lng = round($lng, 6);

        // Same point → same fee for 10 minutes, so the price shown is the price charged.
        return Cache::remember("delivery.quote.{$lat}.{$lng}", 600, function () use ($lat, $lng) {
            $store = $this->store();
            $live = $this->lalamove->quote(pickup: $store, dropoff: ['lat' => $lat, 'lng' => $lng]);

            if ($live) {
                return [
                    'fee' => round((float) $live['fee'], 2),
                    'provider' => 'lalamove',
                    'service' => $live['service'] ?? null,
                    'distance_km' => isset($live['distance_km']) ? (float) $live['distance_km'] : null,
                    'lat' => $lat,
                    'lng' => $lng,
                    'note' => null,
                ];
            }

            $km = self::distanceKm($store['lat'], $store['lng'], $lat, $lng);

            return [
                'fee' => (float) max(80, min(500, round(80 + $km * 18, -1))),
                'provider' => 'estimate',
                'service' => 'Delivery',
                'distance_km' => round($km, 1),
                'lat' => $lat,
                'lng' => $lng,
                'note' => 'Estimated from distance; the exact Lalamove fee is confirmed after your order.',
            ];
        });
    }

    private static function distanceKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 6371 * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
