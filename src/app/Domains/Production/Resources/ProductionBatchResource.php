<?php

namespace App\Domains\Production\Resources;

use App\Domains\Production\Models\ProductionBatch;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class ProductionBatchResource extends Resource
{
    public function __construct(
        public string $id,
        public ?string $batchNumber,
        public string $recipeId,
        public ?string $recipeName,
        public string $productId,
        public ?string $productName,
        public int $plannedQty,
        public ?int $actualYieldQty,
        public string $status,
        public ?string $statusLabel,
        public ?string $bakerUserId,
        public ?string $bakerName,
        public ?string $notes,
        public ?string $completedAt,
        public bool $isCompleted,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel(ProductionBatch $productionBatch, array $context = []): self
    {
        return new self(
            id: $productionBatch->id,
            batchNumber: $productionBatch->batch_number,
            recipeId: $productionBatch->recipe_id,
            recipeName: $productionBatch->recipe?->name,
            productId: $productionBatch->product_id,
            productName: $productionBatch->product?->name,
            plannedQty: (int) $productionBatch->planned_qty,
            actualYieldQty: $productionBatch->actual_yield_qty === null ? null : (int) $productionBatch->actual_yield_qty,
            status: (string) $productionBatch->status,
            statusLabel: self::option(['planned' => 'Planned', 'in_prep' => 'In prep', 'baking' => 'Baking', 'completed' => 'Completed', 'cancelled' => 'Cancelled'], $productionBatch->status),
            bakerUserId: $productionBatch->baker_user_id === null ? null : $productionBatch->baker_user_id,
            bakerName: $productionBatch->baker?->name,
            notes: $productionBatch->notes,
            completedAt: $productionBatch->completed_at?->toIso8601String(),
            isCompleted: $productionBatch->status === 'completed',
            createdAt: $productionBatch->created_at?->toIso8601String(),
            updatedAt: $productionBatch->updated_at?->toIso8601String(),
        );
    }

    /**
     * @param  array<string, string>  $options
     */
    private static function option(array $options, mixed $value): ?string
    {
        return $value === null ? null : ($options[(string) $value] ?? (string) $value);
    }
}
