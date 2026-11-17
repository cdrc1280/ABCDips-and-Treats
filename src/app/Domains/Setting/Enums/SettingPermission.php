<?php

namespace App\Domains\Setting\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum SettingPermission: string implements DefinesPermissions
{
    case SettingsView = 'settings-view';
    case SettingsEdit = 'settings-edit';

    public function group(): string
    {
        return 'Configuration';
    }

    public function description(): string
    {
        return match ($this) {
            self::SettingsView => 'View store settings',
            self::SettingsEdit => 'Change store settings, payment and delivery keys',
        };
    }
}
