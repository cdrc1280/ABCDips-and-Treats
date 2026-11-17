<?php

namespace App\Domains\User\Actions;

use App\Domains\User\Data\CustomerData;
use App\Domains\User\Models\User;
use Illuminate\Support\Facades\DB;

final class UpdateCustomerAction
{
    public function execute(User $customer, CustomerData $data): User
    {
        return DB::transaction(function () use ($customer, $data) {
            $customer->update([
                'name' => $data->name,
                'email' => $data->email,
                'phone' => $data->phone,
                'street_address' => $data->streetAddress,
                'barangay' => $data->barangay,
                'city' => $data->city,
                'province' => $data->province,
                'region' => $data->region,
            ]);

            return $customer;
        });
    }
}
