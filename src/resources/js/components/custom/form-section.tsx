import { ChevronDown, type LucideIcon } from 'lucide-react';
import { useId, useState, type ReactNode } from 'react';

import { cn } from '@/lib/utils';

interface Props {
    icon: LucideIcon;
    title: string;
    description?: string;
    children: ReactNode;
    defaultOpen?: boolean;
    /** Field names in this section; the section opens itself when one of them has an error. */
    fields?: string[];
    errors?: Record<string, string | undefined>;
    className?: string;
}

/** Collapsible form block: icon tile, semibold title, one-line description, chevron. */
export function FormSection({ icon: Icon, title, description, children, defaultOpen = true, fields = [], errors = {}, className }: Props) {
    const [open, setOpen] = useState(defaultOpen);
    const contentId = useId();
    const hasError = fields.some((field) => Boolean(errors[field]) || Object.keys(errors).some((key) => key.startsWith(`${field}.`)));

    // A section holding a validation error is always shown open.
    const isOpen = open || hasError;

    return (
        <section className={cn('border-t first:border-t-0', className)}>
            <button
                type="button"
                aria-expanded={isOpen}
                aria-controls={contentId}
                onClick={() => setOpen(!isOpen)}
                className="flex w-full items-center gap-3 px-4 py-4 text-left sm:px-6"
            >
                <span
                    className={cn(
                        'flex size-8 shrink-0 items-center justify-center rounded-md bg-muted text-muted-foreground',
                        hasError && 'bg-destructive/10 text-destructive',
                    )}
                >
                    <Icon className="size-4" />
                </span>
                <span className="grid flex-1">
                    <span className="text-sm font-semibold">{title}</span>
                    {description && <span className="text-xs text-muted-foreground">{description}</span>}
                </span>
                <ChevronDown className={cn('size-4 text-muted-foreground transition-transform duration-200', isOpen && 'rotate-180')} />
            </button>
            <div id={contentId} hidden={!isOpen} className="px-4 pb-6 sm:px-6">
                {children}
            </div>
        </section>
    );
}
