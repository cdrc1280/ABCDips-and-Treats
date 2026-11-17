import { InfiniteScroll } from '@inertiajs/react';
import { Loader2, SearchX } from 'lucide-react';

import { EmptyState } from '@/components/storefront/page-header';
import { ProductCard } from '@/components/storefront/product-card';
import type { ProductCardData } from '@/types';

/** Infinite product grid bound to an Inertia::scroll() prop named "products". */
export function ProductScroll({ products, onReset }: { products: { data: ProductCardData[] }; onReset?: () => void }) {
    if (products.data.length === 0) {
        return (
            <EmptyState
                icon={<SearchX className="size-7" />}
                title="No pastries found"
                description="Try another search or category."
                action={
                    onReset && (
                        <button type="button" onClick={onReset} className="text-sm font-bold text-primary hover:underline">
                            Clear filters
                        </button>
                    )
                }
            />
        );
    }

    return (
        <InfiniteScroll
            data="products"
            buffer={400}
            className="grid grid-cols-2 gap-3 sm:gap-6 lg:grid-cols-3 xl:grid-cols-4"
            loading={
                <div className="col-span-full flex justify-center py-8">
                    <Loader2 className="size-6 animate-spin text-primary" />
                </div>
            }
        >
            {products.data.map((product) => (
                <ProductCard key={product.id} product={product} />
            ))}
        </InfiniteScroll>
    );
}
