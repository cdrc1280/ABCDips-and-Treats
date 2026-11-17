<?php

namespace App\Domains\Preference\Actions;

use App\Domains\Preference\Models\Preference;
use App\Domains\Preference\Support\TableState;
use App\Domains\User\Models\User;
use App\Shared\Data\FilterData;
use Illuminate\Http\Request;

/**
 * Per-user saved table state, keyed tables.{name}.
 * A request carrying table params saves them; a bare request restores the last state.
 */
final class ResolveTableStateAction
{
    private const PARAMS = ['search', 'sort_field', 'sort_dir', 'per_page', 'page'];

    /**
     * @param  array{sort_field?: string, sort_dir?: string}  $defaults
     */
    public function execute(Request $request, User $user, string $table, array $defaults = []): TableState
    {
        $key = "tables.{$table}";
        $saved = (array) Preference::getFor($user, $key, []);

        if ($request->hasAny(self::PARAMS) || $request->boolean('reset')) {
            $query = $request->boolean('reset')
                ? $defaults
                : array_merge($defaults, $request->only(self::PARAMS));

            $saved['query'] = FilterData::from($query)->toArray();
            Preference::putFor($user, $key, $saved);
        }

        return new TableState(
            name: $table,
            query: FilterData::from(array_merge($defaults, $saved['query'] ?? [])),
            columns: (array) ($saved['columns'] ?? []),
        );
    }
}
