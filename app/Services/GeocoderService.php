<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocoderService
{
    /**
     * Geocode a Philippine address string to {lat, lng}.
     * Uses OpenStreetMap Nominatim — completely free, no API key required.
     *
     * @param string $address  Free-text address (e.g. "123 Zapote Rd, Bacoor, Cavite")
     * @return array|null      ['lat' => float, 'lng' => float] or null on failure
     */
    public function geocode(string $address): ?array
    {
        $cacheKey = 'geocode:' . md5(strtolower(trim($address)));

        return Cache::remember($cacheKey, 3600, function () use ($address) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'ABCDips-Treats/1.0 (abcdips@example.com)',
                    'Accept-Language' => 'en',
                ])->timeout(8)->get('https://nominatim.openstreetmap.org/search', [
                    'q'            => $address . ', Philippines',
                    'format'       => 'json',
                    'countrycodes' => 'ph',
                    'limit'        => 1,
                    'addressdetails' => 0,
                ]);

                if ($response->successful()) {
                    $results = $response->json();
                    if (!empty($results[0])) {
                        return [
                            'lat' => (float) $results[0]['lat'],
                            'lng' => (float) $results[0]['lon'],
                            'display_name' => $results[0]['display_name'] ?? $address,
                        ];
                    }
                }

                Log::warning('[Geocoder] No results for address: ' . $address);
                return null;
            } catch (\Throwable $e) {
                Log::error('[Geocoder] Exception: ' . $e->getMessage());
                return null;
            }
        });
    }
}
