<?php

namespace App\Filament\Resources\ProductCostingResource\Pages;

use App\Filament\Resources\ProductCostingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProductCosting extends EditRecord
{
    protected static string $resource = ProductCostingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
