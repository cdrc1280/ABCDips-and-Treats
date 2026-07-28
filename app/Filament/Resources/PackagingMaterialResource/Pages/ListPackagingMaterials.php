<?php

namespace App\Filament\Resources\PackagingMaterialResource\Pages;

use App\Filament\Resources\PackagingMaterialResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPackagingMaterials extends ListRecords
{
    protected static string $resource = PackagingMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
