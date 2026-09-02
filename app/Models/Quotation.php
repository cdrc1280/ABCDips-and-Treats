<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Quotation extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SENT = 'sent';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CONVERTED = 'converted_to_po';

    protected $fillable = [
        'quotation_number',
        'client_name',
        'supplier_id',
        'status',
        'quotation_date',
        'valid_until',
        'subtotal',
        'tax',
        'total',
        'created_by_user_id',
        'notes',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'valid_until'    => 'date',
        'subtotal'       => 'decimal:2',
        'tax'            => 'decimal:2',
        'total'          => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Quotation $quotation) {
            if (empty($quotation->quotation_number)) {
                $quotation->quotation_number = 'QT-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            }
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
