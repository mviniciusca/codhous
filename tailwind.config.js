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
        "./node_modules/flowbite/**/*.js"

    ],
    safelist: [
        'bg-contain', 'bg-cover', 'bg-auto', 'bg-scroll', 'bg-fixed', 'bg-center', 'bg-top',
        'bg-left', 'bg-right', 'bg-repeat-x', 'bg-repeat-y', 'bg-repeat', 'bg-no-repeat', 'order-1',
        'order-2', 'order-3', 'opacity-30', 'opacity-50', 'opacity-90',
    ],
    darkMode: 'class',
    theme: {
        fontFamily: {
            sans: ['Inter', 'sans-serif'],
        },
        colors: {
            primary: colors.zinc,
            secondary: colors.indigo,
            tertiary: colors.rose,
        },
        extend: {},
    },
    plugins: [
        require('@tailwindcss/typography'),
        require('@tailwindcss/forms'),
        require('flowbite/plugin')
    ],
}

