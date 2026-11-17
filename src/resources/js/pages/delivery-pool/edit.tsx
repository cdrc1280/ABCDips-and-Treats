import { Deferred, Head, router, useForm } from '@inertiajs/react';
import { Route, Loader2, MoreVertical, Trash2 } from 'lucide-react';
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
import { DeliveryPoolPermission } from '@/lib/permissions';
import { dashboard } from '@/routes/admin';
import { destroy, edit, index, update } from '@/routes/admin/delivery-pools';
import type { DeliveryPoolRecord, Option } from '@/types';

import { DeliveryPoolForm, type DeliveryPoolFormData } from './delivery-pool-form';
import { DeliveryPoolPreview } from './delivery-pool-preview';

type Props = { record: DeliveryPoolRecord; switcher?: Option<string>[] };

export default function DeliveryPoolEdit({ record, switcher }: Props) {
    const { can } = usePermissions();
    const [confirmDelete, setConfirmDelete] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const form = useForm<DeliveryPoolFormData>({
        pool_code: record.pool_code === null || record.pool_code === undefined ? '' : String(record.pool_code),
        zone_name: record.zone_name === null || record.zone_name === undefined ? '' : String(record.zone_name),
        region: record.region === null || record.region === undefined ? '' : String(record.region),
        province: record.province === null || record.province === undefined ? '' : String(record.province),
        city: record.city === null || record.city === undefined ? '' : String(record.city),
        barangay: record.barangay === null || record.barangay === undefined ? '' : String(record.barangay),
        total_delivery_fee: record.total_delivery_fee === null || record.total_delivery_fee === undefined ? '' : String(record.total_delivery_fee),
        status: record.status === null || record.status === undefined ? '' : String(record.status),
        allocations: record.allocations.map((row) => ({
            order_id: row.order_id === null || row.order_id === undefined ? '' : String(row.order_id),
            assigned_fee: row.assigned_fee === null || row.assigned_fee === undefined ? '' : String(row.assigned_fee),
        })),
        notes: record.notes === null || record.notes === undefined ? '' : String(record.notes),
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
                    searchPlaceholder="Jump to delivery pool…"
                    onChange={(id) => id && id !== record.id && router.visit(edit(id).url)}
                />
            </Deferred>
            {can(DeliveryPoolPermission.DeliveryPoolsDelete) && !(record.orders_count > 0) && (
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="outline" size="icon" aria-label="More actions">
                            <MoreVertical />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem variant="destructive" onSelect={() => setConfirmDelete(true)}>
                            <Trash2 /> Delete delivery pool
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            )}
        </div>
    );

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title={`Edit ${record.pool_code}`} />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Delivery pool list"
                    icon={Route}
                    title={String(record.pool_code)}
                    description={`${record.orders_count} orders · trip ₱${record.total_delivery_fee.toFixed(2)} · allocated ₱${record.allocated_fees.toFixed(2)}`}
                    actions={headerActions}
                />
                <div className={tabGridClass}>
                    <DeliveryPoolPreview data={form.data} title={String(record.pool_code)} />
                    <div className="grid min-w-0 grid-cols-[minmax(0,1fr)] content-start gap-4">
                        <DeliveryPoolForm data={form.data} setData={form.setData} errors={form.errors} options={{}} />
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
                title={`Delete ${record.pool_code}?`}
                description="This delivery pool will be removed."
                confirmLabel="Delete delivery pool"
                processing={deleting}
                onConfirm={remove}
            />
        </UnsavedChangesGuard>
    );
}

DeliveryPoolEdit.layout = {
    breadcrumbs: [
        { title: 'Home', href: dashboard().url },
        { title: 'Orders & Sales' },
        { title: 'Delivery pools', href: index().url },
        { title: 'Edit' },
    ],
};
