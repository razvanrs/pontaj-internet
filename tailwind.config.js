import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Montserrat', 'sans-serif'],
                times: ['Times New Roman', 'serif'],
            },
            colors: {
                main: '#f6f8f9', // background color
                primary: '#040508', // text color
                secondary: '#a3a4b0', // icons
                brand: '#4f46e5', // buttons & hovers
                line: '#e7edf6', // borders
                transparent: 'transparent',
                current: 'currentColor',
            },
            fontSize: {
                xxs: ['0.6rem', '0.8rem'],
            },
        },
    },

    plugins: [forms, typography],
}
