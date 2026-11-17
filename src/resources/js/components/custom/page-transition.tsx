import type { ReactNode } from 'react';

/** Short page fade; respects reduced motion. Keyed by URL in the layout so it replays per visit. */
export function PageTransition({ children }: { children: ReactNode }) {
    return <div className="animate-in duration-300 ease-out fade-in-0 motion-reduce:animate-none">{children}</div>;
}
