import { Deferred, Head, router, useForm } from '@inertiajs/react';
import { Newspaper, Loader2, MoreVertical, Trash2 } from 'lucide-react';
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
import { BlogPostPermission } from '@/lib/permissions';
import { dashboard } from '@/routes/admin';
import { destroy, edit, index, update } from '@/routes/admin/blog-posts';
import type { BlogPostRecord, Option } from '@/types';

import { BlogPostForm, type BlogPostFormData } from './blog-post-form';
import { BlogPostPreview } from './blog-post-preview';

type Props = { record: BlogPostRecord; switcher?: Option<string>[] };

export default function BlogPostEdit({ record, switcher }: Props) {
    const { can } = usePermissions();
    const [confirmDelete, setConfirmDelete] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const form = useForm<BlogPostFormData>({
        title: record.title === null || record.title === undefined ? '' : String(record.title),
        slug: record.slug === null || record.slug === undefined ? '' : String(record.slug),
        category: record.category === null || record.category === undefined ? '' : String(record.category),
        excerpt: record.excerpt === null || record.excerpt === undefined ? '' : String(record.excerpt),
        body: record.body === null || record.body === undefined ? '' : String(record.body),
        cover_image: null,
        status: record.status === null || record.status === undefined ? '' : String(record.status),
        published_at: record.published_at ? record.published_at.slice(0, 16) : '',
        seo_title: record.seo_title === null || record.seo_title === undefined ? '' : String(record.seo_title),
        seo_description: record.seo_description === null || record.seo_description === undefined ? '' : String(record.seo_description),
        remove_cover_image: false,
    });

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.transform((data) => ({ ...data, _method: 'put' }));
        form.post(update(record.id).url, { forceFormData: true, onSuccess: () => form.setDefaults() });
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
                    searchPlaceholder="Jump to blog post…"
                    onChange={(id) => id && id !== record.id && router.visit(edit(id).url)}
                />
            </Deferred>
            {can(BlogPostPermission.BlogPostsDelete) && (
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="outline" size="icon" aria-label="More actions">
                            <MoreVertical />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem variant="destructive" onSelect={() => setConfirmDelete(true)}>
                            <Trash2 /> Delete blog post
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            )}
        </div>
    );

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title={`Edit ${record.title}`} />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Blog post list"
                    icon={Newspaper}
                    title={String(record.title)}
                    description={''}
                    actions={headerActions}
                />
                <div className={tabGridClass}>
                    <BlogPostPreview data={form.data} title={String(record.title)} />
                    <div className="grid min-w-0 grid-cols-[minmax(0,1fr)] content-start gap-4">
                        <BlogPostForm
                            data={form.data}
                            setData={form.setData}
                            errors={form.errors}
                            options={{}}
                            currentUrls={{ cover_image: record.cover_image_url }}
                        />
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
                title={`Delete ${record.title}?`}
                description="This blog post will be removed."
                confirmLabel="Delete blog post"
                processing={deleting}
                onConfirm={remove}
            />
        </UnsavedChangesGuard>
    );
}

BlogPostEdit.layout = {
    breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Engagement' }, { title: 'Blog posts', href: index().url }, { title: 'Edit' }],
};
