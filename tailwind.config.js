import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#002444',
                    50:  '#e8eef5',
                    100: '#c5d4e5',
                    200: '#9fb8d4',
                    300: '#789bc2',
                    400: '#5a85b5',
                    500: '#3c70a8',
                    600: '#2e5f96',
                    700: '#1a3a5c',
                    800: '#122843',
                    900: '#002444',
                },
                surface: '#f7f9fb',
                'surface-container': '#eceef0',
                'surface-lowest': '#ffffff',
                success:  '#16a34a',
                danger:   '#dc2626',
                warning:  '#ca8a04',
                info:     '#2563eb',
            },
            borderRadius: {
                '2xl': '1.5rem',
                '3xl': '2rem',
            },
            boxShadow: {
                'ambient': '0 4px 24px 0 rgba(0, 36, 68, 0.06)',
                'card': '0 2px 12px 0 rgba(0, 36, 68, 0.04)',
            },
        },
    },

    plugins: [forms],
};
