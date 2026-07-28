<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Support\Str;

class GCashGateway implements PaymentGatewayInterface
{
    public function charge(Order $order, array $paymentData = []): array
    {
        $isLive = config('services.payments.live', false);
        $reference = 'GCASH-' . strtoupper(Str::random(10));

        if (! $isLive) {
            // Sandbox Mode
            return [
                'success'      => true,
                'reference'    => $reference,
                'checkout_url' => url("/checkout/gcash-sandbox?ref={$reference}&amount={$order->total}"),
                'status'       => 'paid',
                'message'      => 'GCash sandbox payment processed successfully.',
            ];
        }

        // Live API integration placeholder (requires production GCash PayMongo/Direct credentials)
        return [
            'success'      => false,
            'reference'    => $reference,
            'checkout_url' => null,
            'status'       => 'pending',
            'message'      => 'Live GCash gateway requires production credentials.',
        ];
    }

    public function verify(string $reference): array
    {
        return [
            'reference' => $reference,
            'status'    => 'paid',
            'verified'  => true,
        ];
    }

    public function getName(): string
    {
        return 'GCash';
    }
}
