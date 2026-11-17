<?php

namespace App\Services;

use App\Domains\Setting\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayMongoService
{
    private const BASE_URL = 'https://api.paymongo.com/v1';

    private string $secretKey;

    private bool $configLoaded = false;

    /**
     * Settings are read on first use, not in the constructor, so resolving this service
     * (e.g. route/controller reflection during `wayfinder:generate` at build time) never hits the DB.
     */
    private function loadConfig(): void
    {
        if ($this->configLoaded) {
            return;
        }
        $this->configLoaded = true;

        $this->secretKey = (string) (Setting::get('paymongo_secret_key', '') ?? '');
    }

    /**
     * Create a PayMongo source for GCash or Maya (paymaya).
     *
     * @param  string  $type  'gcash' or 'paymaya'
     * @param  int  $amount  Amount in CENTAVOS (e.g. ₱185.50 = 18550)
     * @param  string  $orderId  Your internal order reference
     * @param  string  $email  Customer email for receipt
     * @param  string  $name  Customer name
     * @return array|null ['source_id', 'checkout_url', 'status'] or null on failure
     */
    public function createSource(
        string $type,
        int $amount,
        string $orderId,
        string $email,
        string $name
    ): ?array {
        $this->loadConfig();

        if (empty($this->secretKey)) {
            Log::warning('[PayMongo] Secret key not configured.');

            return null;
        }

        $successUrl = url("/checkout/payment-success?order={$orderId}");
        $failedUrl = url("/checkout/payment-failed?order={$orderId}");

        $payload = [
            'data' => [
                'attributes' => [
                    'amount' => $amount,
                    'currency' => 'PHP',
                    'type' => $type,
                    'redirect' => [
                        'success' => $successUrl,
                        'failed' => $failedUrl,
                    ],
                    'billing' => [
                        'name' => $name,
                        'email' => $email,
                    ],
                ],
            ],
        ];

        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->timeout(15)
                ->post(self::BASE_URL.'/sources', $payload);

            if ($response->successful()) {
                $data = $response->json()['data'] ?? null;
                if ($data) {
                    return [
                        'source_id' => $data['id'],
                        'checkout_url' => $data['attributes']['redirect']['checkout_url'],
                        'status' => $data['attributes']['status'],
                    ];
                }
            }

            Log::warning('[PayMongo] Source creation failed', [
                'type' => $type,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::error('[PayMongo] Exception: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Retrieve a PayMongo source by ID to check its current status.
     */
    public function getSource(string $sourceId): ?array
    {
        $this->loadConfig();

        if (empty($this->secretKey)) {
            return null;
        }

        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->timeout(10)
                ->get(self::BASE_URL.'/sources/'.$sourceId);

            if ($response->successful()) {
                return $response->json()['data'] ?? null;
            }

            return null;
        } catch (\Throwable $e) {
            Log::error('[PayMongo] getSource Exception: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Turn a chargeable source into an actual payment. Until this succeeds no money has moved
     * (PayMongo expires unused chargeable sources after about an hour).
     *
     * @return array{id: string, status: string}|null
     */
    public function captureSource(string $sourceId, int $amount, string $description): ?array
    {
        $this->loadConfig();

        if (empty($this->secretKey)) {
            return null;
        }

        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->timeout(15)
                ->post(self::BASE_URL.'/payments', [
                    'data' => ['attributes' => [
                        'amount' => $amount,
                        'currency' => 'PHP',
                        'description' => $description,
                        'source' => ['id' => $sourceId, 'type' => 'source'],
                    ]],
                ]);

            if ($response->successful() && ($data = $response->json('data'))) {
                return ['id' => (string) $data['id'], 'status' => (string) ($data['attributes']['status'] ?? 'pending')];
            }

            Log::warning('[PayMongo] Payment capture failed', ['source' => $sourceId, 'status' => $response->status(), 'body' => $response->body()]);

            return null;
        } catch (\Throwable $e) {
            Log::error('[PayMongo] captureSource Exception: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Convert PHP peso amount (float) to centavos (int) for PayMongo.
     */
    public static function toCentavos(float $amount): int
    {
        return (int) round($amount * 100);
    }
}
