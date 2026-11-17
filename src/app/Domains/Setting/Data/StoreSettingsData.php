<?php

namespace App\Domains\Setting\Data;

use App\Domains\Setting\Support\StoreSettingsSchema;
use App\Rules\PhilippinePhone;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

/**
 * All settings in one payload: `values[key]` per field, `remove[key]` to clear an image.
 * Rules come from StoreSettingsSchema so the form, validation and save stay in sync.
 */
final class StoreSettingsData extends Data
{
    /**
     * @param  array<string, mixed>  $values
     * @param  array<string, bool>  $remove
     */
    public function __construct(
        public array $values = [],
        public array $remove = [],
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $values = (array) ($properties['values'] ?? []);
        foreach (StoreSettingsSchema::fields() as $key => $field) {
            if (! array_key_exists($key, $values)) {
                continue;
            }
            $values[$key] = match ($field['type']) {
                'bool' => filter_var($values[$key], FILTER_VALIDATE_BOOLEAN),
                'json_list' => array_values(array_filter((array) $values[$key], fn ($row) => is_array($row) && array_filter($row, fn ($v) => $v !== null && $v !== '') !== [])),
                'image' => $values[$key],
                default => is_string($values[$key]) ? trim($values[$key]) : $values[$key],
            };
        }
        $properties['values'] = $values;
        $properties['remove'] = array_map(fn ($v) => filter_var($v, FILTER_VALIDATE_BOOLEAN), (array) ($properties['remove'] ?? []));

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $rules = ['values' => ['required', 'array'], 'remove' => ['array']];

        foreach (StoreSettingsSchema::fields() as $key => $field) {
            $base = $field['required'] ? ['required'] : ['nullable'];
            $rules["values.{$key}"] = match ($field['type']) {
                'bool' => ['boolean'],
                'email' => [...$base, 'email', 'max:255'],
                'tel' => [...$base, 'string', new PhilippinePhone],
                'textarea' => [...$base, 'string', 'max:5000'],
                'number' => [...$base, 'numeric', 'min:0', 'max:1000000'],
                'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
                'select' => [...$base, 'in:'.implode(',', array_keys($field['options'] ?? []))],
                'json_list' => ['nullable', 'array', 'max:20'],
                default => [...$base, 'string', 'max:500'],
            };
            if ($field['type'] === 'json_list') {
                foreach (array_keys($field['columns']) as $column) {
                    $rules["values.{$key}.*.{$column}"] = [in_array($column, ['title', 'desc'], true) ? 'required' : 'nullable', 'string', 'max:1000'];
                }
            }
        }

        $rules['values.store_lat'] = ['nullable', 'numeric', 'between:-90,90'];
        $rules['values.store_lng'] = ['nullable', 'numeric', 'between:-180,180'];

        return $rules;
    }

    public static function attributes(mixed ...$args): array
    {
        $attributes = [];
        foreach (StoreSettingsSchema::fields() as $key => $field) {
            $attributes["values.{$key}"] = strtolower($field['label']);
        }

        return $attributes;
    }
}
