<?php

namespace App\Domains\Production\Actions;

use App\Domains\Production\Models\ProductionBatch;
use App\Services\ProductionService;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\DB;

/** Complete batch. */
final class CompleteProductionBatchAction
{
    public function execute(ProductionBatch $productionBatch): ProductionBatch
    {
        if ($productionBatch->status === 'completed') {
            throw new DomainRuleViolation(__('This batch is already completed.'));
        }

        DB::transaction(function () use ($productionBatch) {
            app(ProductionService::class)->completeBatch($productionBatch, $productionBatch->actual_yield_qty, auth()->user());
        });

        return $productionBatch->refresh();
    }
}
