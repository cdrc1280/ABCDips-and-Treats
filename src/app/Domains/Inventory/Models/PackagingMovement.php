<?php

namespace App\Domains\Inventory\Models;

use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackagingMovement extends Model
{
    use HasUuids;

    protected $fillable = ['packaging_material_id', 'type', 'qty', 'unit_cost', 'reference_type', 'reference_id', 'notes', 'user_id'];

    /** @return BelongsTo<PackagingMaterial, $this> */
    public function material(): BelongsTo
    {
        return $this->belongsTo(PackagingMaterial::class, 'packaging_material_id');
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
