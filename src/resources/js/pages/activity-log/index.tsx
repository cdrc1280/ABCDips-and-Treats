import { Head } from '@inertiajs/react';
import { Eye } from 'lucide-react';
import { useState } from 'react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { formatDate, titleCase } from '@/lib/format';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/activity-log';
import { activityFilter, type ActivitySearchField } from '@/schemas/activity-log/filter';
import { createActivityGridSchema } from '@/schemas/activity-log/grid';
import type { ActivityRecord, Filters, Paginated, TableState } from '@/types';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<ActivityRecord>;
    filters: Filters<ActivitySearchField>;
    table: TableState;
}

const show = (value: unknown) => {
    if (value === null || value === undefined || value === '') return <span className="text-muted-foreground">—</span>;
    if (typeof value === 'object') return <code className="text-xs break-all">{JSON.stringify(value)}</code>;
    return <span className="break-words">{String(value)}</span>;
};

const title = 'Activity log';

/** Read-only: who changed what, and when. */
export default function ActivityLogIndex({ records, filters, table }: Props) {
    const page = useDataTablePage<ActivityRecord, ActivitySearchField>({ url: index().url, records, filters, table });
    const [selected, setSelected] = useState<ActivityRecord | null>(null);

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title}>Every create, update and delete in the back office, with the values before and after.</PageHeader>

                <FilterableDataTable<ActivityRecord, ActivitySearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={activityFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createActivityGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => (
                        <Button variant="ghost" size="icon" className="size-8" aria-label="View changes" onClick={() => setSelected(row)}>
                            <Eye />
                        </Button>
                    )}
                    emptyMessage="No activity matches these filters."
                />
            </div>

            <Dialog open={selected !== null} onOpenChange={(open) => !open && setSelected(null)}>
                <DialogContent className="sm:max-w-2xl">
                    {selected && (
                        <>
                            <DialogHeader>
                                <DialogTitle>
                                    {titleCase(selected.event ?? selected.description)} · {titleCase(selected.log_name)}
                                    {selected.subject_id ? ` #${selected.subject_id}` : ''}
                                </DialogTitle>
                                <DialogDescription>
                                    {formatDate(selected.created_at, true)} · {selected.causer_name ?? 'System'}
                                </DialogDescription>
                            </DialogHeader>
                            <div className="max-h-[60vh] overflow-auto">
                                {selected.changes.length === 0 ? (
                                    <p className="text-sm text-muted-foreground">No field values were recorded.</p>
                                ) : (
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>Field</TableHead>
                                                <TableHead>Before</TableHead>
                                                <TableHead>After</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {selected.changes.map((change) => (
                                                <TableRow key={change.field}>
                                                    <TableCell className="font-medium">{titleCase(change.field)}</TableCell>
                                                    <TableCell className="max-w-56 align-top">{show(change.old)}</TableCell>
                                                    <TableCell className="max-w-56 align-top">{show(change.new)}</TableCell>
                                                </TableRow>
                                            ))}
                                        </TableBody>
                                    </Table>
                                )}
                            </div>
                        </>
                    )}
                </DialogContent>
            </Dialog>
        </>
    );
}

ActivityLogIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'System' }, { title }] };
