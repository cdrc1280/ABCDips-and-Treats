<?php

namespace App\Domains\Inventory\Actions;

use App\Domains\Inventory\Data\SupplierData;
use App\Domains\Inventory\Models\Supplier;
use Illuminate\Support\Facades\DB;

final class CreateSupplierAction
{
    public function execute(SupplierData $data): Supplier
    {
        return DB::transaction(function () use ($data) {
            $supplier = Supplier::query()->create([
                'name' => $data->name,
                'contact_person' => $data->contactPerson,
                'email' => $data->email,
                'phone' => $data->phone,
                'payment_terms' => $data->paymentTerms,
                'address' => $data->address,
                'notes' => $data->notes,
            ]);

            return $supplier;
        });
    }
}
