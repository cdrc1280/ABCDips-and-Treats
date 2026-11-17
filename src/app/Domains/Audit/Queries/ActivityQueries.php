<?php

namespace App\Domains\Audit\Queries;

use App\Domains\Audit\Data\ActivityFilterData;
use App\Domains\User\Models\User;
use App\Shared\Data\FilterData;
use App\Shared\Queries\Concerns\SortsQueries;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Activitylog\Models\Activity;

final class ActivityQueries
{
    use SortsQueries;

    /**
     * @return LengthAwarePaginator<int, Activity>
     */
    public function paginate(FilterData $filters): LengthAwarePaginator
    {
        $search = ActivityFilterData::from($filters->search);

        $query = Activity::query()
            ->with('causer')
            ->when($search->keyword, fn (Builder $q, string $keyword) => $q->where(fn (Builder $q) => $q
                ->whereLike('description', "%{$keyword}%")
                ->orWhereLike('properties', "%{$keyword}%")))
            ->when($search->logName, fn (Builder $q, string $name) => $q->where('log_name', $name))
            ->when($search->event, fn (Builder $q, string $event) => $q->where('event', $event))
            ->when($search->causerId, fn (Builder $q, string $id) => $q->where('causer_id', $id))
            ->when($search->from, fn (Builder $q, string $from) => $q->whereDate('created_at', '>=', $from))
            ->when($search->to, fn (Builder $q, string $to) => $q->whereDate('created_at', '<=', $to));

        $this->applySort($query, $filters->sortField ?? 'id', $filters->sortDir ?? 'desc');

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function logNameOptions(): array
    {
        return Activity::query()->distinct()->orderBy('log_name')->pluck('log_name')->filter()
            ->map(fn (string $name) => ['label' => ucwords(str_replace('_', ' ', $name)), 'value' => $name])
            ->values()->all();
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function causerOptions(): array
    {
        return User::query()
            ->whereIn('id', Activity::query()->whereNotNull('causer_id')->distinct()->select('causer_id'))
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($user) => ['label' => $user->name, 'value' => $user->id])
            ->values()->all();
    }
}
