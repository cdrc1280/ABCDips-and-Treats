import { router } from '@inertiajs/react';
import { useEffect, useRef, useState, type ReactNode } from 'react';

import { ConfirmDialog } from '@/components/custom/confirm-dialog';

/** Blocks Inertia navigation and tab close while `dirty`, with a confirm dialog. */
export function UnsavedChangesGuard({ dirty, children }: { dirty: boolean; children: ReactNode }) {
    const [pending, setPending] = useState<string | null>(null);
    const bypass = useRef(false);

    useEffect(() => {
        if (!dirty) return;
        const onBeforeUnload = (event: BeforeUnloadEvent) => event.preventDefault();
        window.addEventListener('beforeunload', onBeforeUnload);
        const off = router.on('before', (event) => {
            const visit = event.detail.visit;
            if (bypass.current || visit.method !== 'get' || visit.only.length > 0 || visit.preserveState === true) return;
            setPending(visit.url.toString());
            return false;
        });
        return () => {
            window.removeEventListener('beforeunload', onBeforeUnload);
            off();
        };
    }, [dirty]);

    return (
        <>
            {children}
            <ConfirmDialog
                open={pending !== null}
                onOpenChange={(open) => !open && setPending(null)}
                title="Discard unsaved changes?"
                description="You have changes that haven't been saved. Leaving this page will discard them."
                confirmLabel="Discard and leave"
                onConfirm={() => {
                    const url = pending;
                    setPending(null);
                    bypass.current = true;
                    if (url) router.visit(url, { onFinish: () => (bypass.current = false) });
                }}
            />
        </>
    );
}
