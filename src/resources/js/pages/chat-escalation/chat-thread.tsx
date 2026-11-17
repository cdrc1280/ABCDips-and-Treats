import { router } from '@inertiajs/react';
import { Bot, Headset, Loader2, Send, User } from 'lucide-react';
import { useState, type FormEvent } from 'react';

import { InputError } from '@/components/custom/field';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Textarea } from '@/components/ui/textarea';
import { usePermissions } from '@/hooks/use-permissions';
import { ChatEscalationPermission } from '@/lib/permissions';
import { cn } from '@/lib/utils';
import { reply } from '@/routes/admin/chat-escalations';
import type { ChatEscalationRecord } from '@/types';

const who = {
    user: { label: 'Customer', icon: User, bubble: 'bg-muted', align: 'justify-start' },
    assistant: { label: 'AI assistant', icon: Bot, bubble: 'bg-secondary', align: 'justify-start' },
    admin: { label: 'Support team', icon: Headset, bubble: 'bg-primary text-primary-foreground', align: 'justify-end' },
} as const;

/** Hand-written panel on the escalation edit page: the conversation and a reply box. */
export function ChatThread({ record }: { record: ChatEscalationRecord }) {
    const { can } = usePermissions();
    const [message, setMessage] = useState('');
    const [error, setError] = useState<string | undefined>();
    const [sending, setSending] = useState(false);

    const send = (event: FormEvent) => {
        event.preventDefault();
        router.patch(
            reply(record.id).url,
            { message },
            {
                preserveScroll: true,
                onStart: () => setSending(true),
                onFinish: () => setSending(false),
                onError: (errors) => setError(errors.message),
                onSuccess: () => {
                    setMessage('');
                    setError(undefined);
                },
            },
        );
    };

    return (
        <Card>
            <CardHeader>
                <CardTitle className="text-base">Conversation</CardTitle>
            </CardHeader>
            <CardContent className="grid gap-4">
                <div className="grid max-h-[28rem] gap-3 overflow-y-auto pr-1">
                    {record.messages.length === 0 && <p className="text-sm text-muted-foreground">No messages.</p>}
                    {record.messages.map((msg, index) => {
                        const style = who[msg.role] ?? who.user;
                        const Icon = style.icon;
                        return (
                            <div key={index} className={cn('flex', style.align)}>
                                <div className={cn('max-w-[85%] rounded-2xl px-4 py-2.5 text-sm', style.bubble)}>
                                    <div className="mb-1 flex items-center gap-1.5 text-xs opacity-70">
                                        <Icon className="size-3.5" /> {style.label}
                                        {msg.time && <span>· {msg.time}</span>}
                                    </div>
                                    <p className="whitespace-pre-wrap">{msg.content}</p>
                                </div>
                            </div>
                        );
                    })}
                </div>
                {can(ChatEscalationPermission.ChatEscalationsEdit) && (
                    <form onSubmit={send} className="grid gap-2">
                        <Textarea
                            rows={3}
                            value={message}
                            onChange={(event) => setMessage(event.target.value)}
                            placeholder="Type a reply to the customer…"
                            aria-invalid={!!error}
                        />
                        <InputError message={error} />
                        <div className="flex justify-end">
                            <Button type="submit" size="sm" disabled={sending || message.trim() === ''}>
                                {sending ? <Loader2 className="animate-spin" /> : <Send />} Send reply
                            </Button>
                        </div>
                    </form>
                )}
            </CardContent>
        </Card>
    );
}
