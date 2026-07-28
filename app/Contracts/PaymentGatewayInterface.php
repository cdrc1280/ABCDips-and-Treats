<?php

namespace App\Contracts;

use App\Models\Order;

interface PaymentGatewayInterface
{
    /**
     * Process checkout payment for an order.
     * Returns payment reference, checkout URL, and status.
     *
     * @return array{
     *   success: bool,
     *   reference: string,
     *   checkout_url: ?string,
     *   status: string,
     *   message: string
     * }
     */
    public function charge(Order $order, array $paymentData = []): array;

    /**
     * Verify payment status using reference ID.
     */
    public function verify(string $reference): array;

    public function getName(): string;
}
