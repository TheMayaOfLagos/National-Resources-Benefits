import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig(({ isSsrBuild }) => ({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            ssr: 'resources/js/ssr.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    build: {
        // Enable minification
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
            },
        },
        // Optimize chunk splitting - only for client build
        rollupOptions: isSsrBuild ? {} : {
            output: {
                manualChunks: {
                    // Vendor chunks for better caching
                    'vendor-vue': ['vue', '@inertiajs/vue3'],
                    'vendor-primevue': ['primevue'],
                },
            },
        },
        // Increase chunk size warning limit
        chunkSizeWarningLimit: 1000,
    },
}));
