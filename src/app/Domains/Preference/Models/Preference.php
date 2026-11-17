<?php

namespace App\Domains\Preference\Models;

use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Guarded(['id'])]
class Preference extends Model
{
    use HasUuids;

    protected $table = 'user_preferences';

    protected $casts = ['value' => 'array'];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getFor(User $user, string $key, mixed $default = null): mixed
    {
        return static::query()->where('user_id', $user->id)->where('key', $key)->value('value') ?? $default;
    }

    public static function putFor(User $user, string $key, mixed $value): void
    {
        static::query()->updateOrCreate(['user_id' => $user->id, 'key' => $key], ['value' => $value]);
    }
}
