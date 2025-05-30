import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss', 
                'resources/sass/appowner.scss',
                'resources/sass/appagent.scss',
                'resources/sass/header.scss',
                'resources/sass/footer.scss',
                'resources/sass/home.scss',
                'resources/sass/login.scss',
                'resources/sass/register.scss',
                'resources/js/app.js',
                'resources/js/home.js',
                'resources/js/dscactinh.js',
                'resources/js/notifications.js',
                'resources/js/owner-autocomplete.js',
                'resources/css/owner-autocomplete.css',
                'resources/css/customer-autocomplete.css'
            ],
            refresh: true,
        }),
    ],

});
