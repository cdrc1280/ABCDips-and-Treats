import { router } from '@inertiajs/react';
import { useState } from 'react';

import type { Filters, Paginated, TableState } from '@/types';

interface Options<TRow, TSearch extends object> {
    /** Index URL (Wayfinder: things.index().url). */
    url: string;
    records: Paginated<TRow>;
    filters: Filters<TSearch>;
    table: TableState;
    /** Props to reload; defaults to records + filters (summary is deferred separately). */
    only?: string[];
}

/**
 * State + actions for an index page: filter values, sorting, pagination, loading flag.
 * Every change is a partial Inertia visit; the server saves it as the user's table state.
 */
export function useDataTablePage<TRow, TSearch extends object = Record<string, unknown>>({
    url,
    records,
    filters,
    table,
    only = ['records', 'filters', 'table'],
}: Options<TRow, TSearch>) {
    const [loading, setLoading] = useState(false);
    const [filterValues, setFilterValues] = useState<Partial<TSearch>>(filters.search ?? {});

    // Re-sync the filter form when the server returns a different search (e.g. after Reset).
    const [syncedSearch, setSyncedSearch] = useState(filters.search);
    if (syncedSearch !== filters.search) {
        setSyncedSearch(filters.search);
        setFilterValues(filters.search ?? {});
    }

    const visit = (query: Partial<Filters<TSearch>> & { reset?: boolean }) => {
        router.get(
            url,
            {
                search: filters.search,
                sort_field: filters.sort_field,
                sort_dir: filters.sort_dir,
                per_page: filters.per_page,
                page: filters.page,
                ...query,
            } as Record<string, unknown> as never,
            {
                only,
                preserveState: true,
                preserveScroll: true,
                replace: true,
                onStart: () => setLoading(true),
                onFinish: () => setLoading(false),
            },
        );
    };

    const applyFilters = (values: Partial<TSearch> = filterValues) => visit({ search: values, page: 1 });
    const resetFilters = () => {
        setFilterValues({});
        router.get(
            url,
            { reset: 1 },
            { only, preserveState: true, replace: true, onStart: () => setLoading(true), onFinish: () => setLoading(false) },
        );
    };

    const handleSort = (field: string) => {
        const dir = filters.sort_field === field && filters.sort_dir === 'asc' ? 'desc' : 'asc';
        visit({ sort_field: field, sort_dir: dir, page: 1 });
    };

    const goToPage = (page: number) => visit({ page });
    const setPerPage = (perPage: number) => visit({ per_page: perPage, page: 1 });

    return {
        rowData: records.data,
        meta: records.meta,
        loading,
        setLoading,
        filterValues,
        setFilterValues,
        currentSort: { field: filters.sort_field, dir: filters.sort_dir },
        applyFilters,
        resetFilters,
        handleSort,
        goToPage,
        setPerPage,
        savedState: { tableName: table.name, preferences: table.columns, perPageOptions: table.per_page_options },
    };
}
