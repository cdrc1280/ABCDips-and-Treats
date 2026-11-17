import { Deferred, Head, router, useForm } from '@inertiajs/react';
import { Banknote, Loader2, MoreVertical, Trash2 } from 'lucide-react';
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
import { PayrollPermission } from '@/lib/permissions';
import { dashboard } from '@/routes/admin';
import { destroy, edit, index, update } from '@/routes/admin/payrolls';
import type { PayrollRecord, Option } from '@/types';

import { PayrollForm, type PayrollFormData, type PayrollFormOptions } from './payroll-form';
import { PayrollPreview } from './payroll-preview';

type Props = { record: PayrollRecord; switcher?: Option<string>[] } & PayrollFormOptions;

export default function PayrollEdit({ record, switcher, employee_options }: Props) {
    const { can } = usePermissions();
    const [confirmDelete, setConfirmDelete] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const form = useForm<PayrollFormData>({
        payroll_number: record.payroll_number === null || record.payroll_number === undefined ? '' : String(record.payroll_number),
        period_start: record.period_start === null || record.period_start === undefined ? '' : String(record.period_start),
        period_end: record.period_end === null || record.period_end === undefined ? '' : String(record.period_end),
        status: record.status === null || record.status === undefined ? '' : String(record.status),
        notes: record.notes === null || record.notes === undefined ? '' : String(record.notes),
        items: record.items.map((row) => ({
            employee_id: row.employee_id === null || row.employee_id === undefined ? '' : String(row.employee_id),
            basic_pay: row.basic_pay === null || row.basic_pay === undefined ? '' : String(row.basic_pay),
            overtime_pay: row.overtime_pay === null || row.overtime_pay === undefined ? '' : String(row.overtime_pay),
            bonuses: row.bonuses === null || row.bonuses === undefined ? '' : String(row.bonuses),
            holiday_pay: row.holiday_pay === null || row.holiday_pay === undefined ? '' : String(row.holiday_pay),
            attendance_deduction: row.attendance_deduction === null || row.attendance_deduction === undefined ? '' : String(row.attendance_deduction),
            attendance_note: row.attendance_note ?? '',
            sss_deduction: row.sss_deduction === null || row.sss_deduction === undefined ? '' : String(row.sss_deduction),
            philhealth_deduction: row.philhealth_deduction === null || row.philhealth_deduction === undefined ? '' : String(row.philhealth_deduction),
            pagibig_deduction: row.pagibig_deduction === null || row.pagibig_deduction === undefined ? '' : String(row.pagibig_deduction),
            withholding_tax: row.withholding_tax === null || row.withholding_tax === undefined ? '' : String(row.withholding_tax),
        })),
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
                    searchPlaceholder="Jump to payroll…"
                    onChange={(id) => id && id !== record.id && router.visit(edit(id).url)}
                />
            </Deferred>
            {can(PayrollPermission.PayrollsDelete) && !record.is_paid && (
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="outline" size="icon" aria-label="More actions">
                            <MoreVertical />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem variant="destructive" onSelect={() => setConfirmDelete(true)}>
                            <Trash2 /> Delete payroll
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            )}
        </div>
    );

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title={`Edit ${record.payroll_number}`} />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Payroll list"
                    icon={Banknote}
                    title={String(record.payroll_number)}
                    description={`${record.period_start} → ${record.period_end} · net ₱${record.total_net.toFixed(2)}`}
                    actions={headerActions}
                />
                <div className={tabGridClass}>
                    <PayrollPreview data={form.data} title={String(record.payroll_number)} />
                    <div className="grid min-w-0 grid-cols-[minmax(0,1fr)] content-start gap-4">
                        <PayrollForm data={form.data} setData={form.setData} errors={form.errors} options={{ employee_options }} />
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
                title={`Delete ${record.payroll_number}?`}
                description="This payroll will be removed."
                confirmLabel="Delete payroll"
                processing={deleting}
                onConfirm={remove}
            />
        </UnsavedChangesGuard>
    );
}

PayrollEdit.layout = {
    breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'HR & Payroll' }, { title: 'Payroll', href: index().url }, { title: 'Edit' }],
};
