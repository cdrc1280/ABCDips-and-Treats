<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ContactMessage extends Model {
    protected $fillable = ['name','email','phone','subject','message','status','admin_notes','replied_by','replied_at'];
    protected $casts = ['replied_at'=>'datetime'];
    public function repliedBy(): BelongsTo { return $this->belongsTo(User::class,'replied_by'); }
}
