import type { ComponentProps, ReactNode } from 'react';

import { Label } from '@/components/ui/label';
import { useRequired } from '@/hooks/use-required';
import { cn } from '@/lib/utils';

/** Field → FieldLabel (+ <Required/>) → input → FieldDescription → InputError. */
export function Field({ className, ...props }: ComponentProps<'div'>) {
    return <div className={cn('grid content-start gap-1.5', className)} {...props} />;
}

export function Required() {
    return (
        <span aria-hidden className="text-destructive">
            *
        </span>
    );
}

/** Label with an asterisk when the backend Data class marks `name` required. */
export function FieldLabel({ name, htmlFor, children, required }: { name?: string; htmlFor?: string; children: ReactNode; required?: boolean }) {
    const isRequired = useRequired();
    const show = required ?? (name ? isRequired(name) : false);
    return (
        <Label htmlFor={htmlFor ?? name} className="gap-1">
            {children}
            {show && <Required />}
        </Label>
    );
}

export function FieldDescription({ className, ...props }: ComponentProps<'p'>) {
    return <p className={cn('text-xs text-muted-foreground', className)} {...props} />;
}

export function InputError({ message, className }: { message?: string; className?: string }) {
    if (!message) return null;
    return (
        <p role="alert" className={cn('text-xs font-medium text-destructive', className)}>
            {message}
        </p>
    );
}

/** Field grid used inside every FormSection. */
export const fieldGrid = 'grid grid-cols-1 gap-x-5 gap-y-4 sm:grid-cols-2 2xl:grid-cols-3';
