import { Head, Link, router } from '@inertiajs/react';

import { CatalogToolbar, type CatalogFilters } from '@/components/storefront/catalog-toolbar';
import { PageHeader } from '@/components/storefront/page-header';
import { ProductScroll } from '@/components/storefront/product-scroll';
import { cn } from '@/lib/utils';
import { shop } from '@/routes';
import type { ProductCardData, StorefrontCategory } from '@/types';

interface Props {
    filters: CatalogFilters;
    products: { data: ProductCardData[] };
    categories: StorefrontCategory[];
}

/** The shop's menu board: categories with counts, chalk-on-cocoa. Wide screens only; phones use the toolbar pills. */
function MenuBoard({ categories, active }: { categories: StorefrontCategory[]; active?: string | null }) {
    if (categories.length === 0) return null;
    return (
        <nav
            aria-label="Menu board"
            className="hidden w-[30rem] rounded-2xl border-4 border-sidebar-border bg-sidebar p-5 text-sidebar-foreground shadow-md lg:block"
        >
            <p className="mb-2 font-display text-lg text-sidebar-primary">On the board today</p>
            <ul className="grid grid-cols-2 gap-x-6 gap-y-1.5 text-sm">
                {categories.map((category) => (
                    <li key={category.slug}>
                        <Link
                            href={shop({ query: { category: category.slug } }).url}
                            preserveScroll
                            className={cn(
                                'flex items-baseline gap-2 hover:text-sidebar-primary',
                                active === category.slug && 'font-semibold text-sidebar-primary',
                            )}
                        >
                            <span className="truncate">{category.name}</span>
                            <span aria-hidden className="min-w-4 flex-1 border-b border-dotted border-sidebar-foreground/30" />
                            <span className="font-mono text-xs tabular-nums">{category.products_count}</span>
                        </Link>
                    </li>
                ))}
            </ul>
        </nav>
    );
}

export default function Shop({ filters, products, categories }: Props) {
    const active = categories.find((c) => c.slug === filters.category);

    return (
        <>
            <Head title={active ? `${active.name} · Menu` : 'Menu'} />
            <div className="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
                <PageHeader
                    title={active?.name ?? 'The menu'}
                    subtitle={active?.description ?? 'Banana bread, brownies, cookies, cheesecakes and cakes. Tap a treat to see sizes and flavours.'}
                    aside={<MenuBoard categories={categories} active={filters.category} />}
                />
                <CatalogToolbar url={shop().url} filters={filters} categories={categories} />
                <ProductScroll products={products} onReset={() => router.get(shop().url)} />
            </div>
        </>
    );
}
