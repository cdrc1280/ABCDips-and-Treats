import { Deferred, Head, router, useForm } from '@inertiajs/react';
import { Users, Loader2 } from 'lucide-react';
import type { FormEvent } from 'react';

import { AppFooter } from '@/components/custom/app-footer';
import { CreateHeader } from '@/components/custom/create-header';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { UnsavedChangesGuard } from '@/components/custom/unsaved-changes-guard';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';
import { tabGridClass } from '@/lib/layout';
import { dashboard } from '@/routes/admin';
import { edit, index, update } from '@/routes/admin/customers';
import type { CustomerRecord, Option } from '@/types';

import { CustomerForm, type CustomerFormData } from './customer-form';
import { CustomerPreview } from './customer-preview';

type Props = { record: CustomerRecord; switcher?: Option<string>[] };

export default function CustomerEdit({ record, switcher }: Props) {
    const form = useForm<CustomerFormData>({
        name: record.name === null || record.name === undefined ? '' : String(record.name),
        email: record.email === null || record.email === undefined ? '' : String(record.email),
        phone: record.phone === null || record.phone === undefined ? '' : String(record.phone),
        street_address: record.street_address === null || record.street_address === undefined ? '' : String(record.street_address),
        barangay: record.barangay === null || record.barangay === undefined ? '' : String(record.barangay),
        city: record.city === null || record.city === undefined ? '' : String(record.city),
        province: record.province === null || record.province === undefined ? '' : String(record.province),
        region: record.region === null || record.region === undefined ? '' : String(record.region),
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
                    searchPlaceholder="Jump to customer…"
                    onChange={(id) => id && id !== record.id && router.visit(edit(id).url)}
                />
            </Deferred>
        </div>
    );

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title={`Edit ${record.name}`} />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Customer list"
                    icon={Users}
                    title={String(record.name)}
                    description={`${record.orders_count} orders · ₱${record.total_spent.toFixed(2)} spent`}
                    actions={headerActions}
                />
                <div className={tabGridClass}>
                    <CustomerPreview data={form.data} title={String(record.name)} />
                    <div className="grid min-w-0 grid-cols-[minmax(0,1fr)] content-start gap-4">
                        <CustomerForm data={form.data} setData={form.setData} errors={form.errors} options={{}} />
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

CustomerEdit.layout = {
    breadcrumbs: [
        { title: 'Home', href: dashboard().url },
        { title: 'Orders & Sales' },
        { title: 'Customers', href: index().url },
        { title: 'Edit' },
    ],
};
