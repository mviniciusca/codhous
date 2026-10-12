{{--
    Prévia de alertas (iframe do painel). O alerta é renderizado pelo próprio componente
    do site (x-site-alerts) no servidor; o editor envia o HTML novo a cada alteração e
    ele substitui o anterior aqui, sem recarregar a página (o Alpine inicia os novos nós).
--}}
<div id="al-preview-root">
    @if($alerts->isNotEmpty())
        <x-site-alerts :alerts="$alerts" :preview="true" />
    @endif
</div>
<x-site-alerts.styles />
<x-site-alerts.script />
<script>
    (() => {
        const root = document.getElementById('al-preview-root');
        const send = (type, data = {}) => window.parent?.postMessage({ source: 'codhous-preview', type, ...data }, window.location.origin);

        // Botões do alerta são demonstrativos: não navegam, só avisam o painel o que fariam
        window.addEventListener('click', (event) => {
            const target = event.target instanceof Element ? event.target : null;
            if (! target || ! root.contains(target)) return;

            const link = target.closest('a[href]');
            if (link) {
                event.preventDefault();
                event.stopImmediatePropagation();
                send('alert-cta', { href: link.getAttribute('href'), label: link.textContent.trim() });
                return;
            }

            if (target.closest('[data-sa-dismiss]')) send('alert-dismissed');
        }, true);

        window.addEventListener('message', (event) => {
            if (event.origin !== window.location.origin || event.source !== window.parent) return;
            if (event.data?.source !== 'codhous-panel' || event.data.type !== 'alert') return;

            root.innerHTML = typeof event.data.html === 'string' ? event.data.html : '';
        });
    })();
</script>
