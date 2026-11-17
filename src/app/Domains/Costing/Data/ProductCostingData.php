<?php

namespace App\Domains\Costing\Data;

use App\Services\UnitConverterService;
use App\Shared\Support\RouteModel;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class ProductCostingData extends Data
{
    public function __construct(
        public ?string $name = null,
        public ?string $productId = null,
        public ?float $yieldQty = 1,
        public ?string $yieldUnit = 'tub (70g)',
        public array $items = [],
        public ?float $overheadPct = 40,
        public ?float $markupPct = 66,
        public ?float $laborPct = 60,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['name'] = trim((string) ($properties['name'] ?? '')) ?: null;
        $properties['product_id'] = ($properties['product_id'] ?? '') === '' ? null : (string) $properties['product_id'];
        $properties['yield_qty'] = ($properties['yield_qty'] ?? '') === '' ? 1 : (float) $properties['yield_qty'];
        $properties['yield_unit'] = trim((string) ($properties['yield_unit'] ?? '')) ?: 'tub (70g)';
        $properties['items'] = array_values(array_filter((array) ($properties['items'] ?? []), fn ($row) => is_array($row) && array_filter($row, fn ($v) => $v !== null && $v !== '') !== []));
        $properties['overhead_pct'] = ($properties['overhead_pct'] ?? '') === '' ? 40 : (float) $properties['overhead_pct'];
        $properties['markup_pct'] = ($properties['markup_pct'] ?? '') === '' ? 66 : (float) $properties['markup_pct'];
        $properties['labor_pct'] = ($properties['labor_pct'] ?? '') === '' ? 60 : (float) $properties['labor_pct'];

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $id = RouteModel::key('productCosting');

        return [
            'name' => ['nullable', 'string', 'max:255'],
            'product_id' => ['bail', 'nullable', 'uuid', Rule::exists('products', 'id')],
            'yield_qty' => ['required', 'numeric', 'min:0.001'],
            'yield_unit' => ['required', 'string', 'max:50'],
            'items' => ['array', 'max:100'],
            'items.*.group' => ['required', Rule::in(['ingredient', 'packaging'])],
            'items.*.ingredient_id' => ['bail', 'nullable', 'uuid', Rule::exists('ingredients', 'id')],
            'items.*.packaging_material_id' => ['bail', 'nullable', 'uuid', Rule::exists('packaging_materials', 'id')],
            'items.*.name' => ['nullable', 'string', 'max:255'],
            'items.*.unit' => ['required', 'string', 'max:20', fn (string $attribute, mixed $value, \Closure $fail) => is_string($value) && UnitConverterService::isKnownUnit($value) ? null : $fail(__('Use a unit like g, kg, ml, l, tbsp or pcs.'))],
            'items.*.package_amount' => ['required', 'numeric', 'min:0'],
            'items.*.package_price' => ['required', 'numeric', 'min:0'],
            'items.*.qty_used' => ['required', 'numeric', 'min:0'],
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
            'name' => __('sheet name'),
            'product_id' => __('product'),
            'yield_qty' => __('yield'),
            'yield_unit' => __('yield unit'),
            'items' => __('cost lines'),
            'overhead_pct' => __('overhead %'),
            'markup_pct' => __('markup %'),
            'labor_pct' => __('labor %'),
        ];
    }
}
