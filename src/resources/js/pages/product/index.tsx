import { Head } from '@inertiajs/react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { PageActions } from '@/components/custom/page-actions';
import { StatCards } from '@/components/custom/stat-cards';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { ActionDropdown } from '@/renderers/cells/action-dropdown';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/products';
import { createProductActionSchema, productActionSchema } from '@/schemas/product/actions';
import { productFilter, type ProductSearchField } from '@/schemas/product/filter';
import { createProductGridSchema } from '@/schemas/product/grid';
import { productSummary } from '@/schemas/product/summary';
import type { ProductRecord, Filters, Paginated, StatData, TableState } from '@/types';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<ProductRecord>;
    filters: Filters<ProductSearchField>;
    table: TableState;
    summary?: StatData[];
}

const title = 'Products';

export default function ProductIndex({ records, filters, table, summary }: Props) {
    const page = useDataTablePage<ProductRecord, ProductSearchField>({ url: index().url, records, filters, table });
    const rowActions = createProductActionSchema();

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title} actions={<PageActions actions={productActionSchema} />}>
                    Everything on the menu: prices, stock, flavors, variations and photos.
                </PageHeader>

                <StatCards stats={summary} config={productSummary} count={4} />

                <FilterableDataTable<ProductRecord, ProductSearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={productFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createProductGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => <ActionDropdown row={row} actions={rowActions} />}
                    emptyMessage="No products match these filters."
                />
            </div>
        </>
    );
}

ProductIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Catalog' }, { title }] };
