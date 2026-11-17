import { Head, useForm } from '@inertiajs/react';
import { UserCog, Loader2 } from 'lucide-react';
import type { FormEvent } from 'react';

import { AppFooter } from '@/components/custom/app-footer';
import { CreateHeader } from '@/components/custom/create-header';
import { UnsavedChangesGuard } from '@/components/custom/unsaved-changes-guard';
import { Button } from '@/components/ui/button';
import { tabGridClass } from '@/lib/layout';
import { dashboard } from '@/routes/admin';
import { index, store } from '@/routes/admin/users';

import { UserForm, type UserFormData, type UserFormOptions } from './user-form';
import { UserPreview } from './user-preview';

const initial: UserFormData = {
    name: '',
    email: '',
    phone: '',
    roles: [],
    password: '',
    password_confirmation: '',
};

export default function UserCreate({ role_options }: UserFormOptions) {
    const form = useForm<UserFormData>(initial);

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.post(store().url, { onSuccess: () => form.setDefaults() });
    };

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title="New staff account" />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Staff account list"
                    icon={UserCog}
                    title="New staff account"
                    description="Create a sign-in for a staff member and choose their roles."
                    onClear={() => form.reset()}
                    clearDisabled={!form.isDirty}
                />
                <div className={tabGridClass}>
                    <UserPreview data={form.data} />
                    <UserForm data={form.data} setData={form.setData} errors={form.errors} options={{ role_options }} />
                </div>
                <AppFooter>
                    <Button type="button" variant="outline" size="sm" onClick={() => window.history.back()}>
                        Cancel
                    </Button>
                    <Button type="button" size="sm" disabled={form.processing} onClick={() => submit()}>
                        {form.processing && <Loader2 className="animate-spin" />} Save staff account
                    </Button>
                </AppFooter>
            </form>
        </UnsavedChangesGuard>
    );
}

UserCreate.layout = {
    breadcrumbs: [
        { title: 'Home', href: dashboard().url },
        { title: 'System' },
        { title: 'Staff accounts', href: index().url },
        { title: 'New staff account' },
    ],
};
