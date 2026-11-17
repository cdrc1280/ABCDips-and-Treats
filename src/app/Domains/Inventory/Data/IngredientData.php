<?php

namespace App\Domains\Inventory\Data;

use App\Shared\Support\RouteModel;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class IngredientData extends Data
{
    public function __construct(
        public string $name,
        public string $sku,
        public ?string $supplierId = null,
        public ?string $unit = 'grams',
        public ?float $itemUnit = 1000,
        public ?float $itemPrice = 0,
        public ?float $stockQty = 0,
        public ?float $minStockQty = 100,
        public ?float $reorderQty = 1000,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['name'] = trim((string) ($properties['name'] ?? '')) ?: null;
        $properties['sku'] = strtoupper(trim((string) ($properties['sku'] ?? ''))) ?: null;
        $properties['supplier_id'] = ($properties['supplier_id'] ?? '') === '' ? null : (string) $properties['supplier_id'];
        $properties['unit'] = trim((string) ($properties['unit'] ?? '')) ?: 'grams';
        $properties['item_unit'] = ($properties['item_unit'] ?? '') === '' ? 1000 : (float) $properties['item_unit'];
        $properties['item_price'] = ($properties['item_price'] ?? '') === '' ? 0 : (float) $properties['item_price'];
        $properties['stock_qty'] = ($properties['stock_qty'] ?? '') === '' ? 0 : (float) $properties['stock_qty'];
        $properties['min_stock_qty'] = ($properties['min_stock_qty'] ?? '') === '' ? 100 : (float) $properties['min_stock_qty'];
        $properties['reorder_qty'] = ($properties['reorder_qty'] ?? '') === '' ? 1000 : (float) $properties['reorder_qty'];

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $id = RouteModel::key('ingredient');

        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:50', Rule::unique('ingredients', 'sku')->ignore($id)->whereNull('deleted_at')],
            'supplier_id' => ['bail', 'nullable', 'uuid', Rule::exists('suppliers', 'id')->whereNull('deleted_at')],
            'unit' => ['required', Rule::in(['grams', 'kg', 'ml', 'L', 'piece', 'box'])],
            'item_unit' => ['required', 'numeric', 'min:0.001'],
            'item_price' => ['required', 'numeric', 'min:0'],
            'stock_qty' => ['required', 'numeric', 'min:0'],
            'min_stock_qty' => ['required', 'numeric', 'min:0'],
            'reorder_qty' => ['required', 'numeric', 'min:0'],
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
            'supplier_id' => __('supplier'),
            'unit' => __('stock unit'),
            'item_unit' => __('pack size'),
            'item_price' => __('pack price'),
            'stock_qty' => __('in stock'),
            'min_stock_qty' => __('minimum level'),
            'reorder_qty' => __('reorder quantity'),
        ];
    }
}
