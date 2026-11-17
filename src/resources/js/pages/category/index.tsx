import { Head } from '@inertiajs/react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { PageActions } from '@/components/custom/page-actions';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { ActionDropdown } from '@/renderers/cells/action-dropdown';
import { index } from '@/routes/admin/categories';
import { categoryActionSchema, createCategoryActionSchema } from '@/schemas/category/actions';
import { categoryFilter, type CategorySearchField } from '@/schemas/category/filter';
import { createCategoryGridSchema } from '@/schemas/category/grid';
import type { CategoryRecord, Filters, Paginated, TableState } from '@/types';
import { dashboard } from '@/routes/admin';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<CategoryRecord>;
    filters: Filters<CategorySearchField>;
    table: TableState;
}

const title = 'Categories';

export default function CategoryIndex({ records, filters, table }: Props) {
    const page = useDataTablePage<CategoryRecord, CategorySearchField>({ url: index().url, records, filters, table });
    const rowActions = createCategoryActionSchema();

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title} actions={<PageActions actions={categoryActionSchema} />}>
                    Group products into the sections customers browse on the storefront.
                </PageHeader>

                <FilterableDataTable<CategoryRecord, CategorySearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={categoryFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createCategoryGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => <ActionDropdown row={row} actions={rowActions} />}
                    emptyMessage="No categories match these filters."
                />
            </div>
        </>
    );
}

CategoryIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Catalog' }, { title }] };
