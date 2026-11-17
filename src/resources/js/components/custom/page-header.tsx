import type { ReactNode } from 'react';

import { cn } from '@/lib/utils';

/**
 * Index-page heading: serif title, one-line description (children), actions on the right.
 * Stacks on phones, actions wrap under the text.
 */
export function PageHeader({
    title,
    actions,
    children,
    className,
}: {
    title: string;
    actions?: ReactNode;
    children?: ReactNode;
    className?: string;
}) {
    return (
        <header className={cn('flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between', className)}>
            <div className="grid min-w-0 gap-1">
                <h1 className="font-display text-2xl leading-tight tracking-tight md:text-[1.75rem]">{title}</h1>
                {children && <p className="max-w-2xl text-sm text-muted-foreground">{children}</p>}
            </div>
            {actions && <div className="shrink-0">{actions}</div>}
        </header>
    );
}
