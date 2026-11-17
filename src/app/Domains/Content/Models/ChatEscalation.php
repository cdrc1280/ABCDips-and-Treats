<?php

namespace App\Domains\Content\Models;

use App\Concerns\InteractsWithModelFactory;
use App\Domains\Content\Data\ChatEscalationFilterData;
use App\Domains\User\Models\User;
use App\Shared\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatEscalation extends Model
{
    use HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes;

    protected $fillable = [
        'user_id',
        'guest_name',
        'guest_email',
        'conversation',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'conversation' => 'array',
        'status' => 'string',
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * string → global search; array → ChatEscalationFilterData fields.
     *
     * @param  Builder<ChatEscalation>  $query
     * @return Builder<ChatEscalation>
     */
    public function scopeSearch(Builder $query, mixed $search): Builder
    {
        if (is_string($search)) {
            return $query->where(fn (Builder $q) => $q
                ->whereLike('guest_name', "%{$search}%")
                ->orWhereLike('guest_email', "%{$search}%")
                ->orWhereLike('admin_notes', "%{$search}%")
                ->orWhereHas('user', fn (Builder $r) => $r->whereLike('name', "%{$search}%"))
                ->orWhereHas('user', fn (Builder $r) => $r->whereLike('email', "%{$search}%")));
        }

        $filters = ChatEscalationFilterData::from((array) $search);

        return $query
            ->when($filters->keyword, fn (Builder $q, string $keyword) => $q->search($keyword))
            ->when($filters->status !== null && $filters->status !== '', fn (Builder $q) => $q->where('status', $filters->status));
    }
}
