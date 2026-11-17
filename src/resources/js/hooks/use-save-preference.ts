import { router } from '@inertiajs/react';
import { useRef } from 'react';

import { tableColumns } from '@/routes/admin/preferences';
import type { TablePreferences } from '@/types';

/** Persists a table's column layout (hidden / order / widths / pinned) per user, debounced. */
export function useSavePreference(table: string) {
    const timer = useRef<ReturnType<typeof setTimeout> | null>(null);

    return (columns: TablePreferences) => {
        if (timer.current) clearTimeout(timer.current);
        timer.current = setTimeout(() => {
            router.put(tableColumns(table).url, { ...columns }, { preserveState: true, preserveScroll: true, only: [], async: true });
        }, 500);
    };
}
