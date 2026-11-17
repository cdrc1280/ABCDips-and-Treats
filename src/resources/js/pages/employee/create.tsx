import { Head, useForm } from '@inertiajs/react';
import { IdCard, Loader2 } from 'lucide-react';
import type { FormEvent } from 'react';

import { AppFooter } from '@/components/custom/app-footer';
import { CreateHeader } from '@/components/custom/create-header';
import { UnsavedChangesGuard } from '@/components/custom/unsaved-changes-guard';
import { Button } from '@/components/ui/button';
import { tabGridClass } from '@/lib/layout';
import { dashboard } from '@/routes/admin';
import { index, store } from '@/routes/admin/employees';

import { EmployeeForm, type EmployeeFormData, type EmployeeFormOptions } from './employee-form';
import { EmployeePreview } from './employee-preview';

const initial: EmployeeFormData = {
    first_name: '',
    last_name: '',
    employee_number: '',
    email: '',
    phone: '',
    user_id: '',
    role_title: 'Pastry Staff',
    employment_type: 'full_time',
    basic_monthly_salary: '20000',
    hired_at: '',
    is_active: true,
};

export default function EmployeeCreate({ user_options }: EmployeeFormOptions) {
    const form = useForm<EmployeeFormData>(initial);

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.post(store().url, { onSuccess: () => form.setDefaults() });
    };

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title="New employee" />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Employee list"
                    icon={IdCard}
                    title="New employee"
                    description="Add a staff member. The employee number is generated if left empty."
                    onClear={() => form.reset()}
                    clearDisabled={!form.isDirty}
                />
                <div className={tabGridClass}>
                    <EmployeePreview data={form.data} />
                    <EmployeeForm data={form.data} setData={form.setData} errors={form.errors} options={{ user_options }} />
                </div>
                <AppFooter>
                    <Button type="button" variant="outline" size="sm" onClick={() => window.history.back()}>
                        Cancel
                    </Button>
                    <Button type="button" size="sm" disabled={form.processing} onClick={() => submit()}>
                        {form.processing && <Loader2 className="animate-spin" />} Save employee
                    </Button>
                </AppFooter>
            </form>
        </UnsavedChangesGuard>
    );
}

EmployeeCreate.layout = {
    breadcrumbs: [
        { title: 'Home', href: dashboard().url },
        { title: 'HR & Payroll' },
        { title: 'Employees', href: index().url },
        { title: 'New employee' },
    ],
};
