import { Head } from '@inertiajs/react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { PageActions } from '@/components/custom/page-actions';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { ActionDropdown } from '@/renderers/cells/action-dropdown';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/customers';
import { createCustomerActionSchema, customerActionSchema } from '@/schemas/customer/actions';
import { customerFilter, type CustomerSearchField } from '@/schemas/customer/filter';
import { createCustomerGridSchema } from '@/schemas/customer/grid';
import type { CustomerRecord, Filters, Paginated, TableState } from '@/types';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<CustomerRecord>;
    filters: Filters<CustomerSearchField>;
    table: TableState;
}

const title = 'Customers';

export default function CustomerIndex({ records, filters, table }: Props) {
    const page = useDataTablePage<CustomerRecord, CustomerSearchField>({ url: index().url, records, filters, table });
    const rowActions = createCustomerActionSchema();

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title} actions={<PageActions actions={customerActionSchema} />}>
                    Storefront accounts — contact details and purchase history.
                </PageHeader>

                <FilterableDataTable<CustomerRecord, CustomerSearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={customerFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createCustomerGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => <ActionDropdown row={row} actions={rowActions} />}
                    emptyMessage="No customers match these filters."
                />
            </div>
        </>
    );
}

CustomerIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Orders & Sales' }, { title }] };
