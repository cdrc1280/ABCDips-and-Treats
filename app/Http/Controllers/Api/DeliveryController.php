<?php

namespace App\Http\Controllers\Api;

use App\Models\Setting;
use App\Services\GeocoderService;
use App\Services\LalamoveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DeliveryController extends Controller
{
    public function __construct(
        private GeocoderService $geocoder,
        private LalamoveService $lalamove,
    ) {}

    /**
     * POST /api/delivery/quote
     * Geocodes the customer address and gets a Lalamove delivery price.
     */
    public function quote(Request $request): JsonResponse
    {
        $request->validate([
            'address' => 'required|string|min:5|max:500',
        ]);

        $address = $request->input('address');

        // 1. Geocode customer address
        $dropoffCoords = $this->geocoder->geocode($address);

        if (!$dropoffCoords) {
            return response()->json([
                'fee'     => null,
                'error'   => 'Could not locate address. Please enter a more complete delivery address (include Barangay and City).',
                'success' => false,
            ], 422);
        }

        // 2. Get store coordinates and address from admin settings
        $storeAddress = Setting::get('store_address', 'Bacoor, Cavite, Philippines');
        $storeLat = (float) Setting::get('store_lat', 0);
        $storeLng = (float) Setting::get('store_lng', 0);

        if (!$storeLat || !$storeLng) {
            $storeCoords = $this->geocoder->geocode($storeAddress);
            if ($storeCoords) {
                $storeLat = (float) $storeCoords['lat'];
                $storeLng = (float) $storeCoords['lng'];
            } else {
                $storeLat = 14.4597;
                $storeLng = 120.9640;
            }
        }

        // 3. Get Lalamove quote using dynamic store GPS coordinates
        $quote = $this->lalamove->quote(
            pickup:  ['lat' => $storeLat, 'lng' => $storeLng],
            dropoff: ['lat' => $dropoffCoords['lat'], 'lng' => $dropoffCoords['lng']],
        );

        if (!$quote) {
            // Lalamove not configured or failed — return a distance-based estimate
            $estimate = $this->distanceBasedEstimate($storeLat, $storeLng, $dropoffCoords['lat'], $dropoffCoords['lng']);

            return response()->json([
                'fee'            => $estimate['fee'],
                'currency'       => 'PHP',
                'provider'       => 'estimate',
                'provider_label' => 'Estimated',
                'service'        => 'Delivery',
                'note'           => 'Exact Lalamove fee will be confirmed after order. Configure Lalamove API keys in Admin → Settings for real-time quotes.',
                'dropoff'        => [
                    'lat'          => $dropoffCoords['lat'],
                    'lng'          => $dropoffCoords['lng'],
                    'display_name' => $dropoffCoords['display_name'] ?? $address,
                ],
                'success'        => true,
            ]);
        }

        return response()->json([
            'fee'            => $quote['fee'],
            'currency'       => 'PHP',
            'provider'       => 'lalamove',
            'provider_label' => 'Lalamove',
            'service'        => $quote['service'],
            'dropoff'        => [
                'lat'          => $dropoffCoords['lat'],
                'lng'          => $dropoffCoords['lng'],
                'display_name' => $dropoffCoords['display_name'] ?? $address,
            ],
            'success'        => true,
        ]);
    }

    /**
     * Simple Haversine-based fallback estimate when Lalamove is unconfigured.
     * Base: ₱80 + ₱18/km. Min: ₱80, Max: ₱500.
     */
    private function distanceBasedEstimate(float $lat1, float $lng1, float $lat2, float $lng2): array
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distanceKm = $earthRadius * $c;

        $fee = max(80, min(500, round(80 + ($distanceKm * 18), -1)));

        return ['fee' => $fee, 'distance_km' => round($distanceKm, 1)];
    }
}
