<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LalamoveService
{
    private const SANDBOX_URL = 'https://rest.sandbox.lalamove.com';
    private const LIVE_URL = 'https://rest.lalamove.com';

    private string $apiKey;
    private string $apiSecret;
    private bool $isSandbox;
    private string $serviceType;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = Setting::get('lalamove_api_key', '');
        $this->apiSecret = Setting::get('lalamove_api_secret', '');
        $this->isSandbox = (bool) Setting::get('lalamove_sandbox', true);
        $this->serviceType = Setting::get('lalamove_service_type', 'MOTORCYCLE');
        $this->baseUrl = $this->isSandbox ? self::SANDBOX_URL : self::LIVE_URL;
    }

    /**
     * Get a delivery price quote from Lalamove.
     *
     * @param array $pickup    ['lat' => float, 'lng' => float]
     * @param array $dropoff   ['lat' => float, 'lng' => float]
     * @return array|null      ['fee' => float, 'currency' => string, 'service' => string] or null
     */
    public function quote(array $pickup, array $dropoff): ?array
    {
        if (empty($this->apiKey) || empty($this->apiSecret)) {
            Log::warning('[Lalamove] API key or secret not configured.');
            return null;
        }

        $body = [
            'data' => [
                'serviceType' => $this->serviceType,
                'language' => 'en_PH',
                'stops' => [
                    [
                        'coordinates' => [
                            'lat' => (string) $pickup['lat'],
                            'lng' => (string) $pickup['lng'],
                        ],
                        'address' => Setting::get('store_address', 'Cavite, Philippines'),
                    ],
                    [
                        'coordinates' => [
                            'lat' => (string) $dropoff['lat'],
                            'lng' => (string) $dropoff['lng'],
                        ],
                        'address' => 'Customer Address',
                    ],
                ],
            ],
        ];

        $path = '/v3/quotations';
        $method = 'POST';
        $timestamp = (string) round(microtime(true) * 1000);
        $bodyJson = json_encode($body);
        $signature = $this->sign($timestamp, $method, $path, $bodyJson);

        try {
            $response = Http::withHeaders([
                'Authorization' => "hmac {$this->apiKey}:{$timestamp}:{$signature}",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Market' => 'PH',
            ])->timeout(10)->post($this->baseUrl . $path, $body);

            if ($response->successful()) {
                $json = $response->json();
                $fee = $json['data']['priceBreakdown']['total'] ?? null;

                if ($fee !== null) {
                    return [
                        'fee' => (float) $fee,
                        'currency' => 'PHP',
                        'service' => $this->serviceType,
                        'provider' => 'Lalamove',
                    ];
                }
            }

            Log::warning('[Lalamove] Quote failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Throwable $e) {
            Log::error('[Lalamove] Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate HMAC-SHA256 signature for Lalamove API.
     */
    private function sign(string $timestamp, string $method, string $path, string $body): string
    {
        $rawSig = "{$timestamp}\r\n{$method}\r\n{$path}\r\n\r\n{$body}";
        return hash_hmac('sha256', $rawSig, $this->apiSecret);
    }
}
