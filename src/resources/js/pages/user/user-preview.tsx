import { UserCog } from 'lucide-react';

import { Card, CardContent } from '@/components/ui/card';

import type { UserFormData } from './user-form';

/** Sticky aside: live summary of the record being built. */
export function UserPreview({ data, title }: { data: UserFormData; title?: string }) {
    return (
        <aside className="lg:sticky lg:top-20">
            <Card>
                <CardContent className="grid gap-3">
                    <div className="flex items-start gap-3">
                        <span className="flex size-10 shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary">
                            <UserCog className="size-5" />
                        </span>
                        <div className="grid min-w-0 flex-1 gap-1">
                            <p className="truncate font-semibold">{title ?? (data.name || 'New staff account')}</p>
                        </div>
                    </div>
                    {data.email && <p className="text-sm text-muted-foreground">{String(data.email)}</p>}
                </CardContent>
            </Card>
        </aside>
    );
}
