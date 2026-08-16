import '@varlet/ui/es/style';
import '@varlet/ui/es/varlet.css';
import { createInertiaApp } from '@inertiajs/vue3';
import Varlet from '@varlet/ui';
import { ZiggyVue } from 'ziggy-js';

const appName = import.meta.env.VITE_APP_NAME || 'Komang';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    withApp(app) {
        app.use(Varlet);
        app.use(ZiggyVue);
    },
    progress: {
        color: '#2979ff',
    },
});
