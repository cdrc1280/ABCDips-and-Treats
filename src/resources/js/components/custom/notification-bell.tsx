import { Link, router, usePage, usePoll } from '@inertiajs/react';
import { formatDistanceToNow } from 'date-fns';
import { Bell, CheckCheck } from 'lucide-react';
import { useState } from 'react';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { ScrollArea } from '@/components/ui/scroll-area';
import { jsonRequest } from '@/lib/http';
import { cn } from '@/lib/utils';
import notifications from '@/routes/admin/notifications';

interface NotificationItem {
    id: string;
    title: string;
    body: string;
    url: string | null;
    read: boolean;
    created_at: string;
}

/** Back-office bell: unread count from the shared `notifications` prop, list fetched on open. */
export function NotificationBell() {
    const unread = usePage().props.notifications?.unread ?? 0;
    const [open, setOpen] = useState(false);
    const [items, setItems] = useState<NotificationItem[] | null>(null);
    usePoll(60_000, { only: ['notifications'] });

    const refreshCount = () => router.reload({ only: ['notifications'] });

    const load = () =>
        jsonRequest<{ items: NotificationItem[] }>(notifications.index().url)
            .then((data) => setItems(data.items))
            .catch(() => setItems([]));

    const markRead = (item: NotificationItem) => {
        if (item.read) return;
        setItems((list) => list?.map((n) => (n.id === item.id ? { ...n, read: true } : n)) ?? null);
        void jsonRequest(notifications.read(item.id).url, { method: 'POST' }).then(refreshCount, () => undefined);
    };

    const markAll = () => {
        setItems((list) => list?.map((n) => ({ ...n, read: true })) ?? null);
        void jsonRequest(notifications.readAll().url, { method: 'POST' }).then(refreshCount, () => undefined);
    };

    return (
        <Popover
            open={open}
            onOpenChange={(next) => {
                setOpen(next);
                if (next) void load();
            }}
        >
            <PopoverTrigger asChild>
                <Button variant="ghost" size="icon" className="relative" aria-label={unread ? `Notifications (${unread} unread)` : 'Notifications'}>
                    <Bell />
                    {unread > 0 && (
                        <Badge className="absolute -top-0.5 -right-0.5 h-4 min-w-4 rounded-full px-1 text-[10px] leading-none tabular-nums">
                            {unread > 99 ? '99+' : unread}
                        </Badge>
                    )}
                </Button>
            </PopoverTrigger>
            <PopoverContent align="end" className="w-80 p-0">
                <div className="flex items-center justify-between border-b px-3 py-2">
                    <p className="text-sm font-medium">Notifications</p>
                    <Button variant="ghost" size="sm" className="h-7 gap-1 text-xs" disabled={!unread} onClick={markAll}>
                        <CheckCheck className="size-3.5" /> Mark all read
                    </Button>
                </div>
                <ScrollArea className="max-h-96">
                    {items === null ? (
                        <p className="px-3 py-6 text-center text-sm text-muted-foreground">Loading…</p>
                    ) : items.length === 0 ? (
                        <p className="px-3 py-6 text-center text-sm text-muted-foreground">You're all caught up.</p>
                    ) : (
                        <ul className="divide-y">
                            {items.map((item) => {
                                const content = (
                                    <>
                                        <span className="flex items-start gap-2">
                                            <span
                                                className={cn('mt-1.5 size-2 shrink-0 rounded-full', item.read ? 'bg-transparent' : 'bg-primary')}
                                            />
                                            <span className="min-w-0 flex-1">
                                                <span className={cn('block text-sm', !item.read && 'font-medium')}>{item.title}</span>
                                                {item.body && (
                                                    <span className="mt-0.5 line-clamp-2 block text-xs text-muted-foreground">{item.body}</span>
                                                )}
                                                {item.created_at && (
                                                    <span className="mt-1 block text-[11px] text-muted-foreground">
                                                        {formatDistanceToNow(new Date(item.created_at), { addSuffix: true })}
                                                    </span>
                                                )}
                                            </span>
                                        </span>
                                    </>
                                );
                                const className =
                                    'block w-full px-3 py-2.5 text-left hover:bg-muted/60 focus-visible:bg-muted/60 focus-visible:outline-none';

                                return (
                                    <li key={item.id}>
                                        {item.url ? (
                                            <Link
                                                href={item.url}
                                                className={className}
                                                onClick={() => {
                                                    markRead(item);
                                                    setOpen(false);
                                                }}
                                            >
                                                {content}
                                            </Link>
                                        ) : (
                                            <button type="button" className={className} onClick={() => markRead(item)}>
                                                {content}
                                            </button>
                                        )}
                                    </li>
                                );
                            })}
                        </ul>
                    )}
                </ScrollArea>
            </PopoverContent>
        </Popover>
    );
}
