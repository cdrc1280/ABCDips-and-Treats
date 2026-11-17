<?php

namespace App\Domains\Purchasing\Actions;

use App\Domains\Purchasing\Models\Quotation;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\DB;

final class DeleteQuotationAction
{
    public function execute(Quotation $quotation): void
    {
        if ($quotation->status === 'converted_to_po') {
            throw new DomainRuleViolation(__('Converted quotations are linked to a purchase order and cannot be deleted.'));
        }

        DB::transaction(fn () => $quotation->delete());
    }
}
