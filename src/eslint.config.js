import js from '@eslint/js';
import reactHooks from 'eslint-plugin-react-hooks';
import globals from 'globals';
import tseslint from 'typescript-eslint';

export default tseslint.config(
    {
        ignores: [
            'vendor',
            'node_modules',
            'public',
            'bootstrap/ssr',
            'resources/js/components/ui/*',
            'resources/js/hooks/use-mobile.ts',
            'resources/js/routes/*',
            'resources/js/actions/*',
            'resources/js/wayfinder/*',
            // Plain-JS three.js scene + its tree-shaken re-exports (untyped)
            'resources/js/lib/banana-bread-scene.js',
            'resources/js/lib/three-showcase.js',
        ],
    },
    js.configs.recommended,
    ...tseslint.configs.recommended,
    {
        files: ['**/*.{ts,tsx}'],
        languageOptions: { globals: { ...globals.browser } },
        plugins: { 'react-hooks': reactHooks },
        rules: {
            ...reactHooks.configs.recommended.rules,
            '@typescript-eslint/no-explicit-any': 'warn',
            '@typescript-eslint/no-unused-vars': ['error', { argsIgnorePattern: '^_', varsIgnorePattern: '^_' }],
        },
    },
    {
        // Playwright audit scripts: Node, plus browser globals inside page.evaluate().
        files: ['scripts/**/*.mjs'],
        languageOptions: { globals: { ...globals.node, ...globals.browser } },
    },
);
