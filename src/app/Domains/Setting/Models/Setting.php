<?php

namespace App\Domains\Setting\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasUuids;

    protected $fillable = ['key', 'value', 'group'];

    protected $casts = [];

    /**
     * Get a setting value by key, with optional default.
     * Only the stored value is cached (wrapped, so "not set" is cached too); the default is applied after,
     * so a changed default in code shows immediately and callers with different defaults never share one.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        /** @var array{v: ?string} $cached */
        $cached = Cache::remember(self::cacheKey($key), 3600, fn () => ['v' => static::where('key', $key)->value('value')]);

        return $cached['v'] ?? $default;
    }

    private static function cacheKey(string $key): string
    {
        return "setting:v2:{$key}";
    }

    /**
     * Set a setting value, creating or updating as needed.
     */
    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );
        Cache::forget(self::cacheKey($key));
    }

    /**
     * Get a setting value as decoded JSON array or default.
     */
    public static function getJson(string $key, array $default = []): array
    {
        $value = static::get($key);
        if (is_array($value)) {
            return $value;
        }
        if (is_string($value) && ! empty($value)) {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : $default;
        }

        return $default;
    }

    /**
     * Set a setting value as JSON string.
     */
    public static function setJson(string $key, array $value, string $group = 'general'): void
    {
        static::set($key, json_encode($value), $group);
    }

    /**
     * Get all settings as a flat key => value array, optionally filtered by group.
     */
    public static function getAllByGroup(string $group): array
    {
        return static::where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }
}
