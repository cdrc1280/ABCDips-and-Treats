import { Head } from '@inertiajs/react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { PageActions } from '@/components/custom/page-actions';
import { StatCards } from '@/components/custom/stat-cards';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { ActionDropdown } from '@/renderers/cells/action-dropdown';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/purchase-orders';
import { createPurchaseOrderActionSchema, purchaseOrderActionSchema } from '@/schemas/purchase-order/actions';
import { purchaseOrderFilter, type PurchaseOrderSearchField } from '@/schemas/purchase-order/filter';
import { createPurchaseOrderGridSchema } from '@/schemas/purchase-order/grid';
import { purchaseOrderSummary } from '@/schemas/purchase-order/summary';
import type { PurchaseOrderRecord, Filters, Paginated, StatData, TableState } from '@/types';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<PurchaseOrderRecord>;
    filters: Filters<PurchaseOrderSearchField>;
    table: TableState;
    summary?: StatData[];
}

const title = 'Purchase orders';

export default function PurchaseOrderIndex({ records, filters, table, summary }: Props) {
    const page = useDataTablePage<PurchaseOrderRecord, PurchaseOrderSearchField>({ url: index().url, records, filters, table });
    const rowActions = createPurchaseOrderActionSchema();

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title} actions={<PageActions actions={purchaseOrderActionSchema} />}>
                    Orders to suppliers. Receiving a PO adds its items to ingredient stock.
                </PageHeader>

                <StatCards stats={summary} config={purchaseOrderSummary} count={3} />

                <FilterableDataTable<PurchaseOrderRecord, PurchaseOrderSearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={purchaseOrderFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createPurchaseOrderGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => <ActionDropdown row={row} actions={rowActions} />}
                    emptyMessage="No purchase orders match these filters."
                />
            </div>
        </>
    );
}

PurchaseOrderIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Production & Purchasing' }, { title }] };
