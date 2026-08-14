import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import tailwindcss from '@tailwindcss/vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

export default defineConfig(({ command }) => ({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
    tailwindcss(),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
  ],
  resolve: {
    alias: {
      '@': resolve(__dirname, 'resources/js'),
      '~': resolve(__dirname, 'resources'),
    },
  },
  server: {
    host: '127.0.0.1',
    port: 5173,
    watch: {
      ignored: ['**/storage/framework/views/**'],
    },
  },
  // Strip all console.* and debugger statements from production bundles.
  // This prevents API routes, error messages, and app internals from
  // being visible to anyone inspecting the browser devtools console.
  build: {
    // Vite 8 uses OXC (rolldown) for minification — use oxcOptions to drop console
    oxcOptions: command === 'build'
      ? { compress: { drop_console: true, drop_debugger: true } }
      : {},
  },
}))
