<?php

namespace App\Domains\Employee\Queries;

use App\Domains\Employee\Models\Payroll;
use App\Shared\Resources\StatData;

final class PayrollSummaryQuery
{
    /**
     * @return list<StatData>
     */
    public function execute(): array
    {
        $paid = (float) Payroll::query()->where('status', 'paid')->whereYear('paid_at', now()->year)->sum('total_net');

        return [
            new StatData('draft', __('Drafts'), Payroll::query()->where('status', 'draft')->count(), __('Not approved yet')),
            new StatData('approved', __('Approved, unpaid'), Payroll::query()->where('status', 'approved')->count(), __('Waiting for payout')),
            new StatData('paid_ytd', __('Paid this year'), $paid, __('Net pay released since January'), '₱'.number_format($paid, 2)),
        ];
    }
}
