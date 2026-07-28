<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Support\Str;

class CodGateway implements PaymentGatewayInterface
{
    public function charge(Order $order, array $paymentData = []): array
    {
        $reference = 'COD-' . strtoupper(Str::random(8));

        return [
            'success'      => true,
            'reference'    => $reference,
            'checkout_url' => null,
            'status'       => 'pending',
            'message'      => 'Cash on Delivery selected. Please prepare the exact cash amount upon delivery.',
        ];
    }

    public function verify(string $reference): array
    {
        return [
            'reference' => $reference,
            'status'    => 'pending',
            'verified'  => false,
        ];
    }

    public function getName(): string
    {
        return 'Cash on Delivery';
    }
}
