<?php

namespace App\Filament\Resources\CustomOrderResource\Pages;

use App\Filament\Resources\CustomOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomOrder extends CreateRecord
{
    protected static string $resource = CustomOrderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
