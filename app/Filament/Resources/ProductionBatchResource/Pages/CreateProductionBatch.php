<?php

namespace App\Filament\Resources\ProductionBatchResource\Pages;

use App\Filament\Resources\ProductionBatchResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProductionBatch extends CreateRecord
{
    protected static string $resource = ProductionBatchResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
