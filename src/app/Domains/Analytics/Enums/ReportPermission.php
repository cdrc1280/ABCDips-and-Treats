<?php

namespace App\Domains\Analytics\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum ReportPermission: string implements DefinesPermissions
{
    case ReportsExport = 'reports-export';

    public function group(): string
    {
        return 'Analytics';
    }

    public function description(): string
    {
        return 'Download business reports (PDF, Excel, Word)';
    }
}
