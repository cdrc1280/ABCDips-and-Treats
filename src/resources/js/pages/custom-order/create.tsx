import { Head, useForm } from '@inertiajs/react';
import { Cake, Loader2 } from 'lucide-react';
import type { FormEvent } from 'react';

import { AppFooter } from '@/components/custom/app-footer';
import { CreateHeader } from '@/components/custom/create-header';
import { UnsavedChangesGuard } from '@/components/custom/unsaved-changes-guard';
import { Button } from '@/components/ui/button';
import { tabGridClass } from '@/lib/layout';
import { dashboard } from '@/routes/admin';
import { index, store } from '@/routes/admin/custom-orders';

import { CustomOrderForm, type CustomOrderFormData } from './custom-order-form';
import { CustomOrderPreview } from './custom-order-preview';

const initial: CustomOrderFormData = {
    reference_number: '',
    customer_name: '',
    customer_email: '',
    customer_phone: '',
    event_date: '',
    servings_count: '20',
    tiers_count: '1',
    flavor_preference: '',
    frosting_type: '',
    theme_description: '',
    budget_range_min: '',
    budget_range_max: '',
    quoted_price: '',
    status: 'inquiry',
    staff_notes: '',
};

export default function CustomOrderCreate() {
    const form = useForm<CustomOrderFormData>(initial);

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.post(store().url, { onSuccess: () => form.setDefaults() });
    };

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title="New custom order" />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Custom order list"
                    icon={Cake}
                    title="New custom order"
                    description="Log a custom cake request taken by phone, chat or in-store."
                    onClear={() => form.reset()}
                    clearDisabled={!form.isDirty}
                />
                <div className={tabGridClass}>
                    <CustomOrderPreview data={form.data} />
                    <CustomOrderForm data={form.data} setData={form.setData} errors={form.errors} options={{}} />
                </div>
                <AppFooter>
                    <Button type="button" variant="outline" size="sm" onClick={() => window.history.back()}>
                        Cancel
                    </Button>
                    <Button type="button" size="sm" disabled={form.processing} onClick={() => submit()}>
                        {form.processing && <Loader2 className="animate-spin" />} Save custom order
                    </Button>
                </AppFooter>
            </form>
        </UnsavedChangesGuard>
    );
}

CustomOrderCreate.layout = {
    breadcrumbs: [
        { title: 'Home', href: dashboard().url },
        { title: 'Orders & Sales' },
        { title: 'Custom orders', href: index().url },
        { title: 'New custom order' },
    ],
};
