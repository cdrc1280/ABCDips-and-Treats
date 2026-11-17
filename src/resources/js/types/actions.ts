import type { LucideIcon } from 'lucide-react';

/** A header button on an index page (schemas/{thing}/actions.ts). */
export interface PageAction {
    label: string;
    icon?: LucideIcon;
    href?: string;
    onClick?: () => void;
    variant?: 'default' | 'outline' | 'secondary' | 'ghost' | 'destructive';
    /** Hidden when the user lacks it. */
    permission?: string;
    /** Plain <a> (downloads, exports) instead of an Inertia visit. */
    external?: boolean;
}

/** A row action in the kebab menu of the Actions column. */
export interface RowAction<T> {
    label: string;
    icon?: LucideIcon;
    href?: (row: T) => string;
    onClick?: (row: T) => void;
    /** Inertia method visit (e.g. delete) — use with href. */
    method?: 'post' | 'put' | 'patch' | 'delete';
    permission?: string;
    destructive?: boolean;
    isWithConfirmation?: boolean;
    confirmTitle?: string;
    confirmDescription?: string | ((row: T) => string);
    /** Hide per row (e.g. resource flags like has_orders). */
    hidden?: (row: T) => boolean;
    external?: boolean;
    /** Ask for one value before submitting (sent as { [field]: value }). */
    prompt?: {
        field: string;
        label: string;
        type?: 'number' | 'text' | 'select';
        min?: number;
        step?: string;
        options?: { label: string; value: string }[] | ((row: T) => { label: string; value: string }[]);
        description?: string | ((row: T) => string);
    };
}
