<?php

namespace App\Domains\Production\Actions;

use App\Domains\Production\Models\ProductionBatch;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\DB;

final class DeleteProductionBatchAction
{
    public function execute(ProductionBatch $productionBatch): void
    {
        if ($productionBatch->status === 'completed') {
            throw new DomainRuleViolation(__('Completed batches already moved stock and cannot be deleted.'));
        }

        DB::transaction(fn () => $productionBatch->delete());
    }
}
