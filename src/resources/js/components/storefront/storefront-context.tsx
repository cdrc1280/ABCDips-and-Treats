import { router } from '@inertiajs/react';
import type { CartLine } from '@/types';
import { createContext, useCallback, useContext, useEffect, useMemo, useState, type ReactNode } from 'react';

interface StorefrontUi {
    cartOpen: boolean;
    setCartOpen: (open: boolean) => void;
    quickView: QuickViewTarget | null;
    openQuickView: (slug: string | null, editLine?: CartLine) => void;
}

export interface QuickViewTarget {
    slug: string;
    /** Set when re-opening a basket line to change its options. */
    editLine?: CartLine;
}

const StorefrontUiContext = createContext<StorefrontUi | null>(null);

/** Drawer / quick-view state shared by the storefront shell. The server opens the drawer by flashing `cart_drawer`. */
export function StorefrontUiProvider({ children }: { children: ReactNode }) {
    const [cartOpen, setCartOpen] = useState(false);
    const [quickView, setQuickView] = useState<QuickViewTarget | null>(null);
    const openQuickView = useCallback((slug: string | null, editLine?: CartLine) => setQuickView(slug ? { slug, editLine } : null), []);

    useEffect(
        () =>
            router.on('flash', (event) => {
                if ((event.detail.flash as { cart_drawer?: boolean }).cart_drawer) {
                    setQuickView(null);
                    setCartOpen(true);
                }
            }),
        [],
    );

    const value = useMemo(() => ({ cartOpen, setCartOpen, quickView, openQuickView }), [cartOpen, quickView, openQuickView]);

    return <StorefrontUiContext.Provider value={value}>{children}</StorefrontUiContext.Provider>;
}

export function useStorefrontUi(): StorefrontUi {
    const context = useContext(StorefrontUiContext);
    if (!context) throw new Error('useStorefrontUi must be used inside StorefrontUiProvider');
    return context;
}
