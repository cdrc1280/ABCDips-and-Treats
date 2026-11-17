<?php

namespace App\Domains\Purchasing\Queries;

use App\Domains\Purchasing\Models\Quotation;
use App\Shared\Resources\StatData;

final class QuotationSummaryQuery
{
    /**
     * @return list<StatData>
     */
    public function execute(): array
    {
        return [
            new StatData('open', __('Open quotes'), Quotation::query()->whereIn('status', ['draft', 'sent'])->count(), __('Draft or sent')),
            new StatData('accepted', __('Accepted'), Quotation::query()->where('status', 'accepted')->count(), __('Ready to convert')),
            new StatData('converted', __('Converted'), Quotation::query()->where('status', 'converted_to_po')->count(), __('Turned into POs')),
        ];
    }
}
