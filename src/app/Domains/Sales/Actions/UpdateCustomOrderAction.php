<?php

namespace App\Domains\Sales\Actions;

use App\Domains\Sales\Data\CustomOrderData;
use App\Domains\Sales\Models\CustomOrder;
use Illuminate\Support\Facades\DB;

final class UpdateCustomOrderAction
{
    public function execute(CustomOrder $customOrder, CustomOrderData $data): CustomOrder
    {
        return DB::transaction(function () use ($customOrder, $data) {
            $customOrder->update([
                'reference_number' => $data->referenceNumber ?? $customOrder->reference_number,
                'customer_name' => $data->customerName,
                'customer_email' => $data->customerEmail,
                'customer_phone' => $data->customerPhone,
                'event_date' => $data->eventDate,
                'servings_count' => $data->servingsCount,
                'tiers_count' => $data->tiersCount,
                'flavor_preference' => $data->flavorPreference,
                'frosting_type' => $data->frostingType,
                'theme_description' => $data->themeDescription,
                'budget_range_min' => $data->budgetRangeMin,
                'budget_range_max' => $data->budgetRangeMax,
                'quoted_price' => $data->quotedPrice,
                'status' => $data->status,
                'staff_notes' => $data->staffNotes,
            ]);

            return $customOrder;
        });
    }
}
