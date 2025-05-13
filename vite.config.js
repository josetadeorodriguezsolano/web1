import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/tailwind.css',
                'resources/scss/app.scss',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                additionalData: '@import "resources/scss/variables";', // Si necesitas variables globales
            },
        },
        // Eliminamos la configuración de postcss ya que usaremos un archivo separado
    },
});
