import type { ReactNode } from 'react';

import { DataTable, type DataTableProps } from '@/components/custom/data-table';
import { Pagination } from '@/components/custom/pagination';
import { TableFilter } from '@/components/custom/table-filter';
import type { FilterField, Paginated, TablePreferences } from '@/types';

interface Props<T, S extends object> extends Omit<DataTableProps<T>, 'rows' | 'offset' | 'tableName' | 'preferences'> {
    rowData: T[];
    meta: Paginated<T>['meta'];
    filterFields: readonly FilterField[];
    filterValues: Partial<S>;
    setFilterValues: (values: Partial<S>) => void;
    applyFilters: () => void;
    resetFilters: () => void;
    goToPage: (page: number) => void;
    setPerPage: (perPage: number) => void;
    tableName: string;
    preferences: TablePreferences;
    perPageOptions: number[];
    toolbar?: ReactNode;
}

/** Filter bar + data table + pagination: the standard index-page body. */
export function FilterableDataTable<T, S extends object>({
    rowData,
    meta,
    filterFields,
    filterValues,
    setFilterValues,
    applyFilters,
    resetFilters,
    goToPage,
    setPerPage,
    perPageOptions,
    loading,
    ...table
}: Props<T, S>) {
    return (
        <div className="grid gap-4">
            {filterFields.length > 0 && (
                <TableFilter
                    fields={filterFields}
                    values={filterValues}
                    onChange={setFilterValues}
                    onApply={applyFilters}
                    onReset={resetFilters}
                    loading={loading}
                />
            )}
            <DataTable {...table} rows={rowData} loading={loading} offset={(meta.from ?? 1) - 1} />
            <Pagination meta={meta} perPageOptions={perPageOptions} onPage={goToPage} onPerPage={setPerPage} disabled={loading} />
        </div>
    );
}
