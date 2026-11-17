<?php

namespace App\Domains\Access\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Permission\Models\Permission as SpatiePermission;

/** App permission model (config/permission.php → models.permission): Spatie's, with UUID keys. */
class Permission extends SpatiePermission
{
    use HasUuids;
}
