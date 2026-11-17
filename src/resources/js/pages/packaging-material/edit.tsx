import { Deferred, Head, router, useForm } from '@inertiajs/react';
import { PackageOpen, Loader2, MoreVertical, Trash2 } from 'lucide-react';
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
import { PackagingMaterialPermission } from '@/lib/permissions';
import { dashboard } from '@/routes/admin';
import { destroy, edit, index, update } from '@/routes/admin/packaging-materials';
import type { PackagingMaterialRecord, Option } from '@/types';

import { PackagingMaterialForm, type PackagingMaterialFormData } from './packaging-material-form';
import { PackagingMaterialPreview } from './packaging-material-preview';

type Props = { record: PackagingMaterialRecord; switcher?: Option<string>[] };

export default function PackagingMaterialEdit({ record, switcher }: Props) {
    const { can } = usePermissions();
    const [confirmDelete, setConfirmDelete] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const form = useForm<PackagingMaterialFormData>({
        name: record.name === null || record.name === undefined ? '' : String(record.name),
        sku: record.sku === null || record.sku === undefined ? '' : String(record.sku),
        type: record.type === null || record.type === undefined ? '' : String(record.type),
        unit: record.unit === null || record.unit === undefined ? '' : String(record.unit),
        cost_per_unit: record.cost_per_unit === null || record.cost_per_unit === undefined ? '' : String(record.cost_per_unit),
        stock_qty: record.stock_qty === null || record.stock_qty === undefined ? '' : String(record.stock_qty),
        min_stock_qty: record.min_stock_qty === null || record.min_stock_qty === undefined ? '' : String(record.min_stock_qty),
        is_active: record.is_active,
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
                    searchPlaceholder="Jump to packaging material…"
                    onChange={(id) => id && id !== record.id && router.visit(edit(id).url)}
                />
            </Deferred>
            {can(PackagingMaterialPermission.PackagingMaterialsDelete) && (
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="outline" size="icon" aria-label="More actions">
                            <MoreVertical />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem variant="destructive" onSelect={() => setConfirmDelete(true)}>
                            <Trash2 /> Delete packaging material
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
                    backLabel="Packaging material list"
                    icon={PackageOpen}
                    title={String(record.name)}
                    description={''}
                    actions={headerActions}
                />
                <div className={tabGridClass}>
                    <PackagingMaterialPreview data={form.data} title={String(record.name)} />
                    <div className="grid min-w-0 grid-cols-[minmax(0,1fr)] content-start gap-4">
                        <PackagingMaterialForm data={form.data} setData={form.setData} errors={form.errors} options={{}} />
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
                description="This packaging material will be removed."
                confirmLabel="Delete packaging material"
                processing={deleting}
                onConfirm={remove}
            />
        </UnsavedChangesGuard>
    );
}

PackagingMaterialEdit.layout = {
    breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Inventory' }, { title: 'Packaging', href: index().url }, { title: 'Edit' }],
};
