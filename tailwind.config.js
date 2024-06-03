/** @type {import('tailwindcss').Config} */
const colors = require('tailwindcss/colors')
export default {
    content: [
    "./resources/**/*.blade.php",
    "./resources/views/components/filament-fabricator/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    ],
    safelist: [
       'bg-contain' ,'bg-cover', 'bg-auto', 'bg-scroll', 'bg-fixed', 'bg-center', 'bg-top', 'bg-left', 'bg-right', 'bg-repeat-x', 'bg-repeat-y', 'bg-repeat', 'bg-no-repeat'
],
    theme: {
        fontFamily: {
            sans: ['"Inter", sans-serif']
        },
        colors: {
            primary: colors.zinc,
            secondary: colors.indigo,
            tertiary: colors.rose,
            red: colors.red,
            green: colors.green,
        },
        extend: {},
  },
  plugins: [],
}

