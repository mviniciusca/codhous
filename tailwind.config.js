/** @type {import('tailwindcss').Config} */
const colors = require('tailwindcss/colors')

// Altere a cor aqui para qualquer uma do Tailwind (ex: colors.zinc, colors.red, colors.orange)
const themeColor = colors.orange

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
    darkMode: 'class',
    theme: {
        container: {
            center: true,
            padding: "2rem",
            screens: {
                "2xl": "1400px",
            },
        },
        extend: {
            fontFamily: {
                sans: ['Inter', 'system-ui', 'sans-serif'],
                mono: ['"Space Grotesk"', 'monospace'],
            },
            colors: {
                background: 'oklch(0.97 0.002 250 / <alpha-value>)',
                foreground: 'oklch(0.18 0.01 250 / <alpha-value>)',
                card: {
                    DEFAULT: 'oklch(1 0 0 / <alpha-value>)',
                    foreground: 'oklch(0.18 0.01 250 / <alpha-value>)',
                },
                primary: {
                    DEFAULT: themeColor[500],
                    foreground: colors.white,
                },
                secondary: {
                    DEFAULT: 'oklch(0.92 0.005 250 / <alpha-value>)',
                    foreground: 'oklch(0.18 0.01 250 / <alpha-value>)',
                },
                muted: {
                    DEFAULT: 'oklch(0.92 0.005 250 / <alpha-value>)',
                    foreground: 'oklch(0.45 0.01 250 / <alpha-value>)',
                },
                accent: {
                    DEFAULT: 'oklch(0.92 0.005 250 / <alpha-value>)',
                    foreground: 'oklch(0.18 0.01 250 / <alpha-value>)',
                },
                popover: {
                    DEFAULT: 'oklch(1 0 0 / <alpha-value>)',
                    foreground: 'oklch(0.18 0.01 250 / <alpha-value>)',
                },
                destructive: {
                    DEFAULT: 'oklch(0.6 0.2 25 / <alpha-value>)',
                    foreground: 'oklch(0.98 0.002 250 / <alpha-value>)',
                },
                border: 'oklch(0.88 0.005 250 / <alpha-value>)',
                input: 'oklch(0.88 0.005 250 / <alpha-value>)',
                ring: themeColor[500],
            },
            boxShadow: {
                'sm': '0 1px 2px 0 rgb(0 0 0 / 0.05)',
                'DEFAULT': '0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)',
                'md': '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)',
                'lg': '0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)',
                'xl': '0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1)',
                '2xl': '0 25px 50px -12px rgb(0 0 0 / 0.25)',
                'inner': 'inset 0 2px 4px 0 rgb(0 0 0 / 0.05)',
            },
            keyframes: {
                "accordion-down": {
                    from: { height: 0 },
                    to: { height: "var(--radix-accordion-content-height)" },
                },
                "accordion-up": {
                    from: { height: "var(--radix-accordion-content-height)" },
                    to: { height: 0 },
                },
            },
            animation: {
                "accordion-down": "accordion-down 0.2s ease-out",
                "accordion-up": "accordion-up 0.2s ease-out",
            },
        },
    },
    plugins: [
        require('@tailwindcss/typography'),
        require('@tailwindcss/forms'),
        require('flowbite/plugin')
    ],
}

