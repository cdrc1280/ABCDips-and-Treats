import { Head, useForm } from '@inertiajs/react';
import { Newspaper, Loader2 } from 'lucide-react';
import type { FormEvent } from 'react';

import { AppFooter } from '@/components/custom/app-footer';
import { CreateHeader } from '@/components/custom/create-header';
import { UnsavedChangesGuard } from '@/components/custom/unsaved-changes-guard';
import { Button } from '@/components/ui/button';
import { tabGridClass } from '@/lib/layout';
import { dashboard } from '@/routes/admin';
import { index, store } from '@/routes/admin/blog-posts';

import { BlogPostForm, type BlogPostFormData } from './blog-post-form';
import { BlogPostPreview } from './blog-post-preview';

const initial: BlogPostFormData = {
    title: '',
    slug: '',
    category: 'General',
    excerpt: '',
    body: '',
    cover_image: null,
    status: 'draft',
    published_at: '',
    seo_title: '',
    seo_description: '',
    remove_cover_image: false,
};

export default function BlogPostCreate() {
    const form = useForm<BlogPostFormData>(initial);

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.post(store().url, { forceFormData: true, onSuccess: () => form.setDefaults() });
    };

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title="New blog post" />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Blog post list"
                    icon={Newspaper}
                    title="New blog post"
                    description="Write a journal post. Save it as a draft until it is ready."
                    onClear={() => form.reset()}
                    clearDisabled={!form.isDirty}
                />
                <div className={tabGridClass}>
                    <BlogPostPreview data={form.data} />
                    <BlogPostForm data={form.data} setData={form.setData} errors={form.errors} options={{}} />
                </div>
                <AppFooter>
                    <Button type="button" variant="outline" size="sm" onClick={() => window.history.back()}>
                        Cancel
                    </Button>
                    <Button type="button" size="sm" disabled={form.processing} onClick={() => submit()}>
                        {form.processing && <Loader2 className="animate-spin" />} Save blog post
                    </Button>
                </AppFooter>
            </form>
        </UnsavedChangesGuard>
    );
}

BlogPostCreate.layout = {
    breadcrumbs: [
        { title: 'Home', href: dashboard().url },
        { title: 'Engagement' },
        { title: 'Blog posts', href: index().url },
        { title: 'New blog post' },
    ],
};
