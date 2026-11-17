import { Deferred, Head, router, useForm } from '@inertiajs/react';
import { IdCard, Loader2, MoreVertical, Trash2 } from 'lucide-react';
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
import { EmployeePermission } from '@/lib/permissions';
import { dashboard } from '@/routes/admin';
import { destroy, edit, index, update } from '@/routes/admin/employees';
import type { EmployeeRecord, Option } from '@/types';

import { EmployeeForm, type EmployeeFormData, type EmployeeFormOptions } from './employee-form';
import { EmployeePreview } from './employee-preview';

type Props = { record: EmployeeRecord; switcher?: Option<string>[] } & EmployeeFormOptions;

export default function EmployeeEdit({ record, switcher, user_options }: Props) {
    const { can } = usePermissions();
    const [confirmDelete, setConfirmDelete] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const form = useForm<EmployeeFormData>({
        first_name: record.first_name === null || record.first_name === undefined ? '' : String(record.first_name),
        last_name: record.last_name === null || record.last_name === undefined ? '' : String(record.last_name),
        employee_number: record.employee_number === null || record.employee_number === undefined ? '' : String(record.employee_number),
        email: record.email === null || record.email === undefined ? '' : String(record.email),
        phone: record.phone === null || record.phone === undefined ? '' : String(record.phone),
        user_id: record.user_id === null || record.user_id === undefined ? '' : String(record.user_id),
        role_title: record.role_title === null || record.role_title === undefined ? '' : String(record.role_title),
        employment_type: record.employment_type === null || record.employment_type === undefined ? '' : String(record.employment_type),
        basic_monthly_salary:
            record.basic_monthly_salary === null || record.basic_monthly_salary === undefined ? '' : String(record.basic_monthly_salary),
        hired_at: record.hired_at === null || record.hired_at === undefined ? '' : String(record.hired_at),
        is_active: record.is_active,
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
                    searchPlaceholder="Jump to employee…"
                    onChange={(id) => id && id !== record.id && router.visit(edit(id).url)}
                />
            </Deferred>
            {can(EmployeePermission.EmployeesDelete) && (
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="outline" size="icon" aria-label="More actions">
                            <MoreVertical />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem variant="destructive" onSelect={() => setConfirmDelete(true)}>
                            <Trash2 /> Delete employee
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            )}
        </div>
    );

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title={`Edit ${record.first_name}`} />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Employee list"
                    icon={IdCard}
                    title={String(record.first_name)}
                    description={`${record.employee_number} · ${record.role_title}`}
                    actions={headerActions}
                />
                <div className={tabGridClass}>
                    <EmployeePreview data={form.data} title={String(record.first_name)} />
                    <div className="grid min-w-0 grid-cols-[minmax(0,1fr)] content-start gap-4">
                        <EmployeeForm data={form.data} setData={form.setData} errors={form.errors} options={{ user_options }} />
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
                title={`Delete ${record.first_name}?`}
                description="This employee will be removed."
                confirmLabel="Delete employee"
                processing={deleting}
                onConfirm={remove}
            />
        </UnsavedChangesGuard>
    );
}

EmployeeEdit.layout = {
    breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'HR & Payroll' }, { title: 'Employees', href: index().url }, { title: 'Edit' }],
};
