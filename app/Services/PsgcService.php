<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PsgcService
{
    public static function getRegionsOptions(): array
    {
        return Cache::remember('psgc_regions_options_v2', 86400 * 7, function () {
            try {
                $response = Http::timeout(5)->get('https://psgc.gitlab.io/api/regions.json');
                if ($response->successful()) {
                    $regions = $response->json();
                    $options = [];
                    foreach ($regions as $r) {
                        $label = ($r['name'] ?? '') . (!empty($r['regionName']) ? " ({$r['regionName']})" : '');
                        $options[$r['code']] = $label;
                    }
                    return $options;
                }
            } catch (\Throwable $e) {
                // Fallback regions
            }

            return [
                '040000000' => 'CALABARZON (Region IV-A)',
                '130000000' => 'National Capital Region (NCR)',
                '030000000' => 'Central Luzon (Region III)',
            ];
        });
    }

    public static function getProvincesOptions(?string $regionCode): array
    {
        if (!$regionCode) return [];

        // If NCR, return Metro Manila
        if ($regionCode === '130000000') {
            return ['NCR' => 'Metro Manila'];
        }

        return Cache::remember("psgc_provinces_options_{$regionCode}_v2", 86400 * 7, function () use ($regionCode) {
            try {
                $response = Http::timeout(5)->get("https://psgc.gitlab.io/api/regions/{$regionCode}/provinces.json");
                if ($response->successful()) {
                    $provinces = $response->json();
                    $options = [];
                    foreach ($provinces as $p) {
                        $options[$p['code']] = $p['name'];
                    }
                    return $options;
                }
            } catch (\Throwable $e) {}

            if ($regionCode === '040000000') {
                return ['042100000' => 'Cavite', '043400000' => 'Laguna', '041000000' => 'Batangas', '045600000' => 'Rizal', '045300000' => 'Quezon'];
            }

            return [];
        });
    }

    public static function getCitiesOptions(?string $regionCode = null, ?string $provinceCode = null): array
    {
        if (!$regionCode && !$provinceCode) {
            return [
                '042103000' => 'City of Bacoor',
                '042109000' => 'City of Imus',
                '042106000' => 'City of Dasmariñas',
                '042108000' => 'City of General Trias',
                '042111000' => 'Kawit',
                '042118000' => 'Silang',
                '042119000' => 'City of Tagaytay',
                '133900000' => 'Manila',
                '137400000' => 'Quezon City',
                '137600000' => 'Las Piñas',
                '137602000' => 'Parañaque',
                '137603000' => 'Muntinlupa',
            ];
        }

        $cacheKey = $regionCode === '130000000' ? 'ncr' : ($provinceCode ?: $regionCode);

        return Cache::remember("psgc_cities_options_{$cacheKey}_v3", 86400 * 7, function () use ($regionCode, $provinceCode) {
            try {
                $url = $regionCode === '130000000'
                    ? 'https://psgc.gitlab.io/api/regions/130000000/cities-municipalities.json'
                    : "https://psgc.gitlab.io/api/provinces/{$provinceCode}/cities-municipalities.json";

                $response = Http::timeout(5)->get($url);
                if ($response->successful()) {
                    $cities = $response->json();
                    $options = [];
                    foreach ($cities as $c) {
                        $options[$c['code']] = $c['name'];
                    }
                    return $options;
                }
            } catch (\Throwable $e) {}

            return [
                '042103000' => 'City of Bacoor',
                '042109000' => 'City of Imus',
                '042106000' => 'City of Dasmariñas',
                '042108000' => 'City of General Trias',
            ];
        });
    }

    public static function getBarangaysOptions(?string $cityCode): array
    {
        if (!$cityCode) {
            return [
                '042103031' => 'Molino III',
                '042103030' => 'Molino II',
                '042103029' => 'Molino I',
                '042103049' => 'San Nicolas II',
                '042103048' => 'San Nicolas I',
                '042103032' => 'Molino IV',
                '042103033' => 'Molino V',
                '042103034' => 'Molino VI',
                '042103035' => 'Molino VII',
                '042103057' => 'Zapote V',
            ];
        }

        $codeMap = [
            'bacoor'             => '042103000',
            'city of bacoor'      => '042103000',
            'imus'               => '042109000',
            'city of imus'       => '042109000',
            'dasmariñas'         => '042106000',
            'dasmarinas'         => '042106000',
            'city of dasmariñas' => '042106000',
            'general trias'      => '042108000',
        ];

        $effectiveCode = $codeMap[strtolower(trim($cityCode))] ?? $cityCode;

        return Cache::remember("psgc_barangays_options_{$effectiveCode}_v3", 86400 * 7, function () use ($effectiveCode) {
            try {
                $response = Http::timeout(5)->get("https://psgc.gitlab.io/api/cities-municipalities/{$effectiveCode}/barangays.json");
                if ($response->successful()) {
                    $barangays = $response->json();
                    $options = [];
                    foreach ($barangays as $b) {
                        $options[$b['code']] = $b['name'];
                    }
                    return $options;
                }
            } catch (\Throwable $e) {}

            return [
                '042103031' => 'Molino III',
                '042103030' => 'Molino II',
                '042103029' => 'Molino I',
                '042103049' => 'San Nicolas II',
                '042103048' => 'San Nicolas I',
                '042103032' => 'Molino IV',
            ];
        });
    }
}
