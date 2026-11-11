import defaultTheme from 'tailwindcss/defaultTheme';
import colors from 'tailwindcss/colors';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import aspectRatio from '@tailwindcss/aspect-ratio';
import flowbitePlugin from 'flowbite/plugin';

/** @type {import('tailwindcss').Config} */
export default {
    mode: 'jit',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './node_modules/@protonemedia/inertiajs-tables-laravel-query-builder/**/*.{js,vue}',
        './node_modules/litepie-datepicker/**/*.js',
        './node_modules/@flavorly/vanilla-components/dist/presets/tailwind/all.json',
    ],
    darkMode: 'class', // or 'media' or false
    theme: {
        extend: {
            fontSize: {
                md: ['1rem', { lineHeight: '1.5rem' }],
            },
            fontFamily: {
                sans: ['cairo', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                light: 'var(--light)',
                dark: 'var(--dark)',
                darker: 'var(--darker)',
                primary: {
                    DEFAULT: 'var(--color-primary)',
                    50: 'var(--color-primary-50)',
                    100: 'var(--color-primary-100)',
                    light: 'var(--color-primary-light)',
                    lighter: 'var(--color-primary-lighter)',
                    dark: 'var(--color-primary-dark)',
                    darker: 'var(--color-primary-darker)',
                },
                'litepie-primary': colors.sky, // color system for light mode
                'litepie-secondary': colors.gray, // color system for dark mode
                secondary: {
                    DEFAULT: colors.fuchsia[600],
                    50: colors.fuchsia[50],
                    100: colors.fuchsia[100],
                    light: colors.fuchsia[500],
                    lighter: colors.fuchsia[400],
                    dark: colors.fuchsia[700],
                    darker: colors.fuchsia[800],
                },
                success: {
                    DEFAULT: colors.green[600],
                    50: colors.green[50],
                    100: colors.green[100],
                    light: colors.green[500],
                    lighter: colors.green[400],
                    dark: colors.green[700],
                    darker: colors.green[800],
                },
                warning: {
                    DEFAULT: colors.orange[600],
                    50: colors.orange[50],
                    100: colors.orange[100],
                    light: colors.orange[500],
                    lighter: colors.orange[400],
                    dark: colors.orange[700],
                    darker: colors.orange[800],
                },
                danger: {
                    DEFAULT: colors.red[600],
                    50: colors.red[50],
                    100: colors.red[100],
                    light: colors.red[500],
                    lighter: colors.red[400],
                    dark: colors.red[700],
                    darker: colors.red[800],
                },
                info: {
                    DEFAULT: colors.cyan[600],
                    50: colors.cyan[50],
                    100: colors.cyan[100],
                    light: colors.cyan[500],
                    lighter: colors.cyan[400],
                    dark: colors.cyan[700],
                    darker: colors.cyan[800],
                },
            },
        },
    },
    variants: {
        extend: {
            backgroundColor: ['checked', 'disabled'],
            opacity: ['dark'],
            overflow: ['hover'],
            cursor: ['disabled'],
            textOpacity: ['disabled'],
            textColor: ['disabled'],
        },
    },
    plugins: [
        forms,
        flowbitePlugin,
        typography,
        aspectRatio,
    ],
};
