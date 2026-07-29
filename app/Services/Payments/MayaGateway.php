<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Services\PayMongoService;
use Illuminate\Support\Str;

class MayaGateway implements PaymentGatewayInterface
{
    public function charge(Order $order, array $paymentData = []): array
    {
        $reference = 'MAYA-' . strtoupper(Str::random(10));

        $service = new PayMongoService();
        $result  = $service->createSource(
            type:    'paymaya',
            amount:  PayMongoService::toCentavos((float) $order->total),
            orderId: (string) $order->id,
            email:   $order->customer_email ?? '',
            name:    $order->customer_name  ?? 'Customer',
        );

        if ($result) {
            return [
                'success'      => true,
                'reference'    => $result['source_id'] ?? $reference,
                'checkout_url' => $result['checkout_url'],
                'status'       => 'awaiting_payment',
                'message'      => 'Maya payment initiated. Please complete via the Maya app.',
                'provider'     => 'paymongo',
            ];
        }

        // Fallback: PayMongo not configured yet — sandbox stub
        return [
            'success'      => true,
            'reference'    => $reference,
            'checkout_url' => url("/checkout/payment-success?order={$order->order_number}&method=maya"),
            'status'       => 'awaiting_payment',
            'message'      => 'Maya sandbox (PayMongo keys not set). Configure paymongo_secret_key in Admin → Settings.',
            'provider'     => 'sandbox',
        ];
    }

    public function verify(string $reference): array
    {
        $service = new PayMongoService();
        $source  = $service->getSource($reference);

        if ($source) {
            $status = $source['attributes']['status'] ?? 'unknown';
            return [
                'reference' => $reference,
                'status'    => $status === 'chargeable' ? 'paid' : $status,
                'verified'  => $status === 'chargeable',
            ];
        }

        return ['reference' => $reference, 'status' => 'unknown', 'verified' => false];
    }

    public function getName(): string
    {
        return 'Maya';
    }
}
