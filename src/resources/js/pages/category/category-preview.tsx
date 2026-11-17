import { FolderTree } from 'lucide-react';
import { useMemo } from 'react';

import { Card, CardContent } from '@/components/ui/card';
import { BooleanBadge, CountChip } from '@/renderers/cells/badges';

import type { CategoryFormData } from './category-form';

/** Sticky aside: live preview of the category tile being built. */
export function CategoryPreview({
    data,
    currentImageUrl,
    productsCount,
}: {
    data: CategoryFormData;
    currentImageUrl?: string | null;
    productsCount?: number;
}) {
    const objectUrl = useMemo(() => (data.image ? URL.createObjectURL(data.image) : null), [data.image]);
    const image = objectUrl ?? (data.remove_image ? null : currentImageUrl);

    return (
        <aside className="lg:sticky lg:top-20">
            <Card className="overflow-hidden py-0">
                <div className="flex aspect-[4/3] items-center justify-center bg-muted">
                    {image ? <img src={image} alt="" className="size-full object-cover" /> : <FolderTree className="size-10 text-muted-foreground" />}
                </div>
                <CardContent className="grid gap-2 py-4">
                    <div className="flex items-start justify-between gap-2">
                        <p className="font-semibold">{data.name || 'New category'}</p>
                        <BooleanBadge value={data.is_active} yes="Visible" no="Hidden" />
                    </div>
                    <p className="text-xs text-muted-foreground">/category/{data.slug || '…'}</p>
                    {data.description && <p className="line-clamp-3 text-sm text-muted-foreground">{data.description}</p>}
                    {productsCount !== undefined && (
                        <div className="text-xs text-muted-foreground">
                            <CountChip value={productsCount} label={productsCount === 1 ? 'product' : 'products'} />
                        </div>
                    )}
                </CardContent>
            </Card>
        </aside>
    );
}
