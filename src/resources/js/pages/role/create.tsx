import { Head, useForm } from '@inertiajs/react';
import { ShieldCheck, Loader2 } from 'lucide-react';
import type { FormEvent } from 'react';

import { AppFooter } from '@/components/custom/app-footer';
import { CreateHeader } from '@/components/custom/create-header';
import { UnsavedChangesGuard } from '@/components/custom/unsaved-changes-guard';
import { Button } from '@/components/ui/button';
import { tabGridClass } from '@/lib/layout';
import { dashboard } from '@/routes/admin';
import { index, store } from '@/routes/admin/roles';

import { RoleForm, type RoleFormData, type RoleFormOptions } from './role-form';
import { RolePreview } from './role-preview';

const initial: RoleFormData = {
    name: '',
    permissions: [],
};

export default function RoleCreate({ permission_options }: RoleFormOptions) {
    const form = useForm<RoleFormData>(initial);

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.post(store().url, { onSuccess: () => form.setDefaults() });
    };

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title="New role" />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Role list"
                    icon={ShieldCheck}
                    title="New role"
                    description="Group the permissions a job needs, then assign the role to staff."
                    onClear={() => form.reset()}
                    clearDisabled={!form.isDirty}
                />
                <div className={tabGridClass}>
                    <RolePreview data={form.data} />
                    <RoleForm data={form.data} setData={form.setData} errors={form.errors} options={{ permission_options }} />
                </div>
                <AppFooter>
                    <Button type="button" variant="outline" size="sm" onClick={() => window.history.back()}>
                        Cancel
                    </Button>
                    <Button type="button" size="sm" disabled={form.processing} onClick={() => submit()}>
                        {form.processing && <Loader2 className="animate-spin" />} Save role
                    </Button>
                </AppFooter>
            </form>
        </UnsavedChangesGuard>
    );
}

RoleCreate.layout = {
    breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'System' }, { title: 'Roles', href: index().url }, { title: 'New role' }],
};
