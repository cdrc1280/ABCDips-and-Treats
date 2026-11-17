import { Deferred, Head, router, useForm } from '@inertiajs/react';
import { MessagesSquare, Loader2, MoreVertical, Trash2 } from 'lucide-react';
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
import { ChatEscalationPermission } from '@/lib/permissions';
import { dashboard } from '@/routes/admin';
import { destroy, edit, index, update } from '@/routes/admin/chat-escalations';
import type { ChatEscalationRecord, Option } from '@/types';

import { ChatEscalationForm, type ChatEscalationFormData } from './chat-escalation-form';
import { ChatEscalationPreview } from './chat-escalation-preview';
import { ChatThread } from './chat-thread';

type Props = { record: ChatEscalationRecord; switcher?: Option<string>[] };

export default function ChatEscalationEdit({ record, switcher }: Props) {
    const { can } = usePermissions();
    const [confirmDelete, setConfirmDelete] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const form = useForm<ChatEscalationFormData>({
        guest_name: record.guest_name === null || record.guest_name === undefined ? '' : String(record.guest_name),
        guest_email: record.guest_email === null || record.guest_email === undefined ? '' : String(record.guest_email),
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
                    searchPlaceholder="Jump to support escalation…"
                    onChange={(id) => id && id !== record.id && router.visit(edit(id).url)}
                />
            </Deferred>
            {can(ChatEscalationPermission.ChatEscalationsDelete) && (
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="outline" size="icon" aria-label="More actions">
                            <MoreVertical />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem variant="destructive" onSelect={() => setConfirmDelete(true)}>
                            <Trash2 /> Delete support escalation
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            )}
        </div>
    );

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title={`Edit ${record.guest_name}`} />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Support escalation list"
                    icon={MessagesSquare}
                    title={String(record.guest_name)}
                    description={''}
                    actions={headerActions}
                />
                <div className={tabGridClass}>
                    <ChatEscalationPreview data={form.data} title={String(record.guest_name)} />
                    <div className="grid min-w-0 grid-cols-[minmax(0,1fr)] content-start gap-4">
                        <ChatEscalationForm data={form.data} setData={form.setData} errors={form.errors} options={{}} />
                        <ChatThread record={record} />
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
                title={`Delete ${record.guest_name}?`}
                description="This support escalation will be removed."
                confirmLabel="Delete support escalation"
                processing={deleting}
                onConfirm={remove}
            />
        </UnsavedChangesGuard>
    );
}

ChatEscalationEdit.layout = {
    breadcrumbs: [
        { title: 'Home', href: dashboard().url },
        { title: 'Engagement' },
        { title: 'Support escalations', href: index().url },
        { title: 'Edit' },
    ],
};
