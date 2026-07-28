<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use InvalidArgumentException;

class PaymentManager
{
    /**
     * Resolve a payment gateway instance by method key.
     */
    public function driver(string $method): PaymentGatewayInterface
    {
        return match (strtolower($method)) {
            'gcash'         => new GCashGateway(),
            'maya'          => new MayaGateway(),
            'bank_transfer' => new BankTransferGateway(),
            default         => throw new InvalidArgumentException("Unsupported payment method: {$method}"),
        };
    }
}
