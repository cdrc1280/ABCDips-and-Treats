<?php

namespace App\Filament\Resources\PackagingMaterialResource\Pages;

use App\Filament\Resources\PackagingMaterialResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePackagingMaterial extends CreateRecord
{
    protected static string $resource = PackagingMaterialResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
