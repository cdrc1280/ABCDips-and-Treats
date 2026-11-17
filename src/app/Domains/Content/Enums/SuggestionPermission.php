<?php

namespace App\Domains\Content\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum SuggestionPermission: string implements DefinesPermissions
{
    case SuggestionsView = 'suggestions-view';
    case SuggestionsEdit = 'suggestions-edit';
    case SuggestionsDelete = 'suggestions-delete';

    public function group(): string
    {
        return 'Engagement & Content';
    }

    public function description(): string
    {
        return match ($this) {
            self::SuggestionsView => 'View suggestions',
            self::SuggestionsEdit => 'Edit suggestions',
            self::SuggestionsDelete => 'Delete suggestions',
        };
    }
}
