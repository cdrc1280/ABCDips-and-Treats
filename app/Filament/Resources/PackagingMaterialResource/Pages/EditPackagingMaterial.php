<?php

namespace App\Filament\Resources\PackagingMaterialResource\Pages;

use App\Filament\Resources\PackagingMaterialResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPackagingMaterial extends EditRecord
{
    protected static string $resource = PackagingMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
