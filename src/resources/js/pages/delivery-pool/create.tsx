import { Head, useForm } from '@inertiajs/react';
import { Route, Loader2 } from 'lucide-react';
import type { FormEvent } from 'react';

import { AppFooter } from '@/components/custom/app-footer';
import { CreateHeader } from '@/components/custom/create-header';
import { UnsavedChangesGuard } from '@/components/custom/unsaved-changes-guard';
import { Button } from '@/components/ui/button';
import { tabGridClass } from '@/lib/layout';
import { dashboard } from '@/routes/admin';
import { index, store } from '@/routes/admin/delivery-pools';

import { DeliveryPoolForm, type DeliveryPoolFormData } from './delivery-pool-form';
import { DeliveryPoolPreview } from './delivery-pool-preview';

const initial: DeliveryPoolFormData = {
    pool_code: '',
    zone_name: '',
    region: '',
    province: '',
    city: '',
    barangay: '',
    total_delivery_fee: '0',
    status: 'open',
    allocations: [],
    notes: '',
};

export default function DeliveryPoolCreate() {
    const form = useForm<DeliveryPoolFormData>(initial);

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.post(store().url, { onSuccess: () => form.setDefaults() });
    };

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title="New delivery pool" />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Delivery pool list"
                    icon={Route}
                    title="New delivery pool"
                    description="Group nearby pooling orders and set each order’s share of the shipping fee."
                    onClear={() => form.reset()}
                    clearDisabled={!form.isDirty}
                />
                <div className={tabGridClass}>
                    <DeliveryPoolPreview data={form.data} />
                    <DeliveryPoolForm data={form.data} setData={form.setData} errors={form.errors} options={{}} />
                </div>
                <AppFooter>
                    <Button type="button" variant="outline" size="sm" onClick={() => window.history.back()}>
                        Cancel
                    </Button>
                    <Button type="button" size="sm" disabled={form.processing} onClick={() => submit()}>
                        {form.processing && <Loader2 className="animate-spin" />} Save delivery pool
                    </Button>
                </AppFooter>
            </form>
        </UnsavedChangesGuard>
    );
}

DeliveryPoolCreate.layout = {
    breadcrumbs: [
        { title: 'Home', href: dashboard().url },
        { title: 'Orders & Sales' },
        { title: 'Delivery pools', href: index().url },
        { title: 'New delivery pool' },
    ],
};
