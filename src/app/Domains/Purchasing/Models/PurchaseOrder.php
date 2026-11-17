<?php

namespace App\Domains\Purchasing\Models;

use App\Concerns\InteractsWithModelFactory;
use App\Domains\Inventory\Models\Supplier;
use App\Domains\Purchasing\Data\PurchaseOrderFilterData;
use App\Domains\User\Models\User;
use App\Shared\Concerns\FreesUniqueValuesOnDelete;
use App\Shared\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PurchaseOrder extends Model
{
    use FreesUniqueValuesOnDelete, HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes;

    /** @var list<string> */
    protected array $uniqueOnDelete = ['po_number'];

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SENT = 'sent';

    public const STATUS_RECEIVED = 'received';

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
        'is_conforme' => 'boolean',
        'is_signature_verified' => 'boolean',
        'conforme_date' => 'date',
        'expected_delivery_date' => 'date',
        'dr_issued_at' => 'datetime',
        'si_issued_at' => 'datetime',
        'delivered_at' => 'datetime',
        'received_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (PurchaseOrder $po) {
            if (empty($po->po_number)) {
                $po->po_number = 'PO-'.date('Ymd').'-'.strtoupper(Str::random(4));
            }
        });
    }

    /** @return BelongsTo<Supplier, $this> */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /** @return BelongsTo<Quotation, $this> */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    /** @return HasMany<PurchaseOrderItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /** @return BelongsTo<User, $this> */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * string → global search; array → PurchaseOrderFilterData fields.
     *
     * @param  Builder<PurchaseOrder>  $query
     * @return Builder<PurchaseOrder>
     */
    public function scopeSearch(Builder $query, mixed $search): Builder
    {
        if (is_string($search)) {
            return $query->where(fn (Builder $q) => $q
                ->whereLike('po_number', "%{$search}%")
                ->orWhereLike('notes', "%{$search}%")
                ->orWhereLike('delivery_receipt_no', "%{$search}%")
                ->orWhereLike('sales_invoice_no', "%{$search}%")
                ->orWhereHas('supplier', fn (Builder $r) => $r->whereLike('name', "%{$search}%")));
        }

        $filters = PurchaseOrderFilterData::from((array) $search);

        return $query
            ->when($filters->keyword, fn (Builder $q, string $keyword) => $q->search($keyword))
            ->when($filters->status !== null && $filters->status !== '', fn (Builder $q) => $q->where('status', $filters->status))
            ->when($filters->supplierId !== null, fn (Builder $q) => $q->where('supplier_id', $filters->supplierId));
    }
}
