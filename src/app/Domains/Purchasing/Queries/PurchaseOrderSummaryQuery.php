<?php

namespace App\Domains\Purchasing\Queries;

use App\Domains\Purchasing\Models\PurchaseOrder;
use App\Shared\Resources\StatData;

final class PurchaseOrderSummaryQuery
{
    /**
     * @return list<StatData>
     */
    public function execute(): array
    {
        $open = (float) PurchaseOrder::query()->whereIn('status', ['draft', 'sent'])->sum('total');

        return [
            new StatData('open', __('Open POs'), PurchaseOrder::query()->whereIn('status', ['draft', 'sent'])->count(), __('Not received yet')),
            new StatData('open_value', __('Open value'), $open, __('Total of open POs'), '₱'.number_format($open, 2)),
            new StatData('received_month', __('Received this month'), PurchaseOrder::query()->where('status', 'received')->where('received_at', '>=', now()->startOfMonth())->count(), __('Since the 1st')),
        ];
    }
}
