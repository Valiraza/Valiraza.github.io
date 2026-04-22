import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/tailwind.css',
                'resources/js/app.js',
                'resources/compteadmin.js',
                'resources/compteadmin.css',
                'resources/pageAT.js',
                'resources/pageAT.css',
                'resources/circuitCatalog.js',
                'resources/circuitCatalog.css',
                'resources/detailcircuit.css',
                'resources/detailcircuit.js',
                'resources/js/loaders/detail-circuit-loader.js',
                'resources/js/login-admin.js',
            ],
            refresh: true,
        }),
        vue(),
    ],
    server: {
        proxy: {
            '/api': {
                target: 'http://localhost:8000',
                changeOrigin: true,
            },
        },
    },
});
