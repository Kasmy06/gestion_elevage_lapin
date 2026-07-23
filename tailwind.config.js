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
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                farm: {
                    green: '#2E7D32',
                    'green-light': '#66BB6A',
                    'green-pale': '#E8F5E9',
                    'green-dark': '#1B5E20',
                    orange: '#FB8C00',
                    'orange-pale': '#FFF3E0',
                    blue: '#1976D2',
                    'blue-pale': '#E3F2FD',
                    red: '#D32F2F',
                    'red-pale': '#FFEBEE',
                    purple: '#7B1FA2',
                    'purple-pale': '#F3E5F5',
                    bg: '#F5F5F5',
                    text: '#424242',
                    'text-light': '#757575',
                    border: '#E0E0E0',
                },
            },
        },
    },

    plugins: [forms],
};
