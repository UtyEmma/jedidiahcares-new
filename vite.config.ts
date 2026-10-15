import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';
import { fileURLToPath, URL } from 'node:url';
import laravel from 'laravel-vite-plugin';
import inertia from '@inertiajs/vite';
export default defineConfig(({ isSsrBuild }) => ({
  plugins: [
    laravel({
      input: ['resources/js/app.tsx', 'resources/css/filament/admin/theme.css'],
      refresh: ['resources/views/**', 'routes/**', 'app/Http/**'],
    }),
    inertia({ ssr: { host: '127.0.0.1', sourcemap: false } }),
    react(),
    tailwindcss(),
  ],
  resolve: { alias: { '@': fileURLToPath(new URL('./resources/js', import.meta.url)) } },
  build: {
    sourcemap: false,
    rollupOptions: isSsrBuild
      ? {}
      : {
          output: {
            manualChunks: {
              'react-vendor': ['react', 'react-dom', 'react-dom/client'],
              'ui-vendor': [
                '@radix-ui/react-select',
                '@radix-ui/react-accordion',
                '@radix-ui/react-slot',
              ],
            },
          },
        },
  },
}));
