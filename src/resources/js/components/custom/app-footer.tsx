import { createContext, useContext, type ReactNode } from 'react';
import { createPortal } from 'react-dom';

/** The sticky footer element of AppLayout, provided via a callback ref (no effects). */
export const FooterSlotContext = createContext<HTMLElement | null>(null);

/**
 * Renders into the sticky footer slot of AppLayout (the save bar on form pages).
 * Pages place <AppFooter> anywhere; it portals to the bottom of the layout.
 */
export function AppFooter({ children }: { children: ReactNode }) {
    const target = useContext(FooterSlotContext);
    if (!target) return null;
    return createPortal(
        <div className="flex flex-col-reverse gap-2 border-t bg-background/90 px-4 py-3 backdrop-blur-md sm:flex-row sm:items-center sm:justify-end md:rounded-b-xl [&>*]:w-full sm:[&>*]:w-auto">
            {children}
        </div>,
        target,
    );
}
