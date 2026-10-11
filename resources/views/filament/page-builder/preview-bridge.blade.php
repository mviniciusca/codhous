{{--
    Ponte de comunicação entre o Live Preview (iframe) e o Page Builder (painel).
    Só é incluída quando a página é renderizada em modo builderPreview.
--}}
<style>
    :root { --pb-accent: 37 99 235; }
    html { scroll-behavior: auto !important; }

    .pb-block { position: relative; cursor: pointer; scroll-margin-top: 96px; }
    .pb-block::after {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        z-index: 60;
        border: 2px solid transparent;
        border-radius: 2px;
        transition: border-color .15s ease, background-color .15s ease, box-shadow .15s ease;
    }
    .pb-block:hover::after,
    .pb-block.pb-hover::after {
        border-color: rgb(var(--pb-accent) / .85);
        background-color: rgb(var(--pb-accent) / .035);
    }
    .pb-block.pb-selected::after {
        border: 3px solid rgb(var(--pb-accent));
        box-shadow: inset 0 0 0 1px #fff, 0 0 0 4px rgb(var(--pb-accent) / .15);
    }

    .pb-tag {
        position: absolute;
        top: 12px;
        left: 12px;
        z-index: 61;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px 5px 8px;
        border-radius: 999px;
        background: rgb(var(--pb-accent));
        color: #fff;
        font: 600 12px/1 ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif;
        letter-spacing: .01em;
        box-shadow: 0 6px 18px -6px rgb(var(--pb-accent) / .6);
        opacity: 0;
        transform: translateY(-4px);
        transition: opacity .15s ease, transform .15s ease;
        pointer-events: none;
    }
    .pb-tag svg { width: 13px; height: 13px; }
    .pb-block:hover > .pb-tag,
    .pb-block.pb-hover > .pb-tag,
    .pb-block.pb-selected > .pb-tag { opacity: 1; transform: none; }

    .pb-block-error {
        margin: 24px auto;
        max-width: 72rem;
        display: flex;
        flex-direction: column;
        gap: 6px;
        padding: 20px 24px;
        border: 2px dashed #f59e0b;
        border-radius: 16px;
        background: #fffbeb;
        color: #92400e;
        font: 14px/1.5 ui-sans-serif, system-ui, sans-serif;
    }

    .pb-empty {
        margin: 64px auto;
        max-width: 36rem;
        padding: 56px 32px;
        text-align: center;
        border: 2px dashed #cbd5e1;
        border-radius: 24px;
        color: #475569;
        font-family: ui-sans-serif, system-ui, sans-serif;
    }
    .pb-empty-icon {
        width: 56px; height: 56px;
        margin: 0 auto 16px;
        display: grid; place-items: center;
        border-radius: 16px;
        background: rgb(var(--pb-accent) / .1);
        color: rgb(var(--pb-accent));
        font-size: 28px; font-weight: 300;
    }
    .pb-empty h2 { font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 6px; }
</style>

<script>
    (function () {
        if (window.__pbBridge) return;
        window.__pbBridge = true;

        var origin = window.location.origin;
        var inFrame = window.parent && window.parent !== window;

        function post(message) {
            if (!inFrame) return;
            message.source = 'pb-preview';
            window.parent.postMessage(message, origin);
        }

        function blocks() {
            return Array.prototype.slice.call(document.querySelectorAll('[data-pb-block]'));
        }

        function find(uuid) {
            if (!uuid) return null;
            return blocks().find(function (el) { return el.dataset.pbBlock === uuid; }) || null;
        }

        // Etiqueta com o nome do bloco
        var pencil = '<svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M13.586 3.586a2 2 0 1 1 2.828 2.828l-.793.793-2.828-2.828.793-.793ZM11.379 5.793 3 14.172V17h2.828l8.38-8.379-2.83-2.828Z"/></svg>';
        blocks().forEach(function (el) {
            var tag = document.createElement('div');
            tag.className = 'pb-tag';
            tag.innerHTML = pencil;
            tag.appendChild(document.createTextNode(el.dataset.pbLabel || 'Bloco'));
            el.insertBefore(tag, el.firstChild);
        });

        // Clique: bloqueia navegação e seleciona o bloco para edição
        document.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var block = event.target.closest('[data-pb-block]');
            if (block) post({ type: 'select', uuid: block.dataset.pbBlock });
        }, true);

        document.addEventListener('submit', function (event) { event.preventDefault(); }, true);

        // Hover sincronizado com a lista de blocos
        var lastHover = null;
        document.addEventListener('mouseover', function (event) {
            var block = event.target.closest('[data-pb-block]');
            var uuid = block ? block.dataset.pbBlock : null;
            if (uuid === lastHover) return;
            lastHover = uuid;
            post({ type: 'hover', uuid: uuid });
        });
        document.addEventListener('mouseleave', function () {
            lastHover = null;
            post({ type: 'hover', uuid: null });
        });

        // Comandos vindos do painel
        window.addEventListener('message', function (event) {
            if (event.origin !== origin || !event.data || event.data.source !== 'pb-host') return;

            var data = event.data;

            if (data.type === 'highlight') {
                blocks().forEach(function (el) { el.classList.toggle('pb-hover', el.dataset.pbBlock === data.uuid); });
            }

            if (data.type === 'select') {
                blocks().forEach(function (el) { el.classList.toggle('pb-selected', el.dataset.pbBlock === data.uuid); });
            }

            if ((data.type === 'select' && data.scroll) || data.type === 'scroll') {
                var target = find(data.uuid);
                if (target) target.scrollIntoView({ behavior: data.instant ? 'auto' : 'smooth', block: 'start' });
            }
        });

        post({ type: 'ready', blocks: blocks().map(function (el) { return el.dataset.pbBlock; }) });
    })();
</script>
