import { Head, Link } from '@inertiajs/react';
import { Cake } from 'lucide-react';

import { EmptyState, PageHeader } from '@/components/storefront/page-header';
import { ProductGrid } from '@/components/storefront/product-card';
import type { ProductCardData } from '@/types';

interface Props {
    collection: { key: string; title: string; description: string };
    products: ProductCardData[];
}

/** Featured / Best Sellers / New Arrivals. */
export default function Collection({ collection, products }: Props) {
    return (
        <>
            <Head title={collection.title} />
            <div className="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
                <PageHeader title={collection.title} subtitle={collection.description} />
                {products.length ? (
                    <ProductGrid products={products} />
                ) : (
                    <EmptyState
                        icon={<Cake className="size-7" />}
                        title="Nothing here yet"
                        description="Nothing in this list right now. The full menu has everything we bake."
                        action={
                            <Link href="/shop" className="text-sm font-bold text-primary hover:underline">
                                Browse the menu →
                            </Link>
                        }
                    />
                )}
            </div>
        </>
    );
}
