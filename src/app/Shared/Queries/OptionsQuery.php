<?php

namespace App\Shared\Queries;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * [['label' => ..., 'value' => id], ...] for any model — the shape SelectDropdown expects.
 */
final class OptionsQuery
{
    /**
     * @param  class-string<Model>  $model
     * @param  (callable(Builder<Model>): mixed)|null  $scope
     * @return array<int, array{label: string, value: string}>
     */
    public static function for(string $model, string $label = 'name', ?callable $scope = null, ?string $description = null): array
    {
        $query = $model::query();
        if ($scope) {
            $scope($query);
        }

        return $query
            ->orderBy($label)
            ->get()
            ->map(fn (Model $row) => array_filter([
                'label' => (string) $row->getAttribute($label),
                'value' => (string) $row->getKey(),
                'description' => $description ? $row->getAttribute($description) : null,
            ], fn ($value) => $value !== null))
            ->values()
            ->all();
    }
}
