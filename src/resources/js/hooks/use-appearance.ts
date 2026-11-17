import { useCallback, useEffect, useState } from 'react';

export type Appearance = 'light' | 'dark' | 'system';

const prefersDark = () => typeof window !== 'undefined' && window.matchMedia('(prefers-color-scheme: dark)').matches;

export function applyTheme(appearance: Appearance) {
    const isDark = appearance === 'dark' || (appearance === 'system' && prefersDark());
    document.documentElement.classList.toggle('dark', isDark);
    document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';
}

function readCookie(): Appearance {
    const match = document.cookie.match(/(?:^|; )appearance=(light|dark|system)/);
    return (match?.[1] as Appearance) ?? 'system';
}

/** Light/dark/system theme, stored in the `appearance` cookie (read server-side by HandleAppearance). */
export function useAppearance() {
    const [appearance, setAppearance] = useState<Appearance>(() => (typeof document === 'undefined' ? 'system' : readCookie()));

    const update = useCallback((mode: Appearance) => {
        setAppearance(mode);
        document.cookie = `appearance=${mode};path=/;max-age=${60 * 60 * 24 * 365};SameSite=Lax`;
        applyTheme(mode);
    }, []);

    useEffect(() => {
        const media = window.matchMedia('(prefers-color-scheme: dark)');
        const onChange = () => readCookie() === 'system' && applyTheme('system');
        media.addEventListener('change', onChange);
        return () => media.removeEventListener('change', onChange);
    }, []);

    const isDark = appearance === 'dark' || (appearance === 'system' && prefersDark());

    return { appearance, isDark, updateAppearance: update } as const;
}
