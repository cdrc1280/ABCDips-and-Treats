import '../css/app.css';

import { createInertiaApp, type ResolvedComponent } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { ReactNode } from 'react';
import { createRoot } from 'react-dom/client';

import { applyTheme, type Appearance } from '@/hooks/use-appearance';
import AppLayout from '@/layouts/app-layout';
import type { PageLayoutMeta } from '@/types';

const appName = import.meta.env.VITE_APP_NAME || 'ABCDips & Treats';

type PageModule = { default: { layout?: PageLayoutMeta | ((page: ReactNode) => ReactNode) } };

/**
 * Back-office entry (/admin). Layout is chosen by page name; pages declare
 * `Page.layout = { breadcrumbs }`, which AppLayout consumes.
 */
createInertiaApp({
    title: (title) => (title ? `${title} · ${appName}` : `Back office · ${appName}`),
    resolve: async (name) => {
        const page = (await resolvePageComponent(`./pages/${name}.tsx`, import.meta.glob<PageModule>('./pages/**/*.tsx'))) as PageModule;
        const meta = page.default.layout;
        if (typeof meta !== 'function') {
            page.default.layout = (content: ReactNode) => <AppLayout breadcrumbs={meta?.breadcrumbs}>{content}</AppLayout>;
        }
        return page.default as ResolvedComponent;
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
    progress: { color: '#C08E5D', delay: 150 },
});

const cookie = document.cookie.match(/(?:^|; )appearance=(light|dark|system)/)?.[1] as Appearance | undefined;
applyTheme(cookie ?? 'system');
