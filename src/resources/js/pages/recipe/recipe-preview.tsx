import { ChefHat } from 'lucide-react';

import { Card, CardContent } from '@/components/ui/card';

import type { RecipeFormData } from './recipe-form';

/** Sticky aside: live summary of the record being built. */
export function RecipePreview({ data, title }: { data: RecipeFormData; title?: string }) {
    return (
        <aside className="lg:sticky lg:top-20">
            <Card>
                <CardContent className="grid gap-3">
                    <div className="flex items-start gap-3">
                        <span className="flex size-10 shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary">
                            <ChefHat className="size-5" />
                        </span>
                        <div className="grid min-w-0 flex-1 gap-1">
                            <p className="truncate font-semibold">{title ?? (data.name || 'New recipe')}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </aside>
    );
}
