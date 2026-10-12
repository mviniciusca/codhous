{{--
    Ponte entre a prévia (iframe) e a tela Design e SEO do painel.
    Incluída só quando o site é renderizado em modo sitePreview.
--}}
<script>
    (() => {
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
                ['--primary-color', '--primary-400', '--primary-500', '--primary-600'].forEach((name) => {
                    document.documentElement.style.setProperty(name, event.data.value);
                });
            }
        });

        send('ready', { path: new URL(window.location.href).searchParams.get('pagina') || '/' });
    })();
</script>
