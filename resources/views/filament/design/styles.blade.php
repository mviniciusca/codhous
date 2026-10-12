{{-- Design e SEO (CSS puro). Prefixo ds-; tokens e primitivas vêm de filament/admin/ui --}}
<style>
    /* O botão Descartar (header da página) só aparece com alterações pendentes */
    html:not(.ds-dirty) [data-ds-discard] { display: none; }

    .ds-root { container: ds / inline-size; }
    .ds {
        --ds-height: max(600px, calc(100dvh - 196px));
        display: grid;
        grid-template-columns: minmax(380px, 500px) minmax(0, 1fr);
        gap: 20px;
        height: var(--ds-height);
    }

    /* ---------- Editor ---------- */
    .ds-editor,
    .ds-preview {
        display: flex;
        flex-direction: column;
        min-width: 0;
        min-height: 0;
        background: var(--cx-surface);
        border: 1px solid var(--cx-border);
        border-radius: var(--cx-radius);
        box-shadow: var(--cx-shadow);
        overflow: hidden;
    }
    .ds-editor-scroll { flex: 1; overflow-y: auto; overscroll-behavior: contain; scrollbar-gutter: stable; }

    /* Abas do Filament: barra fixa no topo do editor, estilo sublinhado */
    .ds-form .fi-tabs {
        position: sticky;
        top: 0;
        z-index: 5;
        gap: 2px;
        margin: 0;
        padding: 8px 12px 0;
        max-width: none;
        border-radius: 0;
        background: var(--cx-surface);
        border-bottom: 1px solid var(--cx-border);
        box-shadow: none;
        overflow-x: auto;
        scrollbar-width: none;
    }
    .ds-form .fi-tabs-item {
        flex: none;
        margin-bottom: -1px;
        padding: 10px 10px 12px;
        border-radius: 0;
        border-bottom: 2px solid transparent;
        background: transparent !important;
    }
    .ds-form .fi-tabs-item-active { border-bottom-color: rgb(var(--primary-500)); }
    .ds-form .fi-tabs-item-label { font-size: 13px; }
    .ds-form .fi-fo-tabs > div:not(.fi-tabs) { padding: 4px 20px 24px; }

    /* Seções sem cartão: separadas por linha, mais leves dentro do painel */
    .ds-form .fi-section {
        background: transparent;
        box-shadow: none;
        border-radius: 0;
    }
    .ds-form .fi-section + .fi-section { border-top: 1px solid var(--cx-border); margin-top: 8px; }
    .ds-form .fi-section-header { padding: 18px 0 4px; }
    .ds-form .fi-section-content-ctn { border-top: 0 !important; }
    .ds-form .fi-section-content { padding: 12px 0 16px !important; }
    .ds-form .fi-section-header-heading { font-size: 14px; }

    .ds-editor-foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 10px 16px;
        font-size: 12px;
        border-top: 1px solid var(--cx-border);
        background: var(--cx-surface-2);
    }
    .ds-state { display: inline-flex; align-items: center; gap: 8px; font-weight: 500; color: var(--cx-muted); }
    .ds-state.is-saved { --tone: var(--success-500); }
    .ds-state.is-dirty { --tone: var(--warning-500); color: var(--cx-text); }
    .ds-hint { color: var(--cx-subtle); }
    .ds-hint kbd {
        padding: 1px 5px;
        border-radius: 4px;
        font: inherit;
        font-size: 11px;
        box-shadow: inset 0 0 0 1px var(--cx-border-strong);
    }

    /* ---------- Prévia ---------- */
    .ds-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 10px;
        border-bottom: 1px solid var(--cx-border);
    }
    .ds-page {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-width: 0;
        padding: 0 4px 0 10px;
        border-radius: 8px;
        color: var(--cx-muted);
        box-shadow: inset 0 0 0 1px var(--cx-border-strong);
    }
    .ds-page select {
        min-width: 0;
        max-width: 200px;
        padding: 6px 28px 6px 2px;
        border: 0;
        font-size: 13px;
        font-weight: 500;
        color: var(--cx-text);
        background-color: transparent;
    }
    .ds-page select:focus { box-shadow: none; outline: none; }
    .ds-page:focus-within { box-shadow: inset 0 0 0 2px rgb(var(--primary-500)); }
    .dark .ds-page select option { background: rgb(var(--gray-900)); }

    .ds-devices {
        display: inline-flex;
        gap: 2px;
        margin: 0 auto;
        padding: 3px;
        border-radius: 9px;
        background: var(--cx-track);
    }
    .ds-devices button {
        display: grid;
        place-items: center;
        width: 34px;
        height: 28px;
        border-radius: 7px;
        color: var(--cx-muted);
        transition: background-color .15s ease, color .15s ease;
    }
    .ds-devices button:hover { color: var(--cx-text); }
    .ds-devices button[aria-checked="true"] { color: var(--cx-text); background: var(--cx-surface); box-shadow: 0 1px 2px rgba(0, 0, 0, .12); }

    .ds-bar-end { display: flex; align-items: center; gap: 4px; }
    .ds-status { font-size: 12px; color: var(--cx-muted); }
    .ds-status-loading { display: inline-flex; align-items: center; gap: 6px; margin-right: 6px; }
    .ds-status-error { margin-right: 6px; font-weight: 500; color: rgb(var(--danger-500)); }
    .ds-icon-btn {
        display: grid;
        place-items: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        color: var(--cx-muted);
        transition: background-color .15s ease, color .15s ease;
    }
    .ds-icon-btn:hover { color: var(--cx-text); background: var(--cx-hover); }

    .ds-stage {
        position: relative;
        flex: 1;
        min-height: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        overflow: hidden;
        background:
            radial-gradient(circle at 1px 1px, rgba(var(--gray-500), .18) 1px, transparent 0) 0 0 / 16px 16px,
            var(--cx-surface-2);
    }
    .ds-frame {
        position: relative;
        flex: none;
        transform-origin: top center;
        background: #fff;
    }
    .ds-stage:not([data-device="desktop"]) .ds-frame {
        margin-top: 16px;
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 0 0 8px rgb(var(--gray-800)), 0 0 0 9px rgba(255, 255, 255, .08), 0 24px 48px -12px rgba(0, 0, 0, .45);
    }
    .ds-frame iframe {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        border: 0;
        background: #fff;
        opacity: 0;
        pointer-events: none;
        transition: opacity .2s ease;
    }
    .ds-frame iframe.is-active { opacity: 1; pointer-events: auto; }

    .ds-notice {
        position: absolute;
        bottom: 16px;
        left: 50%;
        transform: translateX(-50%);
        padding: 8px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 500;
        color: #fff;
        background: rgba(var(--gray-950), .88);
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
        white-space: nowrap;
    }
    .ds-preview-foot {
        padding: 8px 14px;
        font-size: 12px;
        color: var(--cx-subtle);
        border-top: 1px solid var(--cx-border);
    }

    /* ---------- Prévia do Google ---------- */
    .ds-serp { display: grid; gap: 10px; }
    .ds-serp-head { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 6px 12px; font-size: 12px; font-weight: 500; color: var(--cx-muted); }
    .ds-serp-counts { display: inline-flex; gap: 12px; }
    .ds-serp-counts .is-ok b { color: rgb(var(--success-500)); }
    .ds-serp-counts .is-warn b { color: rgb(var(--warning-500)); }
    .ds-serp-counts .is-off b { color: var(--cx-subtle); }
    .ds-serp-card {
        padding: 14px 16px;
        border-radius: 12px;
        font-family: Arial, sans-serif;
        background: #fff;
        box-shadow: inset 0 0 0 1px rgba(15, 23, 42, .08);
    }
    .dark .ds-serp-card { background: #202124; box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .08); }
    .ds-serp-site { display: flex; align-items: center; gap: 10px; }
    .ds-serp-site > span:last-child { display: flex; flex-direction: column; min-width: 0; }
    .ds-serp-favicon {
        display: grid;
        place-items: center;
        width: 26px;
        height: 26px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        color: #4d5156;
        background: #f1f3f4;
    }
    .dark .ds-serp-favicon { color: #bdc1c6; background: #303134; }
    .ds-serp-name { font-size: 13px; color: #202124; }
    .ds-serp-url { font-size: 12px; color: #4d5156; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .dark .ds-serp-name { color: #dadce0; }
    .dark .ds-serp-url { color: #bdc1c6; }
    .ds-serp-title {
        margin-top: 8px;
        font-size: 18px;
        line-height: 1.3;
        color: #1a0dab;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .dark .ds-serp-title { color: #99c3ff; }
    .ds-serp-desc {
        margin-top: 4px;
        font-size: 13px;
        line-height: 1.55;
        color: #4d5156;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .dark .ds-serp-desc { color: #bdc1c6; }
    .ds-serp-desc.is-empty { font-style: italic; opacity: .7; }

    /* ---------- Alternância e responsivo ---------- */
    .ds-switch { display: none; }

    @container ds (max-width: 960px) {
        .ds { grid-template-columns: minmax(0, 1fr); grid-template-rows: auto minmax(0, 1fr); gap: 12px; }
        .ds-switch {
            display: inline-flex;
            justify-self: start;
            gap: 2px;
            padding: 3px;
            border-radius: 10px;
            background: var(--cx-track);
        }
        .ds-switch button {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            color: var(--cx-muted);
        }
        .ds-switch button[aria-selected="true"] { color: var(--cx-text); background: var(--cx-surface); box-shadow: 0 1px 2px rgba(0, 0, 0, .12); }
        .ds-root[data-mode="edit"] .ds-preview { display: none; }
        .ds-root[data-mode="preview"] .ds-editor { display: none; }
    }
    @container ds (max-width: 520px) {
        .ds-form .fi-fo-tabs > div:not(.fi-tabs) { padding: 4px 14px 20px; }
        .ds-hint { display: none; }
        .ds-page select { max-width: 120px; }
        .ds-preview-foot { display: none; }
    }

    @media (prefers-reduced-motion: reduce) {
        .ds *, .ds *::before { transition: none !important; }
    }
</style>
