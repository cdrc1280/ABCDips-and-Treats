import { Head, useForm } from '@inertiajs/react';
import { CalendarCheck, Loader2 } from 'lucide-react';
import type { FormEvent } from 'react';

import { AppFooter } from '@/components/custom/app-footer';
import { CreateHeader } from '@/components/custom/create-header';
import { UnsavedChangesGuard } from '@/components/custom/unsaved-changes-guard';
import { Button } from '@/components/ui/button';
import { tabGridClass } from '@/lib/layout';
import { dashboard } from '@/routes/admin';
import { index, store } from '@/routes/admin/attendances';

import { AttendanceForm, type AttendanceFormData, type AttendanceFormOptions } from './attendance-form';
import { AttendancePreview } from './attendance-preview';

const initial: AttendanceFormData = {
    employee_id: '',
    date: '',
    status: 'present',
    time_in: '',
    time_out: '',
    hours_worked: '8',
    is_overtime: false,
    overtime_hours: '0',
    is_holiday: false,
    notes: '',
};

export default function AttendanceCreate({ employee_options }: AttendanceFormOptions) {
    const form = useForm<AttendanceFormData>(initial);

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.post(store().url, { onSuccess: () => form.setDefaults() });
    };

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title="New attendance" />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Attendance list"
                    icon={CalendarCheck}
                    title="New attendance"
                    description="Log one employee-day."
                    onClear={() => form.reset()}
                    clearDisabled={!form.isDirty}
                />
                <div className={tabGridClass}>
                    <AttendancePreview data={form.data} />
                    <AttendanceForm data={form.data} setData={form.setData} errors={form.errors} options={{ employee_options }} />
                </div>
                <AppFooter>
                    <Button type="button" variant="outline" size="sm" onClick={() => window.history.back()}>
                        Cancel
                    </Button>
                    <Button type="button" size="sm" disabled={form.processing} onClick={() => submit()}>
                        {form.processing && <Loader2 className="animate-spin" />} Save attendance
                    </Button>
                </AppFooter>
            </form>
        </UnsavedChangesGuard>
    );
}

AttendanceCreate.layout = {
    breadcrumbs: [
        { title: 'Home', href: dashboard().url },
        { title: 'HR & Payroll' },
        { title: 'Attendance', href: index().url },
        { title: 'New attendance' },
    ],
};
