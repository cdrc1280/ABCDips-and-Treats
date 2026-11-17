import { Head } from '@inertiajs/react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { PageActions } from '@/components/custom/page-actions';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { ActionDropdown } from '@/renderers/cells/action-dropdown';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/suppliers';
import { createSupplierActionSchema, supplierActionSchema } from '@/schemas/supplier/actions';
import { supplierFilter, type SupplierSearchField } from '@/schemas/supplier/filter';
import { createSupplierGridSchema } from '@/schemas/supplier/grid';
import type { SupplierRecord, Filters, Paginated, TableState } from '@/types';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<SupplierRecord>;
    filters: Filters<SupplierSearchField>;
    table: TableState;
}

const title = 'Suppliers';

export default function SupplierIndex({ records, filters, table }: Props) {
    const page = useDataTablePage<SupplierRecord, SupplierSearchField>({ url: index().url, records, filters, table });
    const rowActions = createSupplierActionSchema();

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title} actions={<PageActions actions={supplierActionSchema} />}>
                    Companies you buy ingredients and packaging from.
                </PageHeader>

                <FilterableDataTable<SupplierRecord, SupplierSearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={supplierFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createSupplierGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => <ActionDropdown row={row} actions={rowActions} />}
                    emptyMessage="No suppliers match these filters."
                />
            </div>
        </>
    );
}

SupplierIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Inventory' }, { title }] };
