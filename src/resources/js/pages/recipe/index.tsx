import { Head } from '@inertiajs/react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { PageActions } from '@/components/custom/page-actions';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { ActionDropdown } from '@/renderers/cells/action-dropdown';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/recipes';
import { createRecipeActionSchema, recipeActionSchema } from '@/schemas/recipe/actions';
import { recipeFilter, type RecipeSearchField } from '@/schemas/recipe/filter';
import { createRecipeGridSchema } from '@/schemas/recipe/grid';
import type { RecipeRecord, Filters, Paginated, TableState } from '@/types';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<RecipeRecord>;
    filters: Filters<RecipeSearchField>;
    table: TableState;
}

const title = 'Recipes';

export default function RecipeIndex({ records, filters, table }: Props) {
    const page = useDataTablePage<RecipeRecord, RecipeSearchField>({ url: index().url, records, filters, table });
    const rowActions = createRecipeActionSchema();

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title} actions={<PageActions actions={recipeActionSchema} />}>
                    Bills of materials per product — drives costing, production and stock deduction.
                </PageHeader>

                <FilterableDataTable<RecipeRecord, RecipeSearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={recipeFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createRecipeGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => <ActionDropdown row={row} actions={rowActions} />}
                    emptyMessage="No recipes match these filters."
                />
            </div>
        </>
    );
}

RecipeIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Catalog' }, { title }] };
