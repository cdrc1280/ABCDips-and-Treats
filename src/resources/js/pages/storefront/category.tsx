import { Head, Link, router } from '@inertiajs/react';
import { ChevronRight } from 'lucide-react';

import { CatalogToolbar, type CatalogFilters } from '@/components/storefront/catalog-toolbar';
import { PageHeader } from '@/components/storefront/page-header';
import { ProductScroll } from '@/components/storefront/product-scroll';
import { category as categoryRoute } from '@/routes';
import type { ProductCardData } from '@/types';

interface Props {
    category: { name: string; slug: string; description: string | null; image_url: string | null };
    filters: CatalogFilters;
    products: { data: ProductCardData[] };
}

export default function Category({ category, filters, products }: Props) {
    const url = categoryRoute(category.slug).url;

    return (
        <>
            <Head title={category.name}>
                <meta name="description" content={category.description ?? `${category.name} from ABCDips & Treats`} />
            </Head>
            <div className="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
                <nav aria-label="Breadcrumb" className="flex items-center gap-1 pt-6 text-xs font-semibold text-muted-foreground">
                    <Link href="/" className="hover:text-primary">
                        Home
                    </Link>
                    <ChevronRight className="size-3" />
                    <Link href="/shop" className="hover:text-primary">
                        Menu
                    </Link>
                    <ChevronRight className="size-3" />
                    <span className="text-primary">{category.name}</span>
                </nav>
                <PageHeader title={category.name} subtitle={category.description} />
                <CatalogToolbar url={url} filters={filters} />
                <ProductScroll products={products} onReset={() => router.get(url)} />
            </div>
        </>
    );
}
