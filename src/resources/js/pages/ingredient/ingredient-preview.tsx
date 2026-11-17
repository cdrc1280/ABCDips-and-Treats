import { Wheat } from 'lucide-react';

import { Card, CardContent } from '@/components/ui/card';

import type { IngredientFormData } from './ingredient-form';

/** Sticky aside: live summary of the record being built. */
export function IngredientPreview({ data, title }: { data: IngredientFormData; title?: string }) {
    return (
        <aside className="lg:sticky lg:top-20">
            <Card>
                <CardContent className="grid gap-3">
                    <div className="flex items-start gap-3">
                        <span className="flex size-10 shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary">
                            <Wheat className="size-5" />
                        </span>
                        <div className="grid min-w-0 flex-1 gap-1">
                            <p className="truncate font-semibold">{title ?? (data.name || 'New ingredient')}</p>
                        </div>
                    </div>
                    {data.sku && <p className="text-sm text-muted-foreground">{String(data.sku)}</p>}
                    <div className="flex justify-between gap-2 text-sm">
                        <span className="text-muted-foreground">Cost per unit</span>
                        <span className="font-medium tabular-nums">
                            {data.item_unit && Number(data.item_unit) > 0 ? `₱${(Number(data.item_price) / Number(data.item_unit)).toFixed(4)}` : '—'}
                        </span>
                    </div>
                    <div className="flex justify-between gap-2 text-sm">
                        <span className="text-muted-foreground">In stock</span>
                        <span className="font-medium tabular-nums">{`${data.stock_qty || 0} ${data.unit}`}</span>
                    </div>
                </CardContent>
            </Card>
        </aside>
    );
}
