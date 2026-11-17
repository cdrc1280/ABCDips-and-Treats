<?php

namespace App\Domains\Content\Actions;

use App\Domains\Content\Models\ChatEscalation;
use Illuminate\Support\Facades\DB;

/** Reply. */
final class ReplyChatEscalationAction
{
    public function execute(ChatEscalation $chatEscalation, mixed $value): ChatEscalation
    {
        DB::transaction(function () use ($chatEscalation, $value) {
            $conversation = (array) ($chatEscalation->conversation ?? []);
            $conversation[] = ['role' => 'admin', 'content' => trim((string) $value), 'time' => now()->format('M j, g:i A')];
            $chatEscalation->update(['conversation' => $conversation, 'status' => 'in_progress']);
        });

        return $chatEscalation->refresh();
    }
}
