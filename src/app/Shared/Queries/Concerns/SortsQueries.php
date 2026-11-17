<?php

namespace App\Shared\Queries\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

/**
 * applySort() with a whitelist-free but safe column check, and "relation.column"
 * support for BelongsTo relations (joined, so pagination stays one query).
 */
trait SortsQueries
{
    /**
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $query
     * @return Builder<TModel>
     */
    protected function applySort(Builder $query, string $field, string $direction = 'asc'): Builder
    {
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';
        $model = $query->getModel();
        $table = $model->getTable();

        if (str_contains($field, '.')) {
            [$relation, $column] = explode('.', $field, 2);

            if (! method_exists($model, $relation) || ! preg_match('/^\w+$/', $column)) {
                return $query;
            }

            $related = $model->{$relation}();
            if (! $related instanceof BelongsTo) {
                return $query;
            }

            $relatedTable = $related->getRelated()->getTable();
            $alias = 'sort_'.$relation;

            return $query
                ->select($table.'.*')
                ->leftJoin(
                    $relatedTable.' as '.$alias,
                    $alias.'.'.$related->getOwnerKeyName(),
                    '=',
                    $table.'.'.$related->getForeignKeyName(),
                )
                ->orderBy($alias.'.'.$column, $direction);
        }

        // Aggregates from withCount()/withSum() (e.g. items_count) are valid sort keys too.
        if (preg_match('/^\w+$/', $field) && ($this->isColumn($table, $field) || str_ends_with($field, '_count') || str_contains($field, '_sum_'))) {
            return $query->orderBy($this->isColumn($table, $field) ? $table.'.'.$field : $field, $direction);
        }

        return $query;
    }

    private function isColumn(string $table, string $column): bool
    {
        static $columns = [];

        $columns[$table] ??= Schema::getColumnListing($table);

        return in_array($column, $columns[$table], true);
    }
}
