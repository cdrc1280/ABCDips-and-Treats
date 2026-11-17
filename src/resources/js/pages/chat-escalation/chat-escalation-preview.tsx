import { MessagesSquare } from 'lucide-react';

import { Card, CardContent } from '@/components/ui/card';
import { StatusBadge, toneFor } from '@/renderers/cells/badges';

import type { ChatEscalationFormData } from './chat-escalation-form';

/** Sticky aside: live summary of the record being built. */
export function ChatEscalationPreview({ data, title }: { data: ChatEscalationFormData; title?: string }) {
    return (
        <aside className="lg:sticky lg:top-20">
            <Card>
                <CardContent className="grid gap-3">
                    <div className="flex items-start gap-3">
                        <span className="flex size-10 shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary">
                            <MessagesSquare className="size-5" />
                        </span>
                        <div className="grid min-w-0 flex-1 gap-1">
                            <p className="truncate font-semibold">{title ?? (data.guest_name || 'New support escalation')}</p>
                            <StatusBadge label={data.status} tone={toneFor(data.status)} />
                        </div>
                    </div>
                    {data.guest_email && <p className="text-sm text-muted-foreground">{String(data.guest_email)}</p>}
                </CardContent>
            </Card>
        </aside>
    );
}
