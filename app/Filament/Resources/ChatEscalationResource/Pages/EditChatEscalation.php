<?php

namespace App\Filament\Resources\ChatEscalationResource\Pages;

use App\Filament\Resources\ChatEscalationResource;
use Filament\Resources\Pages\EditRecord;

class EditChatEscalation extends EditRecord
{
    protected static string $resource = ChatEscalationResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['new_reply'])) {
            $reply = trim($data['new_reply']);
            $conversation = $this->record->conversation ?? [];
            $conversation[] = [
                'role' => 'admin',
                'content' => $reply,
                'time' => now()->format('M j, g:i A'),
            ];

            $data['conversation'] = $conversation;
            $data['status'] = 'in_progress';
            unset($data['new_reply']);
        }

        return $data;
    }
}
