<?php

namespace App\Domains\Content\Actions;

use App\Domains\Content\Data\ChatEscalationData;
use App\Domains\Content\Models\ChatEscalation;
use Illuminate\Support\Facades\DB;

final class UpdateChatEscalationAction
{
    public function execute(ChatEscalation $chatEscalation, ChatEscalationData $data): ChatEscalation
    {
        return DB::transaction(function () use ($chatEscalation, $data) {
            $chatEscalation->update([
                'guest_name' => $data->guestName,
                'guest_email' => $data->guestEmail,
                'status' => $data->status,
                'admin_notes' => $data->adminNotes,
            ]);

            return $chatEscalation;
        });
    }
}
