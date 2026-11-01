import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['var(--font-body)'],
                display: ['var(--font-display)'],
            },
            colors: {
                paper: 'var(--color-paper)',
                'paper-2': 'var(--color-paper-2)',
                'paper-3': 'var(--color-paper-3)',
                ink: 'var(--color-ink)',
                'ink-2': 'var(--color-ink-2)',
                muted: 'var(--color-muted)',
                rule: 'var(--color-rule)',
                accent: 'var(--color-accent)',
                'accent-2': 'var(--color-accent-2)',
                error: 'var(--color-error)',
                success: 'var(--color-success)',
            },
        },
    },

    plugins: [forms({ strategy: 'class' })],
};
