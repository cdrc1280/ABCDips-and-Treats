import { useCallback, useSyncExternalStore } from 'react';

/** Live `window.matchMedia(query).matches`, correct on the first render (no layout flash). */
export function useMediaQuery(query: string): boolean {
    const subscribe = useCallback(
        (notify: () => void) => {
            const list = window.matchMedia(query);
            list.addEventListener('change', notify);
            return () => list.removeEventListener('change', notify);
        },
        [query],
    );

    return useSyncExternalStore(
        subscribe,
        () => window.matchMedia(query).matches,
        () => false,
    );
}

/** Below Tailwind's `sm` breakpoint (640px). */
export const PHONE_QUERY = '(max-width: 639.98px)';
