import { Link } from '@inertiajs/react';
import { Loader2 } from 'lucide-react';
import { useEffect, useState } from 'react';

import { ProductGallery, ProductPurchasePanel } from '@/components/storefront/product-purchase-panel';
import { useStorefrontUi } from '@/components/storefront/storefront-context';
import { Dialog, DialogContent, DialogDescription, DialogTitle } from '@/components/ui/dialog';
import products from '@/routes/products';
import type { ProductDetailData } from '@/types';

/** Product dialog opened from listings and basket "Edit"; data comes from the read-only quick-view endpoint. */
export default function ProductQuickView() {
    const { quickView, openQuickView } = useStorefrontUi();
    const [product, setProduct] = useState<ProductDetailData | null>(null);
    const [failed, setFailed] = useState(false);
    const slug = quickView?.slug ?? null;
    const shown = product?.slug === slug ? product : null;

    useEffect(() => {
        if (!slug) return;
        const controller = new AbortController();
        fetch(products.quickView(slug).url, { headers: { Accept: 'application/json' }, signal: controller.signal })
            .then((res) => (res.ok ? res.json() : Promise.reject(res)))
            .then((data: ProductDetailData) => {
                setFailed(false);
                setProduct(data);
            })
            .catch((error: unknown) => {
                if (!(error instanceof DOMException && error.name === 'AbortError')) setFailed(true);
            });
        return () => controller.abort();
    }, [slug]);

    const close = () => openQuickView(null);

    return (
        <Dialog open={!!quickView} onOpenChange={(open) => !open && close()}>
            <DialogContent className="max-h-[92vh] overflow-y-auto p-4 sm:max-w-5xl sm:p-8">
                {shown ? (
                    <>
                        <DialogTitle className="sr-only">{shown.name}</DialogTitle>
                        <DialogDescription className="sr-only">Choose options and add {shown.name} to your basket.</DialogDescription>
                        <div className="grid gap-6 md:grid-cols-12 md:gap-8">
                            <div className="md:col-span-5">
                                <ProductGallery product={shown} />
                            </div>
                            <div className="md:col-span-7">
                                <ProductPurchasePanel product={shown} editLine={quickView?.editLine} onDone={close} />
                                <Link
                                    href={products.show(shown.slug).url}
                                    onClick={close}
                                    className="mt-4 inline-block text-sm font-bold text-primary hover:underline"
                                >
                                    View full details, reviews &amp; nutrition →
                                </Link>
                            </div>
                        </div>
                    </>
                ) : (
                    <div className="flex min-h-64 flex-col items-center justify-center gap-3">
                        <DialogTitle className="sr-only">Loading product</DialogTitle>
                        <DialogDescription className="sr-only">Product details are loading.</DialogDescription>
                        {failed ? (
                            <p className="text-sm text-muted-foreground">This product is no longer available.</p>
                        ) : (
                            <Loader2 className="size-8 animate-spin text-primary" />
                        )}
                    </div>
                )}
            </DialogContent>
        </Dialog>
    );
}
