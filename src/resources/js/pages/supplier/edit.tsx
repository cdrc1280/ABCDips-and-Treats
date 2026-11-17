import { Deferred, Head, router, useForm } from '@inertiajs/react';
import { Truck, Loader2, MoreVertical, Trash2 } from 'lucide-react';
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
import { SupplierPermission } from '@/lib/permissions';
import { dashboard } from '@/routes/admin';
import { destroy, edit, index, update } from '@/routes/admin/suppliers';
import type { SupplierRecord, Option } from '@/types';

import { SupplierForm, type SupplierFormData } from './supplier-form';
import { SupplierPreview } from './supplier-preview';

type Props = { record: SupplierRecord; switcher?: Option<string>[] };

export default function SupplierEdit({ record, switcher }: Props) {
    const { can } = usePermissions();
    const [confirmDelete, setConfirmDelete] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const form = useForm<SupplierFormData>({
        name: record.name === null || record.name === undefined ? '' : String(record.name),
        contact_person: record.contact_person === null || record.contact_person === undefined ? '' : String(record.contact_person),
        email: record.email === null || record.email === undefined ? '' : String(record.email),
        phone: record.phone === null || record.phone === undefined ? '' : String(record.phone),
        payment_terms: record.payment_terms === null || record.payment_terms === undefined ? '' : String(record.payment_terms),
        address: record.address === null || record.address === undefined ? '' : String(record.address),
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
                    searchPlaceholder="Jump to supplier…"
                    onChange={(id) => id && id !== record.id && router.visit(edit(id).url)}
                />
            </Deferred>
            {can(SupplierPermission.SuppliersDelete) && !record.in_use && (
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="outline" size="icon" aria-label="More actions">
                            <MoreVertical />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem variant="destructive" onSelect={() => setConfirmDelete(true)}>
                            <Trash2 /> Delete supplier
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            )}
        </div>
    );

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title={`Edit ${record.name}`} />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Supplier list"
                    icon={Truck}
                    title={String(record.name)}
                    description={`${record.ingredients_count} ingredients · ${record.purchase_orders_count} purchase orders`}
                    actions={headerActions}
                />
                <div className={tabGridClass}>
                    <SupplierPreview data={form.data} title={String(record.name)} />
                    <div className="grid min-w-0 grid-cols-[minmax(0,1fr)] content-start gap-4">
                        <SupplierForm data={form.data} setData={form.setData} errors={form.errors} options={{}} />
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
                title={`Delete ${record.name}?`}
                description="This supplier will be removed."
                confirmLabel="Delete supplier"
                processing={deleting}
                onConfirm={remove}
            />
        </UnsavedChangesGuard>
    );
}

SupplierEdit.layout = {
    breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Inventory' }, { title: 'Suppliers', href: index().url }, { title: 'Edit' }],
};
