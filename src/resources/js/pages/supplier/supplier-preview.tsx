import { Truck } from 'lucide-react';

import { Card, CardContent } from '@/components/ui/card';

import type { SupplierFormData } from './supplier-form';

/** Sticky aside: live summary of the record being built. */
export function SupplierPreview({ data, title }: { data: SupplierFormData; title?: string }) {
    return (
        <aside className="lg:sticky lg:top-20">
            <Card>
                <CardContent className="grid gap-3">
                    <div className="flex items-start gap-3">
                        <span className="flex size-10 shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary">
                            <Truck className="size-5" />
                        </span>
                        <div className="grid min-w-0 flex-1 gap-1">
                            <p className="truncate font-semibold">{title ?? (data.name || 'New supplier')}</p>
                        </div>
                    </div>
                    {data.contact_person && <p className="text-sm text-muted-foreground">{String(data.contact_person)}</p>}
                </CardContent>
            </Card>
        </aside>
    );
}
