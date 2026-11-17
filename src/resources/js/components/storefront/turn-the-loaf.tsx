import { Rotate3d } from 'lucide-react';
import { lazy, Suspense, useState } from 'react';

import { Dialog, DialogContent, DialogDescription, DialogTitle } from '@/components/ui/dialog';

const BananaBreadShowcase = lazy(() => import('@/components/storefront/banana-bread-showcase'));

/**
 * Opt-in 3D view for products that have a model (`has_3d`). Tablet and desktop only (hidden below md),
 * and nothing 3D is downloaded until the button is clicked.
 */
export function TurnTheLoaf({ productName }: { productName: string }) {
    const [open, setOpen] = useState(false);

    return (
        <>
            <button
                type="button"
                onClick={() => setOpen(true)}
                className="hidden items-center gap-2 rounded-xl border border-border bg-card px-4 py-2 text-sm font-semibold text-foreground transition-colors hover:border-primary/40 hover:bg-accent md:inline-flex"
            >
                <Rotate3d className="size-4 text-primary" /> Turn the loaf
            </button>
            <Dialog open={open} onOpenChange={setOpen}>
                <DialogContent className="p-4 sm:max-w-3xl sm:p-6">
                    <DialogTitle className="font-display text-xl font-normal">{productName}</DialogTitle>
                    <DialogDescription>A 3D model of the loaf. Drag to look at it from any side.</DialogDescription>
                    {open && (
                        <Suspense fallback={<div className="aspect-4/3 w-full animate-pulse rounded-2xl bg-sidebar motion-reduce:animate-none" />}>
                            <BananaBreadShowcase />
                        </Suspense>
                    )}
                </DialogContent>
            </Dialog>
        </>
    );
}
