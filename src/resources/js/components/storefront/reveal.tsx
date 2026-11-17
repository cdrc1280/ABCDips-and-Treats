import { useEffect, useRef, useState, type ReactNode } from 'react';

import { cn } from '@/lib/utils';

/** Fades/slides children in the first time they scroll into view (no-op under reduced motion). */
export function Reveal({ children, className, delay = 0 }: { children: ReactNode; className?: string; delay?: number }) {
    const ref = useRef<HTMLDivElement>(null);
    const [shown, setShown] = useState(false);

    useEffect(() => {
        const node = ref.current;
        if (!node) return;
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) {
            node.dataset.shown = 'true';
            requestAnimationFrame(() => setShown(true));
            return;
        }
        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    setShown(true);
                    observer.disconnect();
                }
            },
            { rootMargin: '0px 0px -10% 0px' },
        );
        observer.observe(node);
        return () => observer.disconnect();
    }, []);

    return (
        <div
            ref={ref}
            style={{ transitionDelay: `${delay}ms` }}
            className={cn('transition-all duration-300 ease-out', shown ? 'translate-y-0 opacity-100' : 'translate-y-3 opacity-0', className)}
        >
            {children}
        </div>
    );
}

/** Left-aligned section title with an optional link on the right. No eyebrow line. */
export function SectionHeading({ title, action }: { title: string; action?: ReactNode }) {
    return (
        <div className="mb-6 flex flex-wrap items-end justify-between gap-x-4 gap-y-2">
            <h2 className="font-display text-2xl text-foreground md:text-3xl">{title}</h2>
            {action}
        </div>
    );
}
