import '../css/storefront.css';

import { createInertiaApp, type ResolvedComponent } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { ReactNode } from 'react';
import { createRoot } from 'react-dom/client';

import { applyTheme, type Appearance } from '@/hooks/use-appearance';
import AuthLayout from '@/layouts/auth/auth-layout';
import StorefrontLayout from '@/layouts/storefront-layout';

type PageModule = { default: { layout?: unknown } };

/**
 * Storefront entry (everything outside /admin). Layout by page name:
 * auth/* → AuthLayout, storefront/* and account/* → StorefrontLayout.
 */
createInertiaApp({
    title: (title) => (title ? `${title} — ABCDips & Treats` : 'ABCDips & Treats — Handcrafted Pastries'),
    resolve: async (name) => {
        const pages = import.meta.glob<PageModule>(['./pages/storefront/**/*.tsx', './pages/account/**/*.tsx', './pages/auth/**/*.tsx']);
        const target = name === 'error' ? 'storefront/error' : name;
        const page = (await resolvePageComponent(`./pages/${target}.tsx`, pages)) as PageModule;

        if (typeof page.default.layout !== 'function') {
            const Layout = name.startsWith('auth/') ? AuthLayout : StorefrontLayout;
            page.default.layout = (content: ReactNode) => <Layout>{content}</Layout>;
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
