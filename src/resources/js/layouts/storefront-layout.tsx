import { router } from '@inertiajs/react';
import { lazy, Suspense, useEffect, type ReactNode } from 'react';
import { toast } from 'sonner';

import CartDrawer from '@/components/storefront/cart-drawer';
import { StorefrontUiProvider, useStorefrontUi } from '@/components/storefront/storefront-context';
import StorefrontFooter from '@/components/storefront/storefront-footer';
import StorefrontNav from '@/components/storefront/storefront-nav';
import { Toaster } from '@/components/ui/sonner';
import { useCart } from '@/hooks/use-cart';
import { useFlashToast } from '@/hooks/use-flash-toast';

const ProductQuickView = lazy(() => import('@/components/storefront/product-quick-view'));
const AiChatWidget = lazy(() => import('@/components/storefront/ai-chat-widget'));

/** "Item removed — Undo" toast driven by the `cart_undo` flash. */
function useUndoRemoval() {
    const { restore } = useCart();
    useEffect(
        () =>
            router.on('flash', (event) => {
                const itemId = (event.detail.flash as { cart_undo?: string }).cart_undo;
                if (itemId) toast('Item removed from your basket.', { action: { label: 'Undo', onClick: () => restore(itemId) } });
            }),
        [restore],
    );
}

function Shell({ children }: { children: ReactNode }) {
    const { quickView } = useStorefrontUi();
    useFlashToast();
    useUndoRemoval();

    return (
        <div className="relative flex min-h-svh flex-col bg-background text-foreground">
            <StorefrontNav />
            <main className="relative z-10 flex-1 animate-in duration-500 fade-in">{children}</main>
            <StorefrontFooter />
            <CartDrawer />
            <Suspense fallback={null}>
                {quickView && <ProductQuickView />}
                <AiChatWidget />
            </Suspense>
            <Toaster position="top-center" richColors />
        </div>
    );
}

export default function StorefrontLayout({ children }: { children: ReactNode }) {
    return (
        <StorefrontUiProvider>
            <Shell>{children}</Shell>
        </StorefrontUiProvider>
    );
}
