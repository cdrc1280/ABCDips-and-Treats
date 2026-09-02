<?php

namespace App\Filament\Resources\QuotationResource\Pages;

use App\Filament\Resources\QuotationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditQuotation extends EditRecord
{
    protected static string $resource = QuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
