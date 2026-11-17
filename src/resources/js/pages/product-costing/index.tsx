import { Head } from '@inertiajs/react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { PageActions } from '@/components/custom/page-actions';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { ActionDropdown } from '@/renderers/cells/action-dropdown';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/product-costings';
import { createProductCostingActionSchema, productCostingActionSchema } from '@/schemas/product-costing/actions';
import { productCostingFilter, type ProductCostingSearchField } from '@/schemas/product-costing/filter';
import { createProductCostingGridSchema } from '@/schemas/product-costing/grid';
import type { ProductCostingRecord, Filters, Paginated, TableState } from '@/types';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<ProductCostingRecord>;
    filters: Filters<ProductCostingSearchField>;
    table: TableState;
}

const title = 'Product costing';

export default function ProductCostingIndex({ records, filters, table }: Props) {
    const page = useDataTablePage<ProductCostingRecord, ProductCostingSearchField>({ url: index().url, records, filters, table });
    const rowActions = createProductCostingActionSchema();

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title} actions={<PageActions actions={productCostingActionSchema} />}>
                    Costing sheets: ingredients and packaging per yield, with overhead, markup and labor.
                </PageHeader>

                <FilterableDataTable<ProductCostingRecord, ProductCostingSearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={productCostingFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createProductCostingGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => <ActionDropdown row={row} actions={rowActions} />}
                    emptyMessage="No product costing match these filters."
                />
            </div>
        </>
    );
}

ProductCostingIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Catalog' }, { title }] };
