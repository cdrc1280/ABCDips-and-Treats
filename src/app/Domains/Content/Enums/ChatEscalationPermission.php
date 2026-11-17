<?php

namespace App\Domains\Content\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum ChatEscalationPermission: string implements DefinesPermissions
{
    case ChatEscalationsView = 'chat-escalations-view';
    case ChatEscalationsEdit = 'chat-escalations-edit';
    case ChatEscalationsDelete = 'chat-escalations-delete';

    public function group(): string
    {
        return 'Engagement & Content';
    }

    public function description(): string
    {
        return match ($this) {
            self::ChatEscalationsView => 'View support escalations',
            self::ChatEscalationsEdit => 'Edit support escalations',
            self::ChatEscalationsDelete => 'Delete support escalations',
        };
    }
}
