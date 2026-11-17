<?php

namespace App\Domains\Content\Actions;

use App\Domains\Content\Models\ChatEscalation;
use Illuminate\Support\Facades\DB;

/** Reopen. */
final class ReopenChatEscalationAction
{
    public function execute(ChatEscalation $chatEscalation): ChatEscalation
    {
        DB::transaction(function () use ($chatEscalation) {
            $chatEscalation->update(['status' => 'open']);
        });

        return $chatEscalation->refresh();
    }
}
