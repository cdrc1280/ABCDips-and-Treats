import { router, usePage } from '@inertiajs/react';
import { useEffect } from 'react';
import { toast } from 'sonner';

import type { Toast } from '@/types';

function show(flash?: { toast?: Toast } | null) {
    const item = flash?.toast;
    if (!item?.message) return;
    const fn = { success: toast.success, error: toast.error, warning: toast.warning, info: toast.info }[item.type] ?? toast;
    fn(item.message);
}

/** Shows flashed `toast` props (Inertia::flash('toast', [...])) as sonner toasts. */
export function useFlashToast() {
    const page = usePage();

    useEffect(() => {
        show(page.flash as { toast?: Toast });
        return router.on('flash', (event) => show(event.detail.flash as { toast?: Toast }));
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);
}
