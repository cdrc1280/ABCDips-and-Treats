<?php

namespace App\Domains\Costing\Data;

use App\Shared\Support\RouteModel;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class RecipeData extends Data
{
    public function __construct(
        public string $name,
        public string $productId,
        public ?int $yieldQty = 1,
        public ?int $prepTimeMinutes = 20,
        public ?int $bakingTimeMinutes = 40,
        public ?string $instructions = null,
        public array $ingredients = [],
        public array $packaging = [],
        public ?float $overheadPct = 40,
        public ?float $markupPct = 66,
        public ?float $laborPct = 60,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['name'] = trim((string) ($properties['name'] ?? '')) ?: null;
        $properties['product_id'] = ($properties['product_id'] ?? '') === '' ? null : (string) $properties['product_id'];
        $properties['yield_qty'] = ($properties['yield_qty'] ?? '') === '' ? 1 : (int) $properties['yield_qty'];
        $properties['prep_time_minutes'] = ($properties['prep_time_minutes'] ?? '') === '' ? 20 : (int) $properties['prep_time_minutes'];
        $properties['baking_time_minutes'] = ($properties['baking_time_minutes'] ?? '') === '' ? 40 : (int) $properties['baking_time_minutes'];
        $properties['instructions'] = trim((string) ($properties['instructions'] ?? '')) ?: null;
        $properties['ingredients'] = array_values(array_filter((array) ($properties['ingredients'] ?? []), fn ($row) => is_array($row) && array_filter($row, fn ($v) => $v !== null && $v !== '') !== []));
        $properties['packaging'] = array_values(array_filter((array) ($properties['packaging'] ?? []), fn ($row) => is_array($row) && array_filter($row, fn ($v) => $v !== null && $v !== '') !== []));
        $properties['overhead_pct'] = ($properties['overhead_pct'] ?? '') === '' ? 40 : (float) $properties['overhead_pct'];
        $properties['markup_pct'] = ($properties['markup_pct'] ?? '') === '' ? 66 : (float) $properties['markup_pct'];
        $properties['labor_pct'] = ($properties['labor_pct'] ?? '') === '' ? 60 : (float) $properties['labor_pct'];

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $id = RouteModel::key('recipe');

        return [
            'name' => ['required', 'string', 'max:255'],
            'product_id' => ['bail', 'required', 'uuid', Rule::exists('products', 'id')],
            'yield_qty' => ['required', 'integer', 'min:1'],
            'prep_time_minutes' => ['required', 'integer', 'min:0'],
            'baking_time_minutes' => ['required', 'integer', 'min:0'],
            'instructions' => ['nullable', 'string', 'max:100000'],
            'ingredients' => ['array', 'max:100'],
            'ingredients.*.ingredient_id' => ['bail', 'required', 'uuid', Rule::exists('ingredients', 'id')],
            'ingredients.*.qty_required' => ['required', 'numeric', 'min:0'],
            'ingredients.*.unit' => ['required', 'string', 'max:255'],
            'packaging' => ['array', 'max:100'],
            'packaging.*.packaging_material_id' => ['bail', 'nullable', 'uuid', Rule::exists('packaging_materials', 'id')],
            'packaging.*.name' => ['required', 'string', 'max:255'],
            'packaging.*.unit' => ['required', 'string', 'max:255'],
            'packaging.*.package_qty' => ['required', 'numeric', 'min:0'],
            'packaging.*.package_cost' => ['required', 'numeric', 'min:0'],
            'packaging.*.qty_used' => ['required', 'numeric', 'min:0'],
            'overhead_pct' => ['required', 'numeric', 'min:0', 'max:1000'],
            'markup_pct' => ['required', 'numeric', 'min:0', 'max:1000'],
            'labor_pct' => ['required', 'numeric', 'min:0', 'max:1000'],
        ];
    }

    public static function messages(mixed ...$args): array
    {
        return [

        ];
    }

    public static function attributes(mixed ...$args): array
    {
        return [
            'name' => __('recipe name'),
            'product_id' => __('product'),
            'yield_qty' => __('yield per batch'),
            'prep_time_minutes' => __('prep time (min)'),
            'baking_time_minutes' => __('baking time (min)'),
            'instructions' => __('method'),
            'ingredients' => __('ingredients'),
            'packaging' => __('packaging'),
            'overhead_pct' => __('overhead %'),
            'markup_pct' => __('markup %'),
            'labor_pct' => __('labor %'),
        ];
    }
}
