<?php

namespace App\Services;

use App\Models\CustomOrder;
use App\Models\User;
use Illuminate\Support\Str;

class CustomOrderService
{
    public function createInquiry(array $data, ?User $user = null): CustomOrder
    {
        $refNumber = 'CUST-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        $order = CustomOrder::create([
            'reference_number'  => $refNumber,
            'user_id'           => $user?->id,
            'customer_name'     => $data['customer_name'],
            'customer_email'    => $data['customer_email'],
            'customer_phone'    => $data['customer_phone'],
            'event_date'        => $data['event_date'],
            'servings_count'    => (int) ($data['servings_count'] ?? 20),
            'tiers_count'       => (int) ($data['tiers_count'] ?? 1),
            'flavor_preference' => $data['flavor_preference'] ?? null,
            'frosting_type'     => $data['frosting_type'] ?? null,
            'theme_description' => $data['theme_description'],
            'budget_range_min'  => $data['budget_range_min'] ?? null,
            'budget_range_max'  => $data['budget_range_max'] ?? null,
            'status'            => CustomOrder::STATUS_INQUIRY,
        ]);

        if (!empty($data['reference_photos'])) {
            foreach ($data['reference_photos'] as $photo) {
                if ($photo instanceof \Illuminate\Http\UploadedFile) {
                    $order->addMedia($photo)->toMediaCollection('reference_photos');
                }
            }
        }

        return $order;
    }
}
