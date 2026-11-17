<?php

namespace App\Domains\Content\Models;

use App\Domains\Content\Data\BlogPostFilterData;
use App\Domains\User\Models\User;
use App\Shared\Concerns\FreesUniqueValuesOnDelete;
use App\Shared\Concerns\LogsModelActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPost extends Model
{
    use FreesUniqueValuesOnDelete, HasUuids, LogsModelActivity, SoftDeletes;

    /** @var list<string> */
    protected array $uniqueOnDelete = ['slug'];

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'cover_image',
        'status',
        'category',
        'tags',
        'seo_title',
        'seo_description',
        'author_id',
        'published_at',
    ];

    protected $casts = ['tags' => 'array', 'published_at' => 'datetime'];

    /** @return BelongsTo<User, $this> */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * string → global search; array → BlogPostFilterData fields.
     *
     * @param  Builder<BlogPost>  $query
     * @return Builder<BlogPost>
     */
    public function scopeSearch(Builder $query, mixed $search): Builder
    {
        if (is_string($search)) {
            return $query->where(fn (Builder $q) => $q
                ->whereLike('title', "%{$search}%")
                ->orWhereLike('slug', "%{$search}%")
                ->orWhereLike('excerpt', "%{$search}%")
                ->orWhereLike('category', "%{$search}%"));
        }

        $filters = BlogPostFilterData::from((array) $search);

        return $query
            ->when($filters->keyword, fn (Builder $q, string $keyword) => $q->search($keyword))
            ->when($filters->status !== null && $filters->status !== '', fn (Builder $q) => $q->where('status', $filters->status));
    }
}
