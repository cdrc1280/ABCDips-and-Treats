import { Head } from '@inertiajs/react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { PageActions } from '@/components/custom/page-actions';
import { StatCards } from '@/components/custom/stat-cards';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { ActionDropdown } from '@/renderers/cells/action-dropdown';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/contact-messages';
import { createContactMessageActionSchema, contactMessageActionSchema } from '@/schemas/contact-message/actions';
import { contactMessageFilter, type ContactMessageSearchField } from '@/schemas/contact-message/filter';
import { createContactMessageGridSchema } from '@/schemas/contact-message/grid';
import { contactMessageSummary } from '@/schemas/contact-message/summary';
import type { ContactMessageRecord, Filters, Paginated, StatData, TableState } from '@/types';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<ContactMessageRecord>;
    filters: Filters<ContactMessageSearchField>;
    table: TableState;
    summary?: StatData[];
}

const title = 'Contact messages';

export default function ContactMessageIndex({ records, filters, table, summary }: Props) {
    const page = useDataTablePage<ContactMessageRecord, ContactMessageSearchField>({ url: index().url, records, filters, table });
    const rowActions = createContactMessageActionSchema();

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title} actions={<PageActions actions={contactMessageActionSchema} />}>
                    Messages sent from the storefront contact form.
                </PageHeader>

                <StatCards stats={summary} config={contactMessageSummary} count={3} />

                <FilterableDataTable<ContactMessageRecord, ContactMessageSearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={contactMessageFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createContactMessageGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => <ActionDropdown row={row} actions={rowActions} />}
                    emptyMessage="No contact messages match these filters."
                />
            </div>
        </>
    );
}

ContactMessageIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Engagement' }, { title }] };
