import { Deferred, Head, router, useForm } from '@inertiajs/react';
import { TicketPercent, Loader2, MoreVertical, Trash2 } from 'lucide-react';
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
import { CouponPermission } from '@/lib/permissions';
import { dashboard } from '@/routes/admin';
import { destroy, edit, index, update } from '@/routes/admin/coupons';
import type { CouponRecord, Option } from '@/types';

import { CouponForm, type CouponFormData } from './coupon-form';
import { CouponPreview } from './coupon-preview';

type Props = { record: CouponRecord; switcher?: Option<string>[] };

export default function CouponEdit({ record, switcher }: Props) {
    const { can } = usePermissions();
    const [confirmDelete, setConfirmDelete] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const form = useForm<CouponFormData>({
        code: record.code === null || record.code === undefined ? '' : String(record.code),
        type: record.type === null || record.type === undefined ? '' : String(record.type),
        value: record.value === null || record.value === undefined ? '' : String(record.value),
        min_spend: record.min_spend === null || record.min_spend === undefined ? '' : String(record.min_spend),
        max_discount: record.max_discount === null || record.max_discount === undefined ? '' : String(record.max_discount),
        usage_limit: record.usage_limit === null || record.usage_limit === undefined ? '' : String(record.usage_limit),
        starts_at: record.starts_at ? record.starts_at.slice(0, 16) : '',
        expires_at: record.expires_at ? record.expires_at.slice(0, 16) : '',
        is_active: record.is_active,
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
                    searchPlaceholder="Jump to coupon…"
                    onChange={(id) => id && id !== record.id && router.visit(edit(id).url)}
                />
            </Deferred>
            {can(CouponPermission.CouponsDelete) && (
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="outline" size="icon" aria-label="More actions">
                            <MoreVertical />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem variant="destructive" onSelect={() => setConfirmDelete(true)}>
                            <Trash2 /> Delete coupon
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            )}
        </div>
    );

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title={`Edit ${record.code}`} />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Coupon list"
                    icon={TicketPercent}
                    title={String(record.code)}
                    description={''}
                    actions={headerActions}
                />
                <div className={tabGridClass}>
                    <CouponPreview data={form.data} title={String(record.code)} />
                    <div className="grid min-w-0 grid-cols-[minmax(0,1fr)] content-start gap-4">
                        <CouponForm data={form.data} setData={form.setData} errors={form.errors} options={{}} />
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
                title={`Delete ${record.code}?`}
                description="This coupon will be removed."
                confirmLabel="Delete coupon"
                processing={deleting}
                onConfirm={remove}
            />
        </UnsavedChangesGuard>
    );
}

CouponEdit.layout = {
    breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Orders & Sales' }, { title: 'Coupons', href: index().url }, { title: 'Edit' }],
};
