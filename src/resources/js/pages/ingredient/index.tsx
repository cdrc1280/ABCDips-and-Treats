import { Head } from '@inertiajs/react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { PageActions } from '@/components/custom/page-actions';
import { StatCards } from '@/components/custom/stat-cards';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { ActionDropdown } from '@/renderers/cells/action-dropdown';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/ingredients';
import { createIngredientActionSchema, ingredientActionSchema } from '@/schemas/ingredient/actions';
import { ingredientFilter, type IngredientSearchField } from '@/schemas/ingredient/filter';
import { createIngredientGridSchema } from '@/schemas/ingredient/grid';
import { ingredientSummary } from '@/schemas/ingredient/summary';
import type { IngredientRecord, Filters, Paginated, StatData, TableState } from '@/types';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<IngredientRecord>;
    filters: Filters<IngredientSearchField>;
    table: TableState;
    summary?: StatData[];
}

const title = 'Ingredients';

export default function IngredientIndex({ records, filters, table, summary }: Props) {
    const page = useDataTablePage<IngredientRecord, IngredientSearchField>({ url: index().url, records, filters, table });
    const rowActions = createIngredientActionSchema();

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title} actions={<PageActions actions={ingredientActionSchema} />}>
                    Raw materials, stock levels and what each unit costs.
                </PageHeader>

                <StatCards stats={summary} config={ingredientSummary} count={3} />

                <FilterableDataTable<IngredientRecord, IngredientSearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={ingredientFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createIngredientGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => <ActionDropdown row={row} actions={rowActions} />}
                    emptyMessage="No ingredients match these filters."
                />
            </div>
        </>
    );
}

IngredientIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Inventory' }, { title }] };
