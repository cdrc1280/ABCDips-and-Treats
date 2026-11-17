import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import babel from '@rolldown/plugin-babel';
import react, { reactCompilerPreset } from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { resolve } from 'node:path';
import { defineConfig } from 'vite';

export default defineConfig(({ command }) => ({
    plugins: [
        laravel({
            input: [
                // Back office (Inertia + React)
                'resources/css/app.css',
                'resources/js/app.tsx',
                // Storefront (Inertia + React)
                'resources/css/storefront.css',
                'resources/js/storefront.tsx',
            ],
            refresh: true,
        }),
        react(),
        // React Compiler (auto-memoisation) for the React code only
        babel({ presets: [reactCompilerPreset()], include: [/\.tsx?$/], exclude: [/node_modules/] }),
        tailwindcss(),
        // Wayfinder runs `php artisan wayfinder:generate`. Vercel's build image has no PHP, so there
        // the committed output (resources/js/{routes,actions,wayfinder}) is used as-is.
        ...(process.env.VERCEL ? [] : [wayfinder({ formVariants: true })]),
    ],
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources/js'),
            '~': resolve(__dirname, 'resources'),
        },
    },
    // scripts/dev.sh runs Vite inside Docker: listen on all interfaces, but tell the
    // browser to load assets / HMR from localhost.
    server: process.env.VITE_IN_DOCKER
        ? {
              host: '0.0.0.0',
              port: 5174, // 5173 is often taken by other local projects
              strictPort: true,
              origin: 'http://localhost:5174',
              hmr: { host: 'localhost', port: 5174 },
              cors: true,
              watch: { usePolling: true, interval: 300, ignored: ['**/storage/framework/views/**', '**/vendor/**'] },
          }
        : {
              host: '127.0.0.1',
              port: 5173,
              watch: { ignored: ['**/storage/framework/views/**'] },
          },
    build: {
        rollupOptions: {
            output: {
                manualChunks(id: string) {
                    if (!id.includes('node_modules')) return undefined;
                    if (/[\\/](react|react-dom|scheduler|@inertiajs)[\\/]/.test(id)) return 'vendor-react';
                    if (/[\\/](radix-ui|@radix-ui|cmdk|react-day-picker|date-fns)[\\/]/.test(id)) return 'vendor-ui';
                    return undefined;
                },
            },
        },
        oxcOptions: command === 'build' ? { compress: { drop_console: true, drop_debugger: true } } : {},
    },
}));
