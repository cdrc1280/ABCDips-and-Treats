<?php

namespace App\Filament\Resources\ProductCostingResource\Pages;

use App\Filament\Resources\ProductCostingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProductCosting extends CreateRecord
{
    protected static string $resource = ProductCostingResource::class;

    public function mount(): void
    {
        parent::mount();

        $productId = request()->query('product_id');
        if ($productId) {
            $this->form->fill([
                'product_id' => (int) $productId,
            ]);
        }
    }
}
