import { Newspaper } from 'lucide-react';

import { Card, CardContent } from '@/components/ui/card';
import { StatusBadge, toneFor } from '@/renderers/cells/badges';

import type { BlogPostFormData } from './blog-post-form';

/** Sticky aside: live summary of the record being built. */
export function BlogPostPreview({ data, title }: { data: BlogPostFormData; title?: string }) {
    return (
        <aside className="lg:sticky lg:top-20">
            <Card>
                <CardContent className="grid gap-3">
                    <div className="flex items-start gap-3">
                        <span className="flex size-10 shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary">
                            <Newspaper className="size-5" />
                        </span>
                        <div className="grid min-w-0 flex-1 gap-1">
                            <p className="truncate font-semibold">{title ?? (data.title || 'New blog post')}</p>
                            <StatusBadge label={data.status} tone={toneFor(data.status)} />
                        </div>
                    </div>
                    {data.category && <p className="text-sm text-muted-foreground">{String(data.category)}</p>}
                </CardContent>
            </Card>
        </aside>
    );
}
