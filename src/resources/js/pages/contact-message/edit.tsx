import { Deferred, Head, router, useForm } from '@inertiajs/react';
import { Mail, Loader2, MoreVertical, Trash2 } from 'lucide-react';
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
import { ContactMessagePermission } from '@/lib/permissions';
import { dashboard } from '@/routes/admin';
import { destroy, edit, index, update } from '@/routes/admin/contact-messages';
import type { ContactMessageRecord, Option } from '@/types';

import { ContactMessageForm, type ContactMessageFormData } from './contact-message-form';
import { ContactMessagePreview } from './contact-message-preview';

type Props = { record: ContactMessageRecord; switcher?: Option<string>[] };

export default function ContactMessageEdit({ record, switcher }: Props) {
    const { can } = usePermissions();
    const [confirmDelete, setConfirmDelete] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const form = useForm<ContactMessageFormData>({
        name: record.name === null || record.name === undefined ? '' : String(record.name),
        email: record.email === null || record.email === undefined ? '' : String(record.email),
        phone: record.phone === null || record.phone === undefined ? '' : String(record.phone),
        subject: record.subject === null || record.subject === undefined ? '' : String(record.subject),
        message: record.message === null || record.message === undefined ? '' : String(record.message),
        status: record.status === null || record.status === undefined ? '' : String(record.status),
        admin_notes: record.admin_notes === null || record.admin_notes === undefined ? '' : String(record.admin_notes),
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
                    searchPlaceholder="Jump to contact message…"
                    onChange={(id) => id && id !== record.id && router.visit(edit(id).url)}
                />
            </Deferred>
            {can(ContactMessagePermission.ContactMessagesDelete) && (
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="outline" size="icon" aria-label="More actions">
                            <MoreVertical />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem variant="destructive" onSelect={() => setConfirmDelete(true)}>
                            <Trash2 /> Delete contact message
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            )}
        </div>
    );

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title={`Edit ${record.subject}`} />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Contact message list"
                    icon={Mail}
                    title={String(record.subject)}
                    description={''}
                    actions={headerActions}
                />
                <div className={tabGridClass}>
                    <ContactMessagePreview data={form.data} title={String(record.subject)} />
                    <div className="grid min-w-0 grid-cols-[minmax(0,1fr)] content-start gap-4">
                        <ContactMessageForm data={form.data} setData={form.setData} errors={form.errors} options={{}} />
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
                title={`Delete ${record.subject}?`}
                description="This contact message will be removed."
                confirmLabel="Delete contact message"
                processing={deleting}
                onConfirm={remove}
            />
        </UnsavedChangesGuard>
    );
}

ContactMessageEdit.layout = {
    breadcrumbs: [
        { title: 'Home', href: dashboard().url },
        { title: 'Engagement' },
        { title: 'Contact messages', href: index().url },
        { title: 'Edit' },
    ],
};
