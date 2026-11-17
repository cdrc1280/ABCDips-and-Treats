import { Lightbulb } from 'lucide-react';

import { Card, CardContent } from '@/components/ui/card';
import { StatusBadge, toneFor } from '@/renderers/cells/badges';

import type { SuggestionFormData } from './suggestion-form';

/** Sticky aside: live summary of the record being built. */
export function SuggestionPreview({ data, title }: { data: SuggestionFormData; title?: string }) {
    return (
        <aside className="lg:sticky lg:top-20">
            <Card>
                <CardContent className="grid gap-3">
                    <div className="flex items-start gap-3">
                        <span className="flex size-10 shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary">
                            <Lightbulb className="size-5" />
                        </span>
                        <div className="grid min-w-0 flex-1 gap-1">
                            <p className="truncate font-semibold">{title ?? (data.subject || 'New suggestion')}</p>
                            <StatusBadge label={data.status} tone={toneFor(data.status)} />
                        </div>
                    </div>
                    {data.name && <p className="text-sm text-muted-foreground">{String(data.name)}</p>}
                </CardContent>
            </Card>
        </aside>
    );
}
