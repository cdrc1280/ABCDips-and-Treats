<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    protected $casts = [];

    /**
     * Get a setting value by key, with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting:{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return ($setting && $setting->value !== null) ? $setting->value : $default;
        });
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
        Cache::forget("setting:{$key}");
    }

    /**
     * Get a setting value as decoded JSON array or default.
     */
    public static function getJson(string $key, array $default = []): array
    {
        $value = static::get($key);
        if (is_array($value)) return $value;
        if (is_string($value) && !empty($value)) {
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
