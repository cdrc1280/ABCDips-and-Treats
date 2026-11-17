import type { ReactNode } from 'react';

/**
 * Storefront page title: left-aligned serif heading and an optional one-line subtitle.
 * `aside` holds the page's one distinct element (menu board, receipt stub, ticket…), right of the title on wide screens.
 */
export function PageHeader({
    title,
    subtitle,
    aside,
    children,
}: {
    title: string;
    subtitle?: string | null;
    aside?: ReactNode;
    children?: ReactNode;
}) {
    return (
        <header className="flex flex-col gap-5 pt-8 pb-8 sm:flex-row sm:items-end sm:justify-between sm:pt-12">
            <div className="max-w-2xl min-w-0">
                <h1 className="font-display text-3xl leading-tight text-balance text-foreground sm:text-4xl">{title}</h1>
                {subtitle && <p className="mt-2 text-sm leading-relaxed text-muted-foreground sm:text-base">{subtitle}</p>}
                {children}
            </div>
            {aside && <div className="shrink-0">{aside}</div>}
        </header>
    );
}

export function EmptyState({ icon, title, description, action }: { icon?: ReactNode; title: string; description?: string; action?: ReactNode }) {
    return (
        <div className="flex flex-col items-center rounded-2xl border border-dashed px-6 py-16 text-center">
            {icon && <div className="mb-4 flex size-14 items-center justify-center rounded-full bg-highlight/25 text-primary">{icon}</div>}
            <h3 className="font-display text-xl">{title}</h3>
            {description && <p className="mt-1 max-w-sm text-sm text-muted-foreground">{description}</p>}
            {action && <div className="mt-6">{action}</div>}
        </div>
    );
}
