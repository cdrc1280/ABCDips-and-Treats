<?php

namespace App\Domains\Sales\Queries;

use App\Domains\Sales\Models\Coupon;
use App\Shared\Data\FilterData;
use App\Shared\Queries\Concerns\SortsQueries;
use App\Shared\Queries\OptionsQuery;
use Illuminate\Pagination\LengthAwarePaginator;

final class CouponQueries
{
    use SortsQueries;

    /**
     * @return LengthAwarePaginator<int, Coupon>
     */
    public function paginate(FilterData $filters): LengthAwarePaginator
    {
        $query = Coupon::query()
            ->search($filters->search);

        $this->applySort($query, $filters->sortField ?? 'created_at', $filters->sortDir ?? 'desc');

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function getAllAsOptions(): array
    {
        return OptionsQuery::for(Coupon::class, 'code');
    }
}
