import './bootstrap';

import { createApp, h } from 'vue'
import { createInertiaApp, Head, Link, router } from '@inertiajs/vue3'
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import axios from 'axios'

// PrimeVue
import PrimeVue from 'primevue/config';
import Chart from 'primevue/chart';
import Aura from '@primevue/themes/aura';
import 'primeicons/primeicons.css'
import Tooltip from 'primevue/tooltip';

import MainLayout from './Layouts/MainLayout.vue'

// Configure Axios to use CSRF token from meta tag
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}

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

// Handle CSRF token expiration and other errors
router.on('error', (errors) => {
  // Handle 419 CSRF token mismatch errors
  if (errors.response && errors.response.status === 419) {
    console.log('CSRF token expired, redirecting to login...');
    window.location.href = '/login';
    return;
  }
  
  // Handle 401 Unauthorized errors
  if (errors.response && errors.response.status === 401) {
    console.log('Unauthorized, redirecting to login...');
    window.location.href = '/login';
    return;
  }
  
  // Handle session expired errors
  if (errors.response && errors.response.status === 403) {
    console.log('Session expired, redirecting to login...');
    window.location.href = '/login';
    return;
  }
});

// Handle global axios errors for CSRF token issues
window.axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response && error.response.status === 419) {
      console.log('CSRF token expired in axios request, redirecting to login...');
      window.location.href = '/login';
      return Promise.reject(error);
    }
    return Promise.reject(error);
  }
);
