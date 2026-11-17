<?php

namespace App\Domains\Purchasing\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum QuotationPermission: string implements DefinesPermissions
{
    case QuotationsView = 'quotations-view';
    case QuotationsCreate = 'quotations-create';
    case QuotationsEdit = 'quotations-edit';
    case QuotationsDelete = 'quotations-delete';

    public function group(): string
    {
        return 'Production & Purchasing';
    }

    public function description(): string
    {
        return match ($this) {
            self::QuotationsView => 'View quotations',
            self::QuotationsCreate => 'Create quotations',
            self::QuotationsEdit => 'Edit quotations',
            self::QuotationsDelete => 'Delete quotations',
        };
    }
}
