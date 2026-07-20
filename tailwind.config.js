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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Demo 1 palette: near-black "ink" as primary, warm orange accent.
                primary: {
                    50: '#f5f6f7',
                    100: '#e9ebee',
                    200: '#d3d7dd',
                    300: '#aab2bd',
                    400: '#6b7480',
                    500: '#3d434c',
                    600: '#141518',
                    700: '#0d0e10',
                    800: '#080809',
                    900: '#030304',
                },
                acc: {
                    50: '#fff3ee',
                    100: '#ffe1d5',
                    200: '#ffc3aa',
                    300: '#ff9d76',
                    400: '#ff7847',
                    500: '#ff5a1f',
                    600: '#e6480f',
                    700: '#bd3a0d',
                },
            },
        },
    },

    plugins: [forms],
};
