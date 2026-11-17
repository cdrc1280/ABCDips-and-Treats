import { Deferred, Head, router, useForm } from '@inertiajs/react';
import { Star, Loader2, MoreVertical, Trash2 } from 'lucide-react';
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
import { ReviewPermission } from '@/lib/permissions';
import { dashboard } from '@/routes/admin';
import { destroy, edit, index, update } from '@/routes/admin/reviews';
import type { ReviewRecord, Option } from '@/types';

import { ReviewForm, type ReviewFormData, type ReviewFormOptions } from './review-form';
import { ReviewPreview } from './review-preview';

type Props = { record: ReviewRecord; switcher?: Option<string>[] } & ReviewFormOptions;

export default function ReviewEdit({ record, switcher, product_options }: Props) {
    const { can } = usePermissions();
    const [confirmDelete, setConfirmDelete] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const form = useForm<ReviewFormData>({
        product_id: record.product_id === null || record.product_id === undefined ? '' : String(record.product_id),
        rating: record.rating === null || record.rating === undefined ? '' : String(record.rating),
        reviewer_name: record.reviewer_name === null || record.reviewer_name === undefined ? '' : String(record.reviewer_name),
        reviewer_email: record.reviewer_email === null || record.reviewer_email === undefined ? '' : String(record.reviewer_email),
        title: record.title === null || record.title === undefined ? '' : String(record.title),
        comment: record.comment === null || record.comment === undefined ? '' : String(record.comment),
        is_approved: record.is_approved,
        is_featured: record.is_featured,
        is_verified_buyer: record.is_verified_buyer,
        is_anonymous: record.is_anonymous,
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
                    searchPlaceholder="Jump to review…"
                    onChange={(id) => id && id !== record.id && router.visit(edit(id).url)}
                />
            </Deferred>
            {can(ReviewPermission.ReviewsDelete) && (
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="outline" size="icon" aria-label="More actions">
                            <MoreVertical />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem variant="destructive" onSelect={() => setConfirmDelete(true)}>
                            <Trash2 /> Delete review
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            )}
        </div>
    );

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title={`Edit ${record.reviewer_name}`} />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Review list"
                    icon={Star}
                    title={String(record.reviewer_name)}
                    description={''}
                    actions={headerActions}
                />
                <div className={tabGridClass}>
                    <ReviewPreview data={form.data} title={String(record.reviewer_name)} />
                    <div className="grid min-w-0 grid-cols-[minmax(0,1fr)] content-start gap-4">
                        <ReviewForm data={form.data} setData={form.setData} errors={form.errors} options={{ product_options }} />
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
                title={`Delete ${record.reviewer_name}?`}
                description="This review will be removed."
                confirmLabel="Delete review"
                processing={deleting}
                onConfirm={remove}
            />
        </UnsavedChangesGuard>
    );
}

ReviewEdit.layout = {
    breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Orders & Sales' }, { title: 'Reviews', href: index().url }, { title: 'Edit' }],
};
