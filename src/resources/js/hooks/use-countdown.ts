import { useEffect, useState } from 'react';

/** Remaining time until `until` (ISO string), ticking every 30 s. */
export function useCountdown(until: string | null | undefined) {
    const target = until ? new Date(until).getTime() : null;
    const [now, setNow] = useState(() => Date.now());

    useEffect(() => {
        if (!target) return;
        const id = window.setInterval(() => setNow(Date.now()), 30_000);
        return () => window.clearInterval(id);
    }, [target]);

    if (!target) return null;
    const ms = Math.max(0, target - now);
    const minutes = Math.floor(ms / 60_000);
    return {
        expired: ms === 0,
        nearExpiry: ms > 0 && ms < 24 * 3_600_000,
        days: Math.floor(minutes / 1440),
        hours: Math.floor((minutes % 1440) / 60),
        minutes: minutes % 60,
    };
}
