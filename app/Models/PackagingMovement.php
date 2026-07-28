<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class PackagingMovement extends Model {
    protected $fillable = ['packaging_material_id','type','qty','unit_cost','reference_type','reference_id','notes','user_id'];
    public function material(): BelongsTo { return $this->belongsTo(PackagingMaterial::class,'packaging_material_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
