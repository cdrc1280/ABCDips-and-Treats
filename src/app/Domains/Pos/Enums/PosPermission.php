<?php

namespace App\Domains\Pos\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum PosPermission: string implements DefinesPermissions
{
    case PosUse = 'pos-use';

    public function group(): string
    {
        return 'Orders & Sales';
    }

    public function description(): string
    {
        return 'Ring up walk-in sales at the POS register';
    }
}
