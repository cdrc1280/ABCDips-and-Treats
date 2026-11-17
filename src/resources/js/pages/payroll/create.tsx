import { Head, useForm } from '@inertiajs/react';
import { Banknote, Loader2 } from 'lucide-react';
import type { FormEvent } from 'react';

import { AppFooter } from '@/components/custom/app-footer';
import { CreateHeader } from '@/components/custom/create-header';
import { UnsavedChangesGuard } from '@/components/custom/unsaved-changes-guard';
import { Button } from '@/components/ui/button';
import { tabGridClass } from '@/lib/layout';
import { dashboard } from '@/routes/admin';
import { index, store } from '@/routes/admin/payrolls';

import { PayrollForm, type PayrollFormData, type PayrollFormOptions } from './payroll-form';
import { PayrollPreview } from './payroll-preview';

const initial: PayrollFormData = {
    payroll_number: '',
    period_start: '',
    period_end: '',
    status: 'draft',
    notes: '',
    items: [],
};

export default function PayrollCreate({ employee_options }: PayrollFormOptions) {
    const form = useForm<PayrollFormData>(initial);

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.post(store().url, { onSuccess: () => form.setDefaults() });
    };

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title="New payroll" />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Payroll list"
                    icon={Banknote}
                    title="New payroll"
                    description="Pick the period — basic pay, overtime and statutory deductions are computed from attendance."
                    onClear={() => form.reset()}
                    clearDisabled={!form.isDirty}
                />
                <div className={tabGridClass}>
                    <PayrollPreview data={form.data} />
                    <PayrollForm data={form.data} setData={form.setData} errors={form.errors} options={{ employee_options }} />
                </div>
                <AppFooter>
                    <Button type="button" variant="outline" size="sm" onClick={() => window.history.back()}>
                        Cancel
                    </Button>
                    <Button type="button" size="sm" disabled={form.processing} onClick={() => submit()}>
                        {form.processing && <Loader2 className="animate-spin" />} Save payroll
                    </Button>
                </AppFooter>
            </form>
        </UnsavedChangesGuard>
    );
}

PayrollCreate.layout = {
    breadcrumbs: [
        { title: 'Home', href: dashboard().url },
        { title: 'HR & Payroll' },
        { title: 'Payroll', href: index().url },
        { title: 'New payroll' },
    ],
};
