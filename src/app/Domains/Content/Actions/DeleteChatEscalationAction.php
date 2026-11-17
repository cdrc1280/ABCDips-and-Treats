<?php

namespace App\Domains\Content\Actions;

use App\Domains\Content\Models\ChatEscalation;
use Illuminate\Support\Facades\DB;

final class DeleteChatEscalationAction
{
    public function execute(ChatEscalation $chatEscalation): void
    {
        DB::transaction(fn () => $chatEscalation->delete());
    }
}
