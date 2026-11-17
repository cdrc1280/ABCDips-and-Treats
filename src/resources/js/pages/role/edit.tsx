import { Deferred, Head, router, useForm } from '@inertiajs/react';
import { ShieldCheck, Loader2, MoreVertical, Trash2 } from 'lucide-react';
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
import { RolePermission } from '@/lib/permissions';
import { dashboard } from '@/routes/admin';
import { destroy, edit, index, update } from '@/routes/admin/roles';
import type { RoleRecord, Option } from '@/types';

import { RoleForm, type RoleFormData, type RoleFormOptions } from './role-form';
import { RolePreview } from './role-preview';

type Props = { record: RoleRecord; switcher?: Option<string>[] } & RoleFormOptions;

export default function RoleEdit({ record, switcher, permission_options }: Props) {
    const { can } = usePermissions();
    const [confirmDelete, setConfirmDelete] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const form = useForm<RoleFormData>({
        name: record.name === null || record.name === undefined ? '' : String(record.name),
        permissions: record.permissions,
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
                    searchPlaceholder="Jump to role…"
                    onChange={(id) => id && id !== record.id && router.visit(edit(id).url)}
                />
            </Deferred>
            {can(RolePermission.RolesDelete) && !(record.is_system || record.users_count > 0) && (
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="outline" size="icon" aria-label="More actions">
                            <MoreVertical />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem variant="destructive" onSelect={() => setConfirmDelete(true)}>
                            <Trash2 /> Delete role
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
                    backLabel="Role list"
                    icon={ShieldCheck}
                    title={String(record.name)}
                    description={`${record.permissions_count} permissions · ${record.users_count} users`}
                    actions={headerActions}
                />
                <div className={tabGridClass}>
                    <RolePreview data={form.data} title={String(record.name)} />
                    <div className="grid min-w-0 grid-cols-[minmax(0,1fr)] content-start gap-4">
                        <RoleForm data={form.data} setData={form.setData} errors={form.errors} options={{ permission_options }} />
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
                description="This role will be removed."
                confirmLabel="Delete role"
                processing={deleting}
                onConfirm={remove}
            />
        </UnsavedChangesGuard>
    );
}

RoleEdit.layout = {
    breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'System' }, { title: 'Roles', href: index().url }, { title: 'Edit' }],
};
