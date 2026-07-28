<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class GiftCard extends Model {
    protected $fillable = ['code','initial_balance','remaining_balance','status','purchased_by','redeemed_by','expires_at','redeemed_at'];
    protected $casts = ['initial_balance'=>'decimal:2','remaining_balance'=>'decimal:2','expires_at'=>'datetime','redeemed_at'=>'datetime'];
    public function purchasedBy(): BelongsTo { return $this->belongsTo(User::class,'purchased_by'); }
    public function redeemedBy(): BelongsTo { return $this->belongsTo(User::class,'redeemed_by'); }
}
