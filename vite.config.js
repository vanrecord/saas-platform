import crypto from 'node:crypto';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

// Polyfill crypto.hash for Node.js < 20.12.0
if (!crypto.hash) {
    crypto.hash = (algorithm, data, outputEncoding = 'hex') => {
        return crypto.createHash(algorithm).update(data).digest(outputEncoding);
    };
}

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
            detectTls: false,
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
});
