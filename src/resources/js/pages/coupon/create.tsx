import { Head, useForm } from '@inertiajs/react';
import { TicketPercent, Loader2 } from 'lucide-react';
import type { FormEvent } from 'react';

import { AppFooter } from '@/components/custom/app-footer';
import { CreateHeader } from '@/components/custom/create-header';
import { UnsavedChangesGuard } from '@/components/custom/unsaved-changes-guard';
import { Button } from '@/components/ui/button';
import { tabGridClass } from '@/lib/layout';
import { dashboard } from '@/routes/admin';
import { index, store } from '@/routes/admin/coupons';

import { CouponForm, type CouponFormData } from './coupon-form';
import { CouponPreview } from './coupon-preview';

const initial: CouponFormData = {
    code: '',
    type: 'fixed',
    value: '',
    min_spend: '0',
    max_discount: '',
    usage_limit: '',
    starts_at: '',
    expires_at: '',
    is_active: true,
};

export default function CouponCreate() {
    const form = useForm<CouponFormData>(initial);

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.post(store().url, { onSuccess: () => form.setDefaults() });
    };

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title="New coupon" />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Coupon list"
                    icon={TicketPercent}
                    title="New coupon"
                    description="Create a fixed or percentage discount code."
                    onClear={() => form.reset()}
                    clearDisabled={!form.isDirty}
                />
                <div className={tabGridClass}>
                    <CouponPreview data={form.data} />
                    <CouponForm data={form.data} setData={form.setData} errors={form.errors} options={{}} />
                </div>
                <AppFooter>
                    <Button type="button" variant="outline" size="sm" onClick={() => window.history.back()}>
                        Cancel
                    </Button>
                    <Button type="button" size="sm" disabled={form.processing} onClick={() => submit()}>
                        {form.processing && <Loader2 className="animate-spin" />} Save coupon
                    </Button>
                </AppFooter>
            </form>
        </UnsavedChangesGuard>
    );
}

CouponCreate.layout = {
    breadcrumbs: [
        { title: 'Home', href: dashboard().url },
        { title: 'Orders & Sales' },
        { title: 'Coupons', href: index().url },
        { title: 'New coupon' },
    ],
};
