<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    public const STATUS_DRAFT     = 'draft';
    public const STATUS_SENT      = 'sent';
    public const STATUS_RECEIVED  = 'received';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'po_number',
        'supplier_id',
        'quotation_id',
        'pdf_path',
        'po_type',
        'is_conforme',
        'is_signature_verified',
        'conforme_signatory',
        'conforme_business_name',
        'conforme_date',
        'payment_terms',
        'status',
        'expected_delivery_date',
        'delivery_receipt_no',
        'sales_invoice_no',
        'dr_issued_at',
        'si_issued_at',
        'delivered_at',
        'subtotal',
        'tax',
        'total',
        'created_by_user_id',
        'received_at',
        'notes',
    ];

    protected $casts = [
        'is_conforme'            => 'boolean',
        'is_signature_verified'  => 'boolean',
        'conforme_date'          => 'date',
        'expected_delivery_date' => 'date',
        'dr_issued_at'           => 'datetime',
        'si_issued_at'           => 'datetime',
        'delivered_at'           => 'datetime',
        'received_at'            => 'datetime',
        'subtotal'               => 'decimal:2',
        'tax'                    => 'decimal:2',
        'total'                  => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (PurchaseOrder $po) {
            if (empty($po->po_number)) {
                $po->po_number = 'PO-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(4));
            }
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
