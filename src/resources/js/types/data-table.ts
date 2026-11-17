import type { ReactNode } from 'react';

export interface ColumnDef<T> {
    /** Unique key; also the sort field when `sortable` is true (supports "relation.column"). */
    key: string;
    header: string;
    cell: (row: T, index: number) => ReactNode;
    sortable?: boolean;
    /** Sort key when it differs from `key`. */
    sortField?: string;
    align?: 'left' | 'right' | 'center';
    /** 1 = always visible, 3 = first to hide on narrow screens. */
    priorityLevel?: 1 | 2 | 3;
    width?: number;
    hideable?: boolean;
    className?: string;
}

export interface TablePreferences {
    hidden?: string[];
    order?: string[];
    widths?: Record<string, number>;
    pinned?: string[];
}
