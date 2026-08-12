<?php

namespace App\Filament\Resources\DeliveryPoolResource\Pages;

use App\Filament\Resources\DeliveryPoolResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeliveryPools extends ListRecords
{
    protected static string $resource = DeliveryPoolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New Delivery Pool Batch 🤝'),
        ];
    }
}
