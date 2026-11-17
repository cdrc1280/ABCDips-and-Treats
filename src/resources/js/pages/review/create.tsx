import { Head, useForm } from '@inertiajs/react';
import { Star, Loader2 } from 'lucide-react';
import type { FormEvent } from 'react';

import { AppFooter } from '@/components/custom/app-footer';
import { CreateHeader } from '@/components/custom/create-header';
import { UnsavedChangesGuard } from '@/components/custom/unsaved-changes-guard';
import { Button } from '@/components/ui/button';
import { tabGridClass } from '@/lib/layout';
import { dashboard } from '@/routes/admin';
import { index, store } from '@/routes/admin/reviews';

import { ReviewForm, type ReviewFormData, type ReviewFormOptions } from './review-form';
import { ReviewPreview } from './review-preview';

const initial: ReviewFormData = {
    product_id: '',
    rating: '5',
    reviewer_name: '',
    reviewer_email: '',
    title: '',
    comment: '',
    is_approved: true,
    is_featured: false,
    is_verified_buyer: false,
    is_anonymous: false,
};

export default function ReviewCreate({ product_options }: ReviewFormOptions) {
    const form = useForm<ReviewFormData>(initial);

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.post(store().url, { onSuccess: () => form.setDefaults() });
    };

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title="New review" />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Review list"
                    icon={Star}
                    title="New review"
                    description="Add a review on behalf of a customer (e.g. from Facebook or in-store)."
                    onClear={() => form.reset()}
                    clearDisabled={!form.isDirty}
                />
                <div className={tabGridClass}>
                    <ReviewPreview data={form.data} />
                    <ReviewForm data={form.data} setData={form.setData} errors={form.errors} options={{ product_options }} />
                </div>
                <AppFooter>
                    <Button type="button" variant="outline" size="sm" onClick={() => window.history.back()}>
                        Cancel
                    </Button>
                    <Button type="button" size="sm" disabled={form.processing} onClick={() => submit()}>
                        {form.processing && <Loader2 className="animate-spin" />} Save review
                    </Button>
                </AppFooter>
            </form>
        </UnsavedChangesGuard>
    );
}

ReviewCreate.layout = {
    breadcrumbs: [
        { title: 'Home', href: dashboard().url },
        { title: 'Orders & Sales' },
        { title: 'Reviews', href: index().url },
        { title: 'New review' },
    ],
};
