import { Deferred, Head, router, useForm } from '@inertiajs/react';
import { ShoppingBag, Loader2 } from 'lucide-react';
import type { FormEvent } from 'react';

import { AppFooter } from '@/components/custom/app-footer';
import { CreateHeader } from '@/components/custom/create-header';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { UnsavedChangesGuard } from '@/components/custom/unsaved-changes-guard';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';
import { tabGridClass } from '@/lib/layout';
import { dashboard } from '@/routes/admin';
import { edit, index, update } from '@/routes/admin/orders';
import type { OrderRecord, Option } from '@/types';

import { OrderForm, type OrderFormData } from './order-form';
import { OrderPreview } from './order-preview';
import { OrderDetails } from './order-details';

type Props = { record: OrderRecord; switcher?: Option<string>[] };

export default function OrderEdit({ record, switcher }: Props) {
    const form = useForm<OrderFormData>({
        customer_name: record.customer_name === null || record.customer_name === undefined ? '' : String(record.customer_name),
        customer_email: record.customer_email === null || record.customer_email === undefined ? '' : String(record.customer_email),
        customer_phone: record.customer_phone === null || record.customer_phone === undefined ? '' : String(record.customer_phone),
        delivery_address: record.delivery_address === null || record.delivery_address === undefined ? '' : String(record.delivery_address),
        city: record.city === null || record.city === undefined ? '' : String(record.city),
        postal_code: record.postal_code === null || record.postal_code === undefined ? '' : String(record.postal_code),
        scheduled_time: record.scheduled_time ? record.scheduled_time.slice(0, 16) : '',
        notes: record.notes === null || record.notes === undefined ? '' : String(record.notes),
        payment_status: record.payment_status === null || record.payment_status === undefined ? '' : String(record.payment_status),
        payment_reference: record.payment_reference === null || record.payment_reference === undefined ? '' : String(record.payment_reference),
    });

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.put(update(record.id).url, { onSuccess: () => form.setDefaults() });
    };

    const headerActions = (
        <div className="flex items-center gap-2">
            <Deferred data="switcher" fallback={<Skeleton className="h-9 w-56" />}>
                <SelectDropdown<string>
                    className="w-56"
                    value={record.id}
                    options={switcher ?? []}
                    searchPlaceholder="Jump to order…"
                    onChange={(id) => id && id !== record.id && router.visit(edit(id).url)}
                />
            </Deferred>
        </div>
    );

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title={`Edit ${record.order_number}`} />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Order list"
                    icon={ShoppingBag}
                    title={String(record.order_number)}
                    description={`${record.items_count} items · ₱${record.total.toFixed(2)} · ${record.payment_method.toUpperCase()}`}
                    actions={headerActions}
                />
                <div className={tabGridClass}>
                    <OrderPreview data={form.data} title={String(record.order_number)} />
                    <div className="grid min-w-0 grid-cols-[minmax(0,1fr)] content-start gap-4">
                        <OrderForm data={form.data} setData={form.setData} errors={form.errors} options={{}} />
                        <OrderDetails record={record} />
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
        </UnsavedChangesGuard>
    );
}

OrderEdit.layout = {
    breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Orders & Sales' }, { title: 'Orders', href: index().url }, { title: 'Edit' }],
};
