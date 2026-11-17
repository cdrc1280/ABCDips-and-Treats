import { ShieldCheck } from 'lucide-react';

import { Card, CardContent } from '@/components/ui/card';

import type { RoleFormData } from './role-form';

/** Sticky aside: live summary of the record being built. */
export function RolePreview({ data, title }: { data: RoleFormData; title?: string }) {
    return (
        <aside className="lg:sticky lg:top-20">
            <Card>
                <CardContent className="grid gap-3">
                    <div className="flex items-start gap-3">
                        <span className="flex size-10 shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary">
                            <ShieldCheck className="size-5" />
                        </span>
                        <div className="grid min-w-0 flex-1 gap-1">
                            <p className="truncate font-semibold">{title ?? (data.name || 'New role')}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </aside>
    );
}
