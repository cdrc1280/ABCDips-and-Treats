<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Str;

class BankTransferGateway implements PaymentGatewayInterface
{
    public function charge(Order $order, array $paymentData = []): array
    {
        $reference    = 'BANK-' . strtoupper(Str::random(8));
        $bankName     = Setting::get('bank_name', 'BDO');
        $accountName  = Setting::get('bank_account_name', 'ABCDips & Treats');
        $accountNo    = Setting::get('bank_account_number', 'Not configured');

        return [
            'success'         => true,
            'reference'       => $reference,
            'checkout_url'    => null,
            'status'          => 'pending',
            'message'         => "Please transfer ₱{$order->total} to {$bankName} account {$accountNo} ({$accountName}). Use your order number {$order->order_number} as the reference.",
            'bank_name'       => $bankName,
            'account_name'    => $accountName,
            'account_number'  => $accountNo,
            'transfer_amount' => $order->total,
            'reference_note'  => $order->order_number,
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
