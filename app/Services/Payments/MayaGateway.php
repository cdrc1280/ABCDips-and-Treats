<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Support\Str;

class MayaGateway implements PaymentGatewayInterface
{
    public function charge(Order $order, array $paymentData = []): array
    {
        $isLive = config('services.payments.live', false);
        $reference = 'MAYA-' . strtoupper(Str::random(10));

        if (! $isLive) {
            // Sandbox Mode
            return [
                'success'      => true,
                'reference'    => $reference,
                'checkout_url' => url("/checkout/maya-sandbox?ref={$reference}&amount={$order->total}"),
                'status'       => 'paid',
                'message'      => 'Maya sandbox payment processed successfully.',
            ];
        }

        return [
            'success'      => false,
            'reference'    => $reference,
            'checkout_url' => null,
            'status'       => 'pending',
            'message'      => 'Live Maya gateway requires production credentials.',
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
        return 'Maya';
    }
}
