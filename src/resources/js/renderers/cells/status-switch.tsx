import { router } from '@inertiajs/react';
import { useState } from 'react';

import { Switch } from '@/components/ui/switch';
import { usePermissions } from '@/hooks/use-permissions';

/** Inline on/off toggle that PATCHes `url` with { [field]: value }. */
export function StatusSwitch({
    checked,
    url,
    field = 'is_active',
    permission,
    label,
}: {
    checked: boolean;
    url: string;
    field?: string;
    permission?: string;
    label: string;
}) {
    const { can } = usePermissions();
    const [value, setValue] = useState(checked);
    const [busy, setBusy] = useState(false);

    return (
        <Switch
            checked={value}
            aria-label={label}
            disabled={busy || !can(permission)}
            onCheckedChange={(next) => {
                setValue(next);
                router.patch(
                    url,
                    { [field]: next },
                    {
                        preserveScroll: true,
                        preserveState: true,
                        onStart: () => setBusy(true),
                        onFinish: () => setBusy(false),
                        onError: () => setValue(!next),
                    },
                );
            }}
        />
    );
}
