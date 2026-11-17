<?php

namespace App\Domains\Content\Models;

use App\Concerns\InteractsWithModelFactory;
use App\Domains\Content\Data\SuggestionFilterData;
use App\Domains\User\Models\User;
use App\Shared\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Suggestion extends Model
{
    use HasUuids, InteractsWithModelFactory, LogsModelActivity, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'category',
        'subject',
        'message',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'category' => 'string',
        'status' => 'string',
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * string → global search; array → SuggestionFilterData fields.
     *
     * @param  Builder<Suggestion>  $query
     * @return Builder<Suggestion>
     */
    public function scopeSearch(Builder $query, mixed $search): Builder
    {
        if (is_string($search)) {
            return $query->where(fn (Builder $q) => $q
                ->whereLike('name', "%{$search}%")
                ->orWhereLike('email', "%{$search}%")
                ->orWhereLike('subject', "%{$search}%")
                ->orWhereLike('message', "%{$search}%"));
        }

        $filters = SuggestionFilterData::from((array) $search);

        return $query
            ->when($filters->keyword, fn (Builder $q, string $keyword) => $q->search($keyword))
            ->when($filters->status !== null && $filters->status !== '', fn (Builder $q) => $q->where('status', $filters->status))
            ->when($filters->category !== null && $filters->category !== '', fn (Builder $q) => $q->where('category', $filters->category));
    }
}
