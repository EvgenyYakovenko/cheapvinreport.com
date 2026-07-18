import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: '#eef7f8',
                    100: '#d7ebef',
                    200: '#b4d8df',
                    300: '#88bfca',
                    400: '#559fb0',
                    500: '#2a7d91',
                    600: '#073b4c',
                    700: '#052e3c',
                    800: '#041f28',
                    900: '#010506',
                },
            },
        },
    },

    plugins: [forms],
};
