<?php

namespace App\Domains\Purchasing\Models;

use App\Concerns\InteractsWithModelFactory;
use App\Domains\Inventory\Models\Supplier;
use App\Domains\Purchasing\Data\QuotationFilterData;
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

class Quotation extends Model
{
    use FreesUniqueValuesOnDelete, HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes;

    /** @var list<string> */
    protected array $uniqueOnDelete = ['quotation_number'];

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
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Quotation $quotation) {
            if (empty($quotation->quotation_number)) {
                $quotation->quotation_number = 'QT-'.date('Ymd').'-'.strtoupper(Str::random(4));
            }
        });
    }

    /** @return BelongsTo<Supplier, $this> */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /** @return HasMany<QuotationItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    /** @return HasMany<PurchaseOrder, $this> */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /** @return BelongsTo<User, $this> */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * string → global search; array → QuotationFilterData fields.
     *
     * @param  Builder<Quotation>  $query
     * @return Builder<Quotation>
     */
    public function scopeSearch(Builder $query, mixed $search): Builder
    {
        if (is_string($search)) {
            return $query->where(fn (Builder $q) => $q
                ->whereLike('quotation_number', "%{$search}%")
                ->orWhereLike('client_name', "%{$search}%")
                ->orWhereLike('notes', "%{$search}%")
                ->orWhereHas('supplier', fn (Builder $r) => $r->whereLike('name', "%{$search}%")));
        }

        $filters = QuotationFilterData::from((array) $search);

        return $query
            ->when($filters->keyword, fn (Builder $q, string $keyword) => $q->search($keyword))
            ->when($filters->status !== null && $filters->status !== '', fn (Builder $q) => $q->where('status', $filters->status))
            ->when($filters->supplierId !== null, fn (Builder $q) => $q->where('supplier_id', $filters->supplierId));
    }
}
