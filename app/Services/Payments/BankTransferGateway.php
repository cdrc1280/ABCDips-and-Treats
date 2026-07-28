<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Support\Str;

class BankTransferGateway implements PaymentGatewayInterface
{
    public function charge(Order $order, array $paymentData = []): array
    {
        $reference = 'BANK-' . strtoupper(Str::random(8));

        return [
            'success'      => true,
            'reference'    => $reference,
            'checkout_url' => null,
            'status'       => 'pending',
            'message'      => 'Bank transfer instructions generated. Please transfer funds to BDO 0012-3456-7890 (ABCDips & Treats).',
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
        return 'Bank Transfer';
    }
}
