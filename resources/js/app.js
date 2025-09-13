import "./bootstrap";
import "../css/app.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { i18nVue } from "laravel-vue-i18n";
import { PerfectScrollbarPlugin } from 'vue3-perfect-scrollbar';
import 'vue3-perfect-scrollbar/style.css';

import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'

const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob("./Pages/**/*.vue")), 
    setup({ el, App, props, plugin }) {
        return createApp({
            render: () => h(App, props),
        })
        .use(plugin)
        .use(pinia)
            .use(i18nVue, {
                // lang: 'pt',
                resolve: lang => {
                    const langs = import.meta.glob('../../lang/*.json', { eager: true });
                    return langs[`../../lang/${lang}.json`].default;
                },
            })
            .use(ZiggyVue)
            .use(PerfectScrollbarPlugin)
            .mount(el);
    },
    progress: {
        color: "#ff0000",
    },
});
