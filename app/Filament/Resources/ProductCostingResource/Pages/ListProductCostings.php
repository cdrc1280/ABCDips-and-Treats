<?php

namespace App\Filament\Resources\ProductCostingResource\Pages;

use App\Filament\Resources\ProductCostingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductCostings extends ListRecords
{
    protected static string $resource = ProductCostingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
