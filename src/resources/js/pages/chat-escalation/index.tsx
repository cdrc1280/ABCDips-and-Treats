import { Head } from '@inertiajs/react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { PageActions } from '@/components/custom/page-actions';
import { StatCards } from '@/components/custom/stat-cards';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { ActionDropdown } from '@/renderers/cells/action-dropdown';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/chat-escalations';
import { createChatEscalationActionSchema, chatEscalationActionSchema } from '@/schemas/chat-escalation/actions';
import { chatEscalationFilter, type ChatEscalationSearchField } from '@/schemas/chat-escalation/filter';
import { createChatEscalationGridSchema } from '@/schemas/chat-escalation/grid';
import { chatEscalationSummary } from '@/schemas/chat-escalation/summary';
import type { ChatEscalationRecord, Filters, Paginated, StatData, TableState } from '@/types';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<ChatEscalationRecord>;
    filters: Filters<ChatEscalationSearchField>;
    table: TableState;
    summary?: StatData[];
}

const title = 'Support escalations';

export default function ChatEscalationIndex({ records, filters, table, summary }: Props) {
    const page = useDataTablePage<ChatEscalationRecord, ChatEscalationSearchField>({ url: index().url, records, filters, table });
    const rowActions = createChatEscalationActionSchema();

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title} actions={<PageActions actions={chatEscalationActionSchema} />}>
                    AI-chat conversations customers asked to hand over to a person.
                </PageHeader>

                <StatCards stats={summary} config={chatEscalationSummary} count={3} />

                <FilterableDataTable<ChatEscalationRecord, ChatEscalationSearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={chatEscalationFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createChatEscalationGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => <ActionDropdown row={row} actions={rowActions} />}
                    emptyMessage="No support escalations match these filters."
                />
            </div>
        </>
    );
}

ChatEscalationIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Engagement' }, { title }] };
