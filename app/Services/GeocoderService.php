<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocoderService
{
    /**
     * Geocode a Philippine address string to {lat, lng}.
     * Multi-stage search: Photon API -> Nominatim Full -> Nominatim Simplified Fallback.
     *
     * @param string $address  Free-text address (e.g. "Namahay, Muzon, Taytay, Rizal, CALABARZON")
     * @return array|null      ['lat' => float, 'lng' => float, 'display_name' => string] or null on failure
     */
    public function geocode(string $address): ?array
    {
        $cleanAddress = trim($address);
        if (strlen($cleanAddress) < 3) {
            return null;
        }

        $cacheKey = 'geocode_v3:' . md5(strtolower($cleanAddress));

        return Cache::remember($cacheKey, 86400, function () use ($cleanAddress) {
            // Stage 1: Try Photon API (Fuzzy search tuned for Philippines)
            try {
                $photonRes = Http::timeout(5)->get('https://photon.komoot.io/api/', [
                    'q'     => $cleanAddress . ' Philippines',
                    'limit' => 1,
                    'bbox'  => '119.5,13.5,122.5,15.5',
                ]);

                if ($photonRes->successful()) {
                    $features = $photonRes->json('features');
                    if (!empty($features[0]['geometry']['coordinates'])) {
                        $coords = $features[0]['geometry']['coordinates'];
                        $props = $features[0]['properties'] ?? [];
                        $name = $props['name'] ?? $props['street'] ?? $cleanAddress;

                        return [
                            'lat'          => (float) $coords[1],
                            'lng'          => (float) $coords[0],
                            'display_name' => $name,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('[Geocoder] Photon failed: ' . $e->getMessage());
            }

            // Stage 2: Nominatim Full Query
            try {
                $nomRes = Http::withHeaders([
                    'User-Agent' => 'ABCDips-Treats/1.0 (abcdips@example.com)',
                    'Accept-Language' => 'en',
                ])->timeout(6)->get('https://nominatim.openstreetmap.org/search', [
                    'q'            => $cleanAddress . ', Philippines',
                    'format'       => 'json',
                    'countrycodes' => 'ph',
                    'limit'        => 1,
                ]);

                if ($nomRes->successful() && !empty($nomRes->json()[0])) {
                    $item = $nomRes->json()[0];
                    return [
                        'lat'          => (float) $item['lat'],
                        'lng'          => (float) $item['lon'],
                        'display_name' => $item['display_name'] ?? $cleanAddress,
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning('[Geocoder] Nominatim stage 2 failed: ' . $e->getMessage());
            }

            // Stage 3: Nominatim Simplified Fallback (strip specific street name, search Barangay + City + Province)
            $parts = array_map('trim', explode(',', $cleanAddress));
            if (count($parts) > 2) {
                array_shift($parts); // Remove first part (street/house detail)
                $simplifiedAddress = implode(', ', $parts);

                try {
                    $fallbackRes = Http::withHeaders([
                        'User-Agent' => 'ABCDips-Treats/1.0 (abcdips@example.com)',
                        'Accept-Language' => 'en',
                    ])->timeout(6)->get('https://nominatim.openstreetmap.org/search', [
                        'q'            => $simplifiedAddress . ', Philippines',
                        'format'       => 'json',
                        'countrycodes' => 'ph',
                        'limit'        => 1,
                    ]);

                    if ($fallbackRes->successful() && !empty($fallbackRes->json()[0])) {
                        $item = $fallbackRes->json()[0];
                        return [
                            'lat'          => (float) $item['lat'],
                            'lng'          => (float) $item['lon'],
                            'display_name' => $item['display_name'] ?? $simplifiedAddress,
                        ];
                    }
                } catch (\Throwable $e) {
                    Log::warning('[Geocoder] Nominatim stage 3 fallback failed: ' . $e->getMessage());
                }
            }

            Log::warning('[Geocoder] All geocoding stages failed for: ' . $cleanAddress);
            return null;
        });
    }
}
