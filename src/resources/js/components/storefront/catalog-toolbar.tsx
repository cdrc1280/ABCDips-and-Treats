import { router } from '@inertiajs/react';
import { Search, X } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';

import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { cn } from '@/lib/utils';
import type { StorefrontCategory } from '@/types';

export interface CatalogFilters {
    search: string | null;
    category: string | null;
    sort: string;
}

const SORTS = [
    { value: 'latest', label: 'Newest first' },
    { value: 'popular', label: 'Most popular' },
    { value: 'rating_high', label: 'Top rated' },
    { value: 'price_low', label: 'Price: low to high' },
    { value: 'price_high', label: 'Price: high to low' },
    { value: 'name_asc', label: 'Name A–Z' },
];

/** Search (debounced), category pills and sort; every change is a GET visit so the URL stays shareable. */
export function CatalogToolbar({ url, filters, categories }: { url: string; filters: CatalogFilters; categories?: StorefrontCategory[] }) {
    const [search, setSearch] = useState(filters.search ?? '');
    const first = useRef(true);

    const visit = (next: Partial<CatalogFilters>) => {
        const query = { ...filters, ...next };
        router.get(
            url,
            Object.fromEntries(
                Object.entries({
                    search: query.search || undefined,
                    category: categories ? query.category || undefined : undefined,
                    sort: query.sort !== 'latest' ? query.sort : undefined,
                }).filter(([, v]) => v),
            ),
            { preserveState: true, preserveScroll: true, replace: true, reset: ['products'] },
        );
    };

    useEffect(() => {
        if (first.current) {
            first.current = false;
            return;
        }
        const id = window.setTimeout(() => visit({ search }), 350);
        return () => window.clearTimeout(id);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search]);

    const pill = (active: boolean) =>
        cn(
            'shrink-0 rounded-full px-4 py-2 text-xs font-bold whitespace-nowrap transition-all sm:text-sm',
            active ? 'bg-primary text-primary-foreground shadow-sm' : 'border border-primary/20 bg-background/80 text-primary hover:bg-highlight/25',
        );

    return (
        <div className="glass-surface sticky top-18 z-30 mx-auto mb-8 flex max-w-7xl flex-col gap-3 rounded-2xl border border-primary/20 p-3 shadow-sm lg:flex-row lg:items-center">
            <label className="relative block lg:w-72">
                <span className="sr-only">Search the menu</span>
                <Search className="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                <input
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    placeholder="Search the menu"
                    className="w-full rounded-xl border border-primary/30 bg-card py-2.5 pr-9 pl-9 text-sm text-foreground focus:ring-2 focus:ring-primary focus:outline-none"
                />
                {search && (
                    <button
                        type="button"
                        aria-label="Clear search"
                        onClick={() => setSearch('')}
                        className="absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground"
                    >
                        <X className="size-4" />
                    </button>
                )}
            </label>
            {categories && (
                <div className="no-scrollbar flex flex-1 gap-2 overflow-x-auto">
                    <button type="button" className={pill(!filters.category)} onClick={() => visit({ category: null })}>
                        All
                    </button>
                    {categories.map((category) => (
                        <button
                            key={category.slug}
                            type="button"
                            className={pill(filters.category === category.slug)}
                            onClick={() => visit({ category: category.slug })}
                        >
                            {category.name}
                        </button>
                    ))}
                </div>
            )}
            <Select value={filters.sort} onValueChange={(sort) => visit({ sort })}>
                <SelectTrigger className="w-full bg-card lg:ml-auto lg:w-48" aria-label="Sort products">
                    <SelectValue />
                </SelectTrigger>
                <SelectContent>
                    {SORTS.map((sort) => (
                        <SelectItem key={sort.value} value={sort.value}>
                            {sort.label}
                        </SelectItem>
                    ))}
                </SelectContent>
            </Select>
        </div>
    );
}
