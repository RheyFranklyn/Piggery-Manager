import '../css/app.css';
import './bootstrap';

import { Head, Link, createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import AdminAuthenticatedLayout from './Layouts/AdminAuthenticatedLayout.vue'; // Import your layout

createInertiaApp({
    title: (title) => `Piggery-Manager ${title}`,// to globalize ag Piggery-Manager na tittle then in every page imo nlng butngan og Head para mo-sumpay ra sya ani.
    resolve: async (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue');
        const page = await pages[`./Pages/${name}.vue`]();

        page.default.layout = page.default.layout || AdminAuthenticatedLayout; // to set a default layout para dili naka mag balik-balik og butang og AdminAuthenticatedLayout sa asa na page.so if wakay ibutang na layout maoni na layout mo-automatic og gamit: AdminAuthenticatedLayout

        return page;
    },
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .component('Head', Head)// use this para dili naka mag butang-butang og -- import { Head, Link } from '@inertiajs/vue3'; -- in very page og mogamit kag head or Link
            .component('Link', Link)
            .use(ZiggyVue)
            .mount(el);
            
    },
    progress: {
        color: '#4B5563',
    },
});
