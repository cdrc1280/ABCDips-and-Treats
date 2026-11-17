import { Head } from '@inertiajs/react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { PageActions } from '@/components/custom/page-actions';
import { StatCards } from '@/components/custom/stat-cards';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { ActionDropdown } from '@/renderers/cells/action-dropdown';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/reviews';
import { createReviewActionSchema, reviewActionSchema } from '@/schemas/review/actions';
import { reviewFilter, type ReviewSearchField } from '@/schemas/review/filter';
import { createReviewGridSchema } from '@/schemas/review/grid';
import { reviewSummary } from '@/schemas/review/summary';
import type { ReviewRecord, Filters, Paginated, StatData, TableState } from '@/types';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<ReviewRecord>;
    filters: Filters<ReviewSearchField>;
    table: TableState;
    summary?: StatData[];
}

const title = 'Reviews';

export default function ReviewIndex({ records, filters, table, summary }: Props) {
    const page = useDataTablePage<ReviewRecord, ReviewSearchField>({ url: index().url, records, filters, table });
    const rowActions = createReviewActionSchema();

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title} actions={<PageActions actions={reviewActionSchema} />}>
                    Product and store reviews — approve, feature or hide them.
                </PageHeader>

                <StatCards stats={summary} config={reviewSummary} count={4} />

                <FilterableDataTable<ReviewRecord, ReviewSearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={reviewFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createReviewGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => <ActionDropdown row={row} actions={rowActions} />}
                    emptyMessage="No reviews match these filters."
                />
            </div>
        </>
    );
}

ReviewIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Orders & Sales' }, { title }] };
