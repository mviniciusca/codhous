/** @type {import('tailwindcss').Config} */
// Cor vinda de uma variável CSS --color-<nome>, com suporte aos modificadores de opacidade (bg-primary/10)
const token = (name) => `color-mix(in oklab, var(--color-${name}) calc(<alpha-value> * 100%), transparent)`

export default {
    content: [
        './app/Filament/**/*.php',
        './app/Livewire/**/*.php',
        './app/Support/**/*.php',
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
                sans: ['Jost', 'system-ui', 'sans-serif'],
                mono: ['"Space Grotesk"', 'monospace'],
            },
            colors: {
                // Tokens de tema: cada cor é uma variável CSS completa (ver resources/css/app.css).
                // Um tema só precisa redefinir as variáveis; as classes continuam as mesmas.
                background: token('background'),
                foreground: token('foreground'),
                card: {
                    DEFAULT: token('card'),
                    foreground: token('card-foreground'),
                },
                primary: {
                    DEFAULT: token('primary-500'),
                    foreground: token('primary-foreground'),
                    // Tom seguro para texto sobre fundo claro (escolhido pelo contraste da cor da marca)
                    ink: token('primary-ink'),
                    400: token('primary-400'),
                    500: token('primary-500'),
                    600: token('primary-600'),
                    700: token('primary-700'),
                    800: token('primary-800'),
                    900: token('primary-900'),
                    950: token('primary-950'),
                },
                secondary: {
                    DEFAULT: token('muted'),
                    foreground: token('foreground'),
                },
                muted: {
                    DEFAULT: token('muted'),
                    foreground: token('muted-foreground'),
                },
                accent: {
                    DEFAULT: token('muted'),
                    foreground: token('foreground'),
                },
                popover: {
                    DEFAULT: token('card'),
                    foreground: token('card-foreground'),
                },
                destructive: {
                    DEFAULT: 'oklch(0.6 0.2 25 / <alpha-value>)',
                    foreground: 'oklch(0.98 0.002 250 / <alpha-value>)',
                },
                border: token('border'),
                input: token('border'),
                ring: token('primary-500'),
            },
            borderRadius: {
                card: 'var(--radius-card)',
                control: 'var(--radius-control)',
            },
            transitionTimingFunction: {
                'out-expo': 'cubic-bezier(0.16, 1, 0.3, 1)',
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
                marquee: {
                    from: { transform: 'translateX(0)' },
                    to: { transform: 'translateX(-50%)' },
                },
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
                marquee: 'marquee var(--marquee-duration, 40s) linear infinite',
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
