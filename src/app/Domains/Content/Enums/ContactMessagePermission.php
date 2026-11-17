<?php

namespace App\Domains\Content\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum ContactMessagePermission: string implements DefinesPermissions
{
    case ContactMessagesView = 'contact-messages-view';
    case ContactMessagesEdit = 'contact-messages-edit';
    case ContactMessagesDelete = 'contact-messages-delete';

    public function group(): string
    {
        return 'Engagement & Content';
    }

    public function description(): string
    {
        return match ($this) {
            self::ContactMessagesView => 'View contact messages',
            self::ContactMessagesEdit => 'Edit contact messages',
            self::ContactMessagesDelete => 'Delete contact messages',
        };
    }
}
