{{--
    Ponte entre a prévia (iframe) e a tela Design e SEO do painel.
    Incluída só quando o site é renderizado em modo sitePreview.
--}}
<script>
    (() => {
        // Mesma regra de App\Support\SiteTheme: texto sobre a cor e tom "ink" pelo contraste
        const applyPrimary = (value) => {
            const luminance = value.split(' ').map(Number)
                .map((c) => (c /= 255) <= 0.03928 ? c / 12.92 : ((c + 0.055) / 1.055) ** 2.4)
                .reduce((sum, c, i) => sum + c * [0.2126, 0.7152, 0.0722][i], 0);
            const ink = Object.entries({ 500: 1, 600: 0.86, 700: 0.7, 800: 0.56 })
                .find(([, mix]) => 1.05 / (luminance * mix ** 3 + 0.05) >= 4)?.[0] ?? 900;
            const root = document.documentElement.style;

            root.setProperty('--primary-color', value);
            root.setProperty('--color-primary-foreground', luminance > 0.42 ? 'oklch(0.2 0.02 60)' : 'oklch(1 0 0)');
            root.setProperty('--color-primary-ink-base', `var(--color-primary-${ink})`);
        };

        const send = (type, data = {}) => window.parent?.postMessage({ source: 'codhous-preview', type, ...data }, window.location.origin);

        // Links: páginas do site abrem dentro da prévia; o resto não sai do iframe
        document.addEventListener('click', (event) => {
            const link = event.target.closest?.('a[href]');
            if (! link) return;

            const url = new URL(link.href, window.location.href);
            if (url.hash && url.pathname === window.location.pathname) return;

            event.preventDefault();
            event.stopPropagation();

            if (url.origin === window.location.origin && ! url.pathname.startsWith('/admin')) {
                send('navigate', { path: url.pathname });
            } else {
                send('blocked', { href: url.href });
            }
        }, true);

        // Formulários não enviam nada na prévia (evita contatos e orçamentos reais)
        document.addEventListener('submit', (event) => {
            event.preventDefault();
            event.stopImmediatePropagation();
            send('blocked', { form: true });
        }, true);

        // Cor de destaque aplicada na hora, sem recarregar
        window.addEventListener('message', (event) => {
            if (event.origin !== window.location.origin || event.data?.source !== 'codhous-panel') return;

            if (event.data.type === 'color' && /^\d{1,3} \d{1,3} \d{1,3}$/.test(event.data.value)) {
                applyPrimary(event.data.value);
            }
        });

        send('ready', { path: new URL(window.location.href).searchParams.get('pagina') || '/' });
    })();
</script>
