import './bootstrap'
import '../css/app.css'
import './../css/theme.css'

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m'

// PrimeVue
import PrimeVue from 'primevue/config'
import Aura from '@primevue/themes/aura'
import { definePreset } from '@primevue/themes'
import ConfirmationService from 'primevue/confirmationservice'
import 'primeicons/primeicons.css'

// import vue toastification
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'

// PrimeVue Components
import Tooltip from 'primevue/tooltip'

const MyPreset = definePreset(Aura, {
    semantic: {
        primary: {
            50: '{indigo.50}',
            100: '{indigo.100}',
            200: '{indigo.200}',
            300: '{indigo.300}',
            400: '{indigo.400}',
            500: '{indigo.500}',
            600: '{indigo.600}',
            700: '{indigo.700}',
            800: '{indigo.800}',
            900: '{indigo.900}',
            950: '{indigo.950}',
        },
    },
}) // Import the Tooltip from PrimeVue

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup ({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(Toast)
            .directive('tooltip', Tooltip)
            .use(PrimeVue, {
                theme: {
                    options: {
                        prefix: 'p',
                        darkModeSelector: '.p-dark',
                        cssLayer: false,
                    },
                    preset: MyPreset,
                },
                locale: {
                    dayNames: ['Duminică', 'Luni', 'Marți', 'Miercuri', 'Joi', 'Vineri', 'Sâmbătă'],
                    dayNamesShort: ['Du', 'Lu', 'Ma', 'Mi', 'Jo', 'Vi', 'Sâ'],
                    dayNamesMin: ['Du', 'Lu', 'Ma', 'Mi', 'Jo', 'Vi', 'Sâ'],
                    monthNames: ['Ianuarie', 'Februarie', 'Martie', 'Aprilie', 'Mai', 'Iunie', 'Iulie', 'August', 'Septembrie', 'Octombrie', 'Noimebrie', 'Decembrie'],
                    monthNamesShort: ['Ian', 'Feb', 'Mar', 'Apr', 'Mai', 'Iun', 'Iul', 'Aug', 'Sep', 'Oct', 'Noi', 'Dec'],
                    today: 'Azi',
                    weekHeader: 'Săpt',
                    firstDayOfWeek: 1,
                    dateFormat: 'dd/mm/yy',
                },
            })
            .use(ConfirmationService)
            .mount(el)
    },
    progress: {
        color: '#4B5563',
    },
})
