/** @type {import('tailwindcss').Config} */
const colors = require('tailwindcss/colors')
export default {
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './resources/**/*.blade.php',
        "./resources/views/components/filament-fabricator/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    safelist: [
        'bg-contain', 'bg-cover', 'bg-auto', 'bg-scroll', 'bg-fixed', 'bg-center', 'bg-top',
        'bg-left', 'bg-right', 'bg-repeat-x', 'bg-repeat-y',
        'bg-repeat', 'bg-no-repeat', 'order-1', 'order-2', 'order-3'
    ],
    darkMode: 'class',
    theme: {
        fontFamily: {
            sans: ['"Inter", sans-serif']
        },
        colors: {
            primary: colors.zinc,
            secondary: colors.indigo,
            tertiary: colors.rose,
            gray: colors.zinc,
            white: colors.white,
            info: colors.indigo,
            green: colors.green,
            danger: colors.red,
            warning: colors.orange,
        },
        extend: {},
    },
    plugins: [
        require('@tailwindcss/typography'),
        require('@tailwindcss/forms'),
    ],
}

