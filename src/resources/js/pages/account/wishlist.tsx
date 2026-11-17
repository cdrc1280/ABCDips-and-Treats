import { Head, Link } from '@inertiajs/react';
import { Heart } from 'lucide-react';

import { AccountShell } from '@/components/storefront/account-shell';
import { EmptyState } from '@/components/storefront/page-header';
import { ProductGrid } from '@/components/storefront/product-card';
import { Button } from '@/components/ui/button';
import type { ProductCardData } from '@/types';

export default function Wishlist({ products }: { products: ProductCardData[] }) {
    return (
        <AccountShell>
            <Head title="My Wishlist" />
            <h1 className="mb-6 font-display text-2xl text-foreground">My Wishlist</h1>
            {products.length ? (
                <ProductGrid products={products} className="xl:grid-cols-3" />
            ) : (
                <EmptyState
                    icon={<Heart className="size-7" />}
                    title="Nothing saved yet"
                    description="Tap the heart on any treat to keep it here for later."
                    action={
                        <Button asChild>
                            <Link href="/shop">Browse treats</Link>
                        </Button>
                    }
                />
            )}
        </AccountShell>
    );
}
