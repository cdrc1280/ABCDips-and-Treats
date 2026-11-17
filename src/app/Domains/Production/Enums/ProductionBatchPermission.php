<?php

namespace App\Domains\Production\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum ProductionBatchPermission: string implements DefinesPermissions
{
    case ProductionBatchesView = 'production-batches-view';
    case ProductionBatchesCreate = 'production-batches-create';
    case ProductionBatchesEdit = 'production-batches-edit';
    case ProductionBatchesDelete = 'production-batches-delete';

    public function group(): string
    {
        return 'Production & Purchasing';
    }

    public function description(): string
    {
        return match ($this) {
            self::ProductionBatchesView => 'View production batches',
            self::ProductionBatchesCreate => 'Create production batches',
            self::ProductionBatchesEdit => 'Edit production batches',
            self::ProductionBatchesDelete => 'Delete production batches',
        };
    }
}
