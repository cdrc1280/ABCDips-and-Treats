import { Head } from '@inertiajs/react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { PageActions } from '@/components/custom/page-actions';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { ActionDropdown } from '@/renderers/cells/action-dropdown';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/coupons';
import { createCouponActionSchema, couponActionSchema } from '@/schemas/coupon/actions';
import { couponFilter, type CouponSearchField } from '@/schemas/coupon/filter';
import { createCouponGridSchema } from '@/schemas/coupon/grid';
import type { CouponRecord, Filters, Paginated, TableState } from '@/types';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<CouponRecord>;
    filters: Filters<CouponSearchField>;
    table: TableState;
}

const title = 'Coupons';

export default function CouponIndex({ records, filters, table }: Props) {
    const page = useDataTablePage<CouponRecord, CouponSearchField>({ url: index().url, records, filters, table });
    const rowActions = createCouponActionSchema();

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title} actions={<PageActions actions={couponActionSchema} />}>
                    Discount codes customers can apply at checkout.
                </PageHeader>

                <FilterableDataTable<CouponRecord, CouponSearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={couponFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createCouponGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => <ActionDropdown row={row} actions={rowActions} />}
                    emptyMessage="No coupons match these filters."
                />
            </div>
        </>
    );
}

CouponIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Orders & Sales' }, { title }] };
