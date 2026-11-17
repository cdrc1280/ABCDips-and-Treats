<?php

namespace App\Domains\Analytics\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum AiAdvisorPermission: string implements DefinesPermissions
{
    case AiAdvisorUse = 'ai-advisor-use';

    public function group(): string
    {
        return 'Analytics';
    }

    public function description(): string
    {
        return 'Ask the AI bakery advisor about sales, stock and pricing';
    }
}
