import './bootstrap';

import { createApp, h } from 'vue'
import { createInertiaApp, Head, Link } from '@inertiajs/vue3'
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'

// PrimeVue
import PrimeVue from 'primevue/config';
import Chart from 'primevue/chart';
import Aura from '@primevue/themes/aura';
import 'primeicons/primeicons.css'
import Tooltip from 'primevue/tooltip';

import MainLayout from './Layouts/MainLayout.vue'


createInertiaApp({
  title: (title) => `MedPort ${title}`,
  resolve: (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    let page = pages[`./Pages/${name}.vue`];
    if (!page) {
        console.error(`Page ${name} not found in './Pages/'`);
        return null;
    }
    // Don't apply MainLayout to auth pages
    if (!name.startsWith('Auth/')) {
        page.default.layout = page.default.layout || MainLayout;
    }
    return page;
  },
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) });

    app.use(plugin)
      .use(ZiggyVue)
      .use(PrimeVue, {
        theme: {
            preset: Aura,
            options: {
              prefix: 'msu-phms',
              darkModeSelector: 'light',
              cssLayer: false
            }
        }
      })
      .component('Head', Head)
      .component('Link', Link)
      .component('Chart', Chart)
      .directive('tooltip', Tooltip)
      .mount(el)
  },
  progress: {
    color: '#29d',
    includeCSS: true,
    showSpinner: false,
  },
})
