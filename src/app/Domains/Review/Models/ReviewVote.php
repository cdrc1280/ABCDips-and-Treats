<?php

namespace App\Domains\Review\Models;

use App\Concerns\InteractsWithModelFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewVote extends Model
{
    use HasUuids, InteractsWithModelFactory;

    protected $fillable = ['review_id', 'user_id', 'ip_address'];

    /** @return BelongsTo<Review, $this> */
    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }
}
