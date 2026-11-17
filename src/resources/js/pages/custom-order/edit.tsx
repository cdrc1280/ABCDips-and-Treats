import { Deferred, Head, router, useForm } from '@inertiajs/react';
import { Cake, Loader2, MoreVertical, Trash2 } from 'lucide-react';
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
import { CustomOrderPermission } from '@/lib/permissions';
import { dashboard } from '@/routes/admin';
import { destroy, edit, index, update } from '@/routes/admin/custom-orders';
import type { CustomOrderRecord, Option } from '@/types';

import { CustomOrderForm, type CustomOrderFormData } from './custom-order-form';
import { CustomOrderPreview } from './custom-order-preview';

type Props = { record: CustomOrderRecord; switcher?: Option<string>[] };

export default function CustomOrderEdit({ record, switcher }: Props) {
    const { can } = usePermissions();
    const [confirmDelete, setConfirmDelete] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const form = useForm<CustomOrderFormData>({
        reference_number: record.reference_number === null || record.reference_number === undefined ? '' : String(record.reference_number),
        customer_name: record.customer_name === null || record.customer_name === undefined ? '' : String(record.customer_name),
        customer_email: record.customer_email === null || record.customer_email === undefined ? '' : String(record.customer_email),
        customer_phone: record.customer_phone === null || record.customer_phone === undefined ? '' : String(record.customer_phone),
        event_date: record.event_date === null || record.event_date === undefined ? '' : String(record.event_date),
        servings_count: record.servings_count === null || record.servings_count === undefined ? '' : String(record.servings_count),
        tiers_count: record.tiers_count === null || record.tiers_count === undefined ? '' : String(record.tiers_count),
        flavor_preference: record.flavor_preference === null || record.flavor_preference === undefined ? '' : String(record.flavor_preference),
        frosting_type: record.frosting_type === null || record.frosting_type === undefined ? '' : String(record.frosting_type),
        theme_description: record.theme_description === null || record.theme_description === undefined ? '' : String(record.theme_description),
        budget_range_min: record.budget_range_min === null || record.budget_range_min === undefined ? '' : String(record.budget_range_min),
        budget_range_max: record.budget_range_max === null || record.budget_range_max === undefined ? '' : String(record.budget_range_max),
        quoted_price: record.quoted_price === null || record.quoted_price === undefined ? '' : String(record.quoted_price),
        status: record.status === null || record.status === undefined ? '' : String(record.status),
        staff_notes: record.staff_notes === null || record.staff_notes === undefined ? '' : String(record.staff_notes),
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
                    searchPlaceholder="Jump to custom order…"
                    onChange={(id) => id && id !== record.id && router.visit(edit(id).url)}
                />
            </Deferred>
            {can(CustomOrderPermission.CustomOrdersDelete) && (
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="outline" size="icon" aria-label="More actions">
                            <MoreVertical />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem variant="destructive" onSelect={() => setConfirmDelete(true)}>
                            <Trash2 /> Delete custom order
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            )}
        </div>
    );

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title={`Edit ${record.reference_number}`} />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Custom order list"
                    icon={Cake}
                    title={String(record.reference_number)}
                    description={''}
                    actions={headerActions}
                />
                <div className={tabGridClass}>
                    <CustomOrderPreview data={form.data} title={String(record.reference_number)} />
                    <div className="grid min-w-0 grid-cols-[minmax(0,1fr)] content-start gap-4">
                        <CustomOrderForm data={form.data} setData={form.setData} errors={form.errors} options={{}} />
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
                title={`Delete ${record.reference_number}?`}
                description="This custom order will be removed."
                confirmLabel="Delete custom order"
                processing={deleting}
                onConfirm={remove}
            />
        </UnsavedChangesGuard>
    );
}

CustomOrderEdit.layout = {
    breadcrumbs: [
        { title: 'Home', href: dashboard().url },
        { title: 'Orders & Sales' },
        { title: 'Custom orders', href: index().url },
        { title: 'Edit' },
    ],
};
