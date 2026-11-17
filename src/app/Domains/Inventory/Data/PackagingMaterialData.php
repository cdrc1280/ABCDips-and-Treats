<?php

namespace App\Domains\Inventory\Data;

use App\Shared\Support\RouteModel;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class PackagingMaterialData extends Data
{
    public function __construct(
        public string $name,
        public string $sku,
        public ?string $type = 'box',
        public ?string $unit = 'pcs',
        public ?float $costPerUnit = 0,
        public ?float $stockQty = 0,
        public ?float $minStockQty = 10,
        public bool $isActive = true,
        public ?string $notes = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['name'] = trim((string) ($properties['name'] ?? '')) ?: null;
        $properties['sku'] = strtoupper(trim((string) ($properties['sku'] ?? ''))) ?: null;
        $properties['type'] = trim((string) ($properties['type'] ?? '')) ?: 'box';
        $properties['unit'] = trim((string) ($properties['unit'] ?? '')) ?: 'pcs';
        $properties['cost_per_unit'] = ($properties['cost_per_unit'] ?? '') === '' ? 0 : (float) $properties['cost_per_unit'];
        $properties['stock_qty'] = ($properties['stock_qty'] ?? '') === '' ? 0 : (float) $properties['stock_qty'];
        $properties['min_stock_qty'] = ($properties['min_stock_qty'] ?? '') === '' ? 10 : (float) $properties['min_stock_qty'];
        $properties['is_active'] = filter_var($properties['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $properties['notes'] = trim((string) ($properties['notes'] ?? '')) ?: null;

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $id = RouteModel::key('packagingMaterial');

        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:50', Rule::unique('packaging_materials', 'sku')->ignore($id)->whereNull('deleted_at')],
            'type' => ['required', Rule::in(['box', 'bag', 'board', 'container', 'ribbon', 'label', 'sticker', 'tape', 'wrap', 'other'])],
            'unit' => ['required', 'string', 'max:20'],
            'cost_per_unit' => ['required', 'numeric', 'min:0'],
            'stock_qty' => ['required', 'numeric', 'min:0'],
            'min_stock_qty' => ['required', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:5000'],
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
            'name' => __('name'),
            'sku' => __('sku'),
            'type' => __('type'),
            'unit' => __('unit'),
            'cost_per_unit' => __('cost per unit'),
            'stock_qty' => __('in stock'),
            'min_stock_qty' => __('minimum level'),
            'is_active' => __('active'),
            'notes' => __('notes'),
        ];
    }
}
