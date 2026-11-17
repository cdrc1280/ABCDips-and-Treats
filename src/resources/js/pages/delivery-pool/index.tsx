import { Head } from '@inertiajs/react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { PageActions } from '@/components/custom/page-actions';
import { StatCards } from '@/components/custom/stat-cards';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { ActionDropdown } from '@/renderers/cells/action-dropdown';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/delivery-pools';
import { createDeliveryPoolActionSchema, deliveryPoolActionSchema } from '@/schemas/delivery-pool/actions';
import { deliveryPoolFilter, type DeliveryPoolSearchField } from '@/schemas/delivery-pool/filter';
import { createDeliveryPoolGridSchema } from '@/schemas/delivery-pool/grid';
import { deliveryPoolSummary } from '@/schemas/delivery-pool/summary';
import type { DeliveryPoolRecord, Filters, Paginated, StatData, TableState } from '@/types';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<DeliveryPoolRecord>;
    filters: Filters<DeliveryPoolSearchField>;
    table: TableState;
    summary?: StatData[];
}

const title = 'Delivery pools';

export default function DeliveryPoolIndex({ records, filters, table, summary }: Props) {
    const page = useDataTablePage<DeliveryPoolRecord, DeliveryPoolSearchField>({ url: index().url, records, filters, table });
    const rowActions = createDeliveryPoolActionSchema();

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title} actions={<PageActions actions={deliveryPoolActionSchema} />}>
                    Group nearby pooling orders, split one shipping fee, then settle so they can be prepared.
                </PageHeader>

                <StatCards stats={summary} config={deliveryPoolSummary} count={3} />

                <FilterableDataTable<DeliveryPoolRecord, DeliveryPoolSearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={deliveryPoolFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createDeliveryPoolGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => <ActionDropdown row={row} actions={rowActions} />}
                    emptyMessage="No delivery pools match these filters."
                />
            </div>
        </>
    );
}

DeliveryPoolIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Orders & Sales' }, { title }] };
