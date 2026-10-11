{{-- Estilos do Page Builder com Live Preview (CSS puro, independente do build do Tailwind) --}}
<style>
    .pb-shell {
        --pb-gap: 14px;
        --pb-radius: 18px;
        --pb-surface: #ffffff;
        --pb-border: rgba(15, 23, 42, .08);
        --pb-muted: rgb(var(--gray-500));
        --pb-text: rgb(var(--gray-950));
        --pb-accent: var(--primary-600);
        --pb-accent-soft: rgba(var(--primary-500), .1);

        display: grid;
        grid-template-columns: 252px minmax(0, 1fr) 320px;
        gap: var(--pb-gap);
        height: calc(100vh - 7.5rem);
        min-height: 640px;
        width: 100%;
    }
    .dark .pb-shell {
        --pb-surface: rgba(255, 255, 255, .03);
        --pb-border: rgba(255, 255, 255, .08);
        --pb-muted: rgb(var(--gray-400));
        --pb-text: #fff;
        --pb-accent: var(--primary-400);
    }

    .pb-col {
        display: flex;
        flex-direction: column;
        min-height: 0;
        background: var(--pb-surface);
        border: 1px solid var(--pb-border);
        border-radius: var(--pb-radius);
        box-shadow: 0 1px 2px rgba(15, 23, 42, .04), 0 8px 24px -12px rgba(15, 23, 42, .08);
        overflow: hidden;
    }

    .pb-col-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 16px 16px 10px;
    }
    .pb-col-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: -.01em;
        color: var(--pb-text);
    }
    .pb-col-subtitle { margin-top: 2px; font-size: 12px; color: var(--pb-muted); }
    .pb-count {
        display: inline-grid;
        place-items: center;
        min-width: 22px;
        height: 20px;
        padding: 0 6px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        color: rgb(var(--pb-accent));
        background: var(--pb-accent-soft);
    }

    /* ───────── Biblioteca ───────── */
    .pb-search {
        position: relative;
        margin: 0 12px 10px;
    }
    .pb-search-icon {
        position: absolute;
        left: 10px;
        top: 50%;
        width: 16px;
        height: 16px;
        transform: translateY(-50%);
        color: var(--pb-muted);
        pointer-events: none;
    }
    .pb-search input {
        width: 100%;
        height: 36px;
        padding: 0 10px 0 32px;
        font-size: 13px;
        color: var(--pb-text);
        background: rgba(var(--gray-500), .06);
        border: 1px solid transparent;
        border-radius: 10px;
        outline: none;
        transition: border-color .15s, background-color .15s, box-shadow .15s;
    }
    .pb-search input:focus {
        background: transparent;
        border-color: rgb(var(--pb-accent));
        box-shadow: 0 0 0 3px var(--pb-accent-soft);
    }

    .pb-palette {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        padding: 2px 8px 12px;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .pb-palette-item {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        padding: 7px 8px;
        border-radius: 10px;
        text-align: left;
        color: var(--pb-text);
        transition: background-color .15s, transform .15s, box-shadow .15s;
    }
    .pb-palette-item:hover:not(:disabled) {
        background: var(--pb-accent-soft);
        transform: translateX(2px);
    }
    .pb-palette-item:active:not(:disabled) { transform: scale(.98); }
    .pb-palette-icon {
        flex-shrink: 0;
        display: grid;
        place-items: center;
        width: 32px;
        height: 32px;
        border-radius: 9px;
        color: var(--pb-muted);
        background: rgba(var(--gray-500), .08);
        transition: background-color .15s, color .15s;
    }
    .pb-palette-icon svg { width: 16px; height: 16px; }
    .pb-palette-item:hover .pb-palette-icon {
        color: #fff;
        background: rgb(var(--pb-accent));
        box-shadow: 0 6px 14px -6px rgba(var(--primary-600), .7);
    }
    .pb-palette-label { flex: 1; min-width: 0; font-size: 13px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .pb-palette-plus { opacity: 0; color: rgb(var(--pb-accent)); transition: opacity .15s; }
    .pb-palette-plus svg { width: 16px; height: 16px; }
    .pb-palette-item:hover .pb-palette-plus { opacity: 1; }

    /* ───────── Canvas ───────── */
    .pb-col--canvas { background: transparent; box-shadow: none; }
    .pb-toolbar {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        gap: 12px;
        padding: 8px 10px;
        background: var(--pb-surface);
        border-bottom: 1px solid var(--pb-border);
    }
    .dark .pb-toolbar { background: rgba(255, 255, 255, .04); }
    .pb-toolbar-group { display: flex; align-items: center; gap: 6px; }
    .pb-toolbar-group--end { justify-content: flex-end; }

    .pb-status {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 28px;
        padding: 0 12px 0 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        color: rgb(var(--gray-600));
        background: rgba(var(--gray-500), .08);
    }
    .dark .pb-status { color: rgb(var(--gray-300)); }
    .pb-status-dot { position: relative; width: 8px; height: 8px; border-radius: 999px; background: rgb(var(--gray-400)); }
    .pb-status[data-status="ready"] { color: rgb(var(--success-700)); background: rgba(var(--success-500), .12); }
    .dark .pb-status[data-status="ready"] { color: rgb(var(--success-400)); }
    .pb-status[data-status="ready"] .pb-status-dot { background: rgb(var(--success-500)); }
    .pb-status[data-status="ready"] .pb-status-dot::after {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: inherit;
        background: rgba(var(--success-500), .45);
        animation: pb-ping 1.8s cubic-bezier(0, 0, .2, 1) infinite;
    }
    .pb-status[data-status="loading"] { color: rgb(var(--pb-accent)); background: var(--pb-accent-soft); }
    .pb-status[data-status="loading"] .pb-status-dot { background: rgb(var(--pb-accent)); animation: pb-pulse 1s ease-in-out infinite; }
    .pb-status[data-status="error"] { color: rgb(var(--danger-600)); background: rgba(var(--danger-500), .1); }
    .pb-status[data-status="error"] .pb-status-dot { background: rgb(var(--danger-500)); }

    .pb-devices {
        display: inline-flex;
        padding: 3px;
        gap: 2px;
        border-radius: 12px;
        background: rgba(var(--gray-500), .1);
    }
    .pb-device {
        display: grid;
        place-items: center;
        width: 36px;
        height: 30px;
        border-radius: 9px;
        color: var(--pb-muted);
        transition: background-color .15s, color .15s, box-shadow .15s;
    }
    .pb-device svg { width: 18px; height: 18px; }
    .pb-device:hover { color: var(--pb-text); }
    .pb-device.is-active {
        color: rgb(var(--pb-accent));
        background: #fff;
        box-shadow: 0 1px 3px rgba(15, 23, 42, .12);
    }
    .dark .pb-device.is-active { background: rgba(255, 255, 255, .1); }

    .pb-zoom {
        min-width: 44px;
        font-size: 11px;
        font-weight: 600;
        font-variant-numeric: tabular-nums;
        text-align: right;
        color: var(--pb-muted);
    }
    .pb-icon-btn {
        display: grid;
        place-items: center;
        width: 32px;
        height: 32px;
        border-radius: 10px;
        color: var(--pb-muted);
        transition: background-color .15s, color .15s;
    }
    .pb-icon-btn svg { width: 17px; height: 17px; }
    .pb-icon-btn:hover { color: rgb(var(--pb-accent)); background: var(--pb-accent-soft); }
    .pb-spin { animation: pb-spin .8s linear infinite; }

    .pb-viewport {
        position: relative;
        flex: 1;
        min-height: 0;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 12px;
        overflow: hidden;
        background-color: rgb(var(--gray-100));
        background-image: radial-gradient(rgba(var(--gray-400), .45) 1px, transparent 1px);
        background-size: 18px 18px;
    }
    .dark .pb-viewport {
        background-color: rgb(var(--gray-950));
        background-image: radial-gradient(rgba(255, 255, 255, .08) 1px, transparent 1px);
    }

    .pb-device-frame {
        position: relative;
        flex-shrink: 0;
        overflow: hidden;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 0 0 1px rgba(15, 23, 42, .06), 0 24px 48px -16px rgba(15, 23, 42, .25);
        transition: width .35s cubic-bezier(.2, .8, .2, 1), border-radius .35s;
    }
    .pb-device-frame.is-tablet,
    .pb-device-frame.is-mobile {
        margin-top: 12px;
        border-radius: 28px;
        box-shadow: 0 0 0 8px rgb(var(--gray-900)), 0 0 0 9px rgba(255, 255, 255, .08), 0 30px 60px -20px rgba(15, 23, 42, .45);
    }
    .pb-device-inner {
        position: relative;
        transform-origin: 0 0;
    }
    .pb-frame {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        border: 0;
        background: #fff;
        opacity: 0;
        pointer-events: none;
        transition: opacity .18s ease;
    }
    .pb-frame.is-active { opacity: 1; pointer-events: auto; z-index: 1; }

    .pb-loading-bar {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        overflow: hidden;
        background: var(--pb-accent-soft);
    }
    .pb-loading-bar::after {
        content: '';
        position: absolute;
        inset: 0;
        width: 40%;
        background: linear-gradient(90deg, transparent, rgb(var(--pb-accent)), transparent);
        animation: pb-slide 1s ease-in-out infinite;
    }

    .pb-viewport-error {
        position: absolute;
        left: 50%;
        bottom: 24px;
        z-index: 5;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        transform: translateX(-50%);
        border-radius: 12px;
        font-size: 13px;
        color: rgb(var(--danger-700));
        background: #fff;
        box-shadow: 0 10px 30px -10px rgba(15, 23, 42, .3);
    }
    .pb-viewport-error svg { width: 18px; height: 18px; }
    .pb-viewport-error button { font-weight: 600; color: rgb(var(--pb-accent)); }

    /* ───────── Estrutura ───────── */
    .pb-layers {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        padding: 2px 10px 14px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .pb-layer {
        position: relative;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 8px 8px 6px;
        border-radius: 12px;
        border: 1px solid var(--pb-border);
        background: var(--pb-surface);
        cursor: pointer;
        transition: border-color .15s, box-shadow .15s, background-color .15s, transform .15s;
    }
    .pb-layer:hover,
    .pb-layer.is-hovered {
        border-color: rgba(var(--primary-500), .45);
        box-shadow: 0 6px 16px -10px rgba(var(--primary-600), .6);
    }
    .pb-layer.is-selected {
        border-color: rgb(var(--pb-accent));
        background: var(--pb-accent-soft);
        box-shadow: 0 0 0 3px var(--pb-accent-soft);
    }
    .pb-layer.sortable-ghost { opacity: .4; }
    .pb-layer.sortable-chosen { transform: scale(1.02); box-shadow: 0 16px 30px -12px rgba(15, 23, 42, .35); }

    .pb-layer-handle { display: flex; color: var(--pb-muted); cursor: grab; }
    .pb-layer-handle:active { cursor: grabbing; }

    .pb-layer-icon {
        position: relative;
        flex-shrink: 0;
        display: grid;
        place-items: center;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        color: rgb(var(--pb-accent));
        background: var(--pb-accent-soft);
    }
    .pb-layer-icon svg { width: 17px; height: 17px; }
    .pb-layer-index {
        position: absolute;
        right: -5px;
        bottom: -5px;
        display: grid;
        place-items: center;
        min-width: 17px;
        height: 17px;
        padding: 0 4px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
        color: #fff;
        background: rgb(var(--gray-800));
        box-shadow: 0 0 0 2px var(--pb-surface);
    }
    .dark .pb-layer-index { background: rgb(var(--gray-600)); box-shadow: 0 0 0 2px rgb(var(--gray-900)); }

    .pb-layer-text { flex: 1; min-width: 0; display: flex; flex-direction: column; }
    .pb-layer-title { font-size: 13px; font-weight: 600; color: var(--pb-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .pb-layer-sub { font-size: 11.5px; color: var(--pb-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    .pb-layer-actions {
        display: flex;
        align-items: center;
        gap: 2px;
        opacity: 0;
        transform: translateX(4px);
        transition: opacity .15s, transform .15s;
    }
    .pb-layer:hover .pb-layer-actions,
    .pb-layer.is-selected .pb-layer-actions { opacity: 1; transform: none; }
    .pb-layer-locate {
        display: grid;
        place-items: center;
        width: 28px;
        height: 28px;
        border-radius: 8px;
        color: var(--pb-muted);
    }
    .pb-layer-locate svg { width: 16px; height: 16px; }
    .pb-layer-locate:hover { color: rgb(var(--pb-accent)); background: var(--pb-accent-soft); }

    .pb-layers-empty {
        margin: 8px 14px;
        padding: 32px 18px;
        text-align: center;
        border: 1.5px dashed var(--pb-border);
        border-radius: 16px;
    }
    .pb-layers-empty-icon {
        display: grid;
        place-items: center;
        width: 44px;
        height: 44px;
        margin: 0 auto 12px;
        border-radius: 12px;
        color: rgb(var(--pb-accent));
        background: var(--pb-accent-soft);
    }
    .pb-layers-empty-icon svg { width: 22px; height: 22px; }
    .pb-layers-empty-title { font-size: 13px; font-weight: 700; color: var(--pb-text); }
    .pb-layers-empty-text { margin-top: 4px; font-size: 12px; color: var(--pb-muted); }

    /* Enquanto um bloco está sendo editado, as colunas laterais ficam bloqueadas */
    .pb-shell.is-editing .pb-col--palette,
    .pb-shell.is-editing .pb-col--layers { opacity: .55; pointer-events: none; filter: saturate(.6); transition: opacity .2s; }

    /* ───────── Painel de edição (slide-over) ─────────
       O fundo fica transparente e não bloqueia o preview, permitindo rolar a página
       e ver as alterações ao vivo enquanto edita. */
    .fi-modal:has(.pb-inspector) > .fi-modal-close-overlay { background: rgba(15, 23, 42, .04) !important; }
    .fi-modal:has(.pb-inspector),
    .fi-modal:has(.pb-inspector) > div,
    .fi-modal:has(.pb-inspector) > div > div,
    .fi-modal:has(.pb-inspector) > div > div > div { pointer-events: none; }
    .fi-modal:has(.pb-inspector) .pb-inspector { pointer-events: auto; box-shadow: -24px 0 48px -24px rgba(15, 23, 42, .35); }

    /* ───────── Responsivo ───────── */
    @media (max-width: 1535px) {
        .pb-shell { grid-template-columns: 220px minmax(0, 1fr) 292px; }
    }
    @media (max-width: 1279px) {
        .pb-shell { grid-template-columns: 64px minmax(0, 1fr) 280px; }
        .pb-col--palette .pb-col-header,
        .pb-col--palette .pb-search,
        .pb-palette-label,
        .pb-palette-plus { display: none; }
        .pb-palette { padding-top: 10px; align-items: center; }
        .pb-palette-item { justify-content: center; width: auto; padding: 6px; }
        .pb-palette-item:hover:not(:disabled) { transform: none; }
    }
    @media (max-width: 1023px) {
        .pb-shell { display: flex; flex-direction: column; height: auto; min-height: 0; }
        .pb-col--canvas { order: 1; height: 72vh; }
        .pb-col--layers { order: 2; max-height: 60vh; }
        .pb-col--palette { order: 3; max-height: 50vh; }
        .pb-col--palette .pb-col-header,
        .pb-col--palette .pb-search,
        .pb-palette-label,
        .pb-palette-plus { display: initial; }
        .pb-palette { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); align-items: stretch; }
        .pb-palette-item { justify-content: flex-start; width: 100%; padding: 7px 8px; }
        .pb-toolbar { grid-template-columns: auto 1fr auto; }
        .pb-zoom { display: none; }
    }

    @keyframes pb-ping { 75%, 100% { transform: scale(2.2); opacity: 0; } }
    @keyframes pb-pulse { 50% { opacity: .35; } }
    @keyframes pb-spin { to { transform: rotate(360deg); } }
    @keyframes pb-slide { from { transform: translateX(-100%); } to { transform: translateX(250%); } }
</style>
