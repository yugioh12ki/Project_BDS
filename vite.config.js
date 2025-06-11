import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/sass/apphome.scss',
                'resources/js/dscactinh.js',
                'resources/js/dscactinh_edit.js',
                'resources/js/google-maps.js',
                'resources/js/property-management.js',
                'resources/sass/appowner.scss',
                'resources/sass/appagent.scss',
                'resources/sass/header.scss',
                'resources/sass/footer.scss',
                'resources/sass/home.scss',
                'resources/sass/login.scss',
                'resources/sass/appagent.scss',
                'resources/sass/register.scss',
                'resources/sass/scss_agent/transactions.scss',
                'resources/sass/scss_agent/create-transaction-modal.scss',
                'resources/js/app.js',
                'resources/js/home.js',
                'resources/js/header_owner.js',
                'resources/js/header_agent.js',
                'resources/js/dscactinh.js',
                'resources/js/dscactinh_edit.js',
                'resources/js/notifications-owner.js',
                'resources/js/notifications-agent.js',
                'resources/js/autocomplete.js',
                'resources/js/property-district.js',
                'resources/js/appointment-agent.js',
                'resources/js/appointment-owner.js',
                'resources/js/create-transaction-modal.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources',
            'jQuery': 'jquery'
        },
    },
});
