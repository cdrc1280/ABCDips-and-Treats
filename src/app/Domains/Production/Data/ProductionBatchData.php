<?php

namespace App\Domains\Production\Data;

use App\Domains\Catalog\Models\Product;
use App\Shared\Support\RouteModel;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class ProductionBatchData extends Data
{
    public function __construct(
        public string $recipeId,
        public string $productId,
        public ?string $batchNumber = null,
        public ?int $plannedQty = 1,
        public ?int $actualYieldQty = null,
        public ?string $status = 'planned',
        public ?string $bakerUserId = null,
        public ?string $notes = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['recipe_id'] = ($properties['recipe_id'] ?? '') === '' ? null : (string) $properties['recipe_id'];
        $properties['product_id'] = ($properties['product_id'] ?? '') === '' ? null : (string) $properties['product_id'];
        $properties['batch_number'] = strtoupper(trim((string) ($properties['batch_number'] ?? ''))) ?: null;
        $properties['planned_qty'] = ($properties['planned_qty'] ?? '') === '' ? 1 : (int) $properties['planned_qty'];
        $properties['actual_yield_qty'] = ($properties['actual_yield_qty'] ?? '') === '' ? null : (int) $properties['actual_yield_qty'];
        $properties['status'] = trim((string) ($properties['status'] ?? '')) ?: 'planned';
        $properties['baker_user_id'] = ($properties['baker_user_id'] ?? '') === '' ? null : (string) $properties['baker_user_id'];
        $properties['notes'] = trim((string) ($properties['notes'] ?? '')) ?: null;

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $id = RouteModel::key('productionBatch');

        return [
            'recipe_id' => ['bail', 'required', 'uuid', Rule::exists('recipes', 'id')],
            'product_id' => ['bail', 'required', 'uuid', Rule::exists('products', 'id'), function (string $attribute, mixed $value, \Closure $fail) {
                // Made-to-order products consume ingredients when sold; producing them ahead would count twice.
                if (Product::query()->whereKey($value)->value('inventory_mode') !== Product::MODE_BAKED_AHEAD) {
                    $fail(__('Only products set to "Baked ahead" can be produced in batches. Change the product\'s stock mode first.'));
                }
            }],
            'batch_number' => ['nullable', 'string', 'max:40', Rule::unique('production_batches', 'batch_number')->ignore($id)->whereNull('deleted_at')],
            'planned_qty' => ['required', 'integer', 'min:1'],
            'actual_yield_qty' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['planned', 'in_prep', 'baking', 'completed', 'cancelled']), Rule::notIn(['completed'])],
            'baker_user_id' => ['bail', 'nullable', 'uuid', Rule::exists('users', 'id')],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public static function messages(mixed ...$args): array
    {
        return [
            'status.not_in' => __('Use “Complete batch” to finish a batch — that is what moves the stock.'),
        ];
    }

    public static function attributes(mixed ...$args): array
    {
        return [
            'batch_number' => __('batch number'),
            'recipe_id' => __('recipe'),
            'product_id' => __('product'),
            'planned_qty' => __('planned quantity'),
            'actual_yield_qty' => __('actual yield'),
            'status' => __('status'),
            'baker_user_id' => __('baker'),
            'notes' => __('notes'),
        ];
    }
}
