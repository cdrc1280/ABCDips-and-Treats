<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class PackagingMaterial extends Model {
    protected $fillable = ['name','sku','type','unit','cost_per_unit','stock_qty','min_stock_qty','supplier_id','notes','is_active'];
    protected $casts = ['cost_per_unit'=>'decimal:2','stock_qty'=>'decimal:2','min_stock_qty'=>'decimal:2','is_active'=>'boolean'];
    public function movements(): HasMany { return $this->hasMany(PackagingMovement::class); }
    public function isLowStock(): bool { return $this->stock_qty <= $this->min_stock_qty; }
}
