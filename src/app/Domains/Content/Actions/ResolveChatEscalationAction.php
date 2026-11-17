<?php

namespace App\Domains\Content\Actions;

use App\Domains\Content\Models\ChatEscalation;
use Illuminate\Support\Facades\DB;

/** Mark resolved. */
final class ResolveChatEscalationAction
{
    public function execute(ChatEscalation $chatEscalation): ChatEscalation
    {
        DB::transaction(function () use ($chatEscalation) {
            $conversation = (array) ($chatEscalation->conversation ?? []);
            $conversation[] = ['role' => 'admin', 'content' => __('This conversation was marked as resolved by our team.'), 'time' => now()->format('M j, g:i A')];
            $chatEscalation->update(['conversation' => $conversation, 'status' => 'resolved']);
        });

        return $chatEscalation->refresh();
    }
}
