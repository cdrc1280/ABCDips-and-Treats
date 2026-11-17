import { Deferred, Head, router, useForm } from '@inertiajs/react';
import { CalendarCheck, Loader2, MoreVertical, Trash2 } from 'lucide-react';
import { useState, type FormEvent } from 'react';

import { AppFooter } from '@/components/custom/app-footer';
import { ConfirmDialog } from '@/components/custom/confirm-dialog';
import { CreateHeader } from '@/components/custom/create-header';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { UnsavedChangesGuard } from '@/components/custom/unsaved-changes-guard';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Skeleton } from '@/components/ui/skeleton';
import { usePermissions } from '@/hooks/use-permissions';
import { tabGridClass } from '@/lib/layout';
import { AttendancePermission } from '@/lib/permissions';
import { dashboard } from '@/routes/admin';
import { destroy, edit, index, update } from '@/routes/admin/attendances';
import type { AttendanceRecord, Option } from '@/types';

import { AttendanceForm, type AttendanceFormData, type AttendanceFormOptions } from './attendance-form';
import { AttendancePreview } from './attendance-preview';

type Props = { record: AttendanceRecord; switcher?: Option<string>[] } & AttendanceFormOptions;

export default function AttendanceEdit({ record, switcher, employee_options }: Props) {
    const { can } = usePermissions();
    const [confirmDelete, setConfirmDelete] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const form = useForm<AttendanceFormData>({
        employee_id: record.employee_id === null || record.employee_id === undefined ? '' : String(record.employee_id),
        date: record.date === null || record.date === undefined ? '' : String(record.date),
        status: record.status === null || record.status === undefined ? '' : String(record.status),
        time_in: record.time_in === null || record.time_in === undefined ? '' : String(record.time_in),
        time_out: record.time_out === null || record.time_out === undefined ? '' : String(record.time_out),
        hours_worked: record.hours_worked === null || record.hours_worked === undefined ? '' : String(record.hours_worked),
        is_overtime: record.is_overtime,
        overtime_hours: record.overtime_hours === null || record.overtime_hours === undefined ? '' : String(record.overtime_hours),
        is_holiday: record.is_holiday,
        notes: record.notes === null || record.notes === undefined ? '' : String(record.notes),
    });

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.put(update(record.id).url, { onSuccess: () => form.setDefaults() });
    };

    const remove = () =>
        router.delete(destroy(record.id).url, {
            onStart: () => setDeleting(true),
            onFinish: () => setDeleting(false),
            onSuccess: () => router.visit(index().url),
        });

    const headerActions = (
        <div className="flex items-center gap-2">
            <Deferred data="switcher" fallback={<Skeleton className="h-9 w-56" />}>
                <SelectDropdown<string>
                    className="w-56"
                    value={record.id}
                    options={switcher ?? []}
                    searchPlaceholder="Jump to attendance…"
                    onChange={(id) => id && id !== record.id && router.visit(edit(id).url)}
                />
            </Deferred>
            {can(AttendancePermission.AttendancesDelete) && (
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="outline" size="icon" aria-label="More actions">
                            <MoreVertical />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem variant="destructive" onSelect={() => setConfirmDelete(true)}>
                            <Trash2 /> Delete attendance
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            )}
        </div>
    );

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title={`Edit ${record.date}`} />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Attendance list"
                    icon={CalendarCheck}
                    title={String(record.date)}
                    description={''}
                    actions={headerActions}
                />
                <div className={tabGridClass}>
                    <AttendancePreview data={form.data} title={String(record.date)} />
                    <div className="grid min-w-0 grid-cols-[minmax(0,1fr)] content-start gap-4">
                        <AttendanceForm data={form.data} setData={form.setData} errors={form.errors} options={{ employee_options }} />
                    </div>
                </div>
                <AppFooter>
                    <Button type="button" variant="outline" size="sm" onClick={() => router.visit(index().url)}>
                        Cancel
                    </Button>
                    <Button type="button" size="sm" disabled={form.processing || !form.isDirty} onClick={() => submit()}>
                        {form.processing && <Loader2 className="animate-spin" />} Save changes
                    </Button>
                </AppFooter>
            </form>
            <ConfirmDialog
                open={confirmDelete}
                onOpenChange={setConfirmDelete}
                title={`Delete ${record.date}?`}
                description="This attendance will be removed."
                confirmLabel="Delete attendance"
                processing={deleting}
                onConfirm={remove}
            />
        </UnsavedChangesGuard>
    );
}

AttendanceEdit.layout = {
    breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'HR & Payroll' }, { title: 'Attendance', href: index().url }, { title: 'Edit' }],
};
