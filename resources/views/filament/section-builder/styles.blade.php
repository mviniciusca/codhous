{{-- Construtor de seções (CSS puro). Prefixo sb-; tokens vêm de filament/admin/ui --}}
@once
<style>
    .sb-host { container: sb / inline-size; }
    .sb {
        --sb-gap: 14px;
        --sb-accent: var(--primary-600);
        --sb-accent-soft: rgba(var(--primary-500), .1);
        display: grid;
        gap: var(--sb-gap);
        grid-template-columns: minmax(0, 1fr);
        grid-template-areas: "canvas" "library" "inspector";
    }
    .dark .sb { --sb-accent: var(--primary-400); }

    .sb-col {
        position: relative;
        min-width: 0;
        border-radius: var(--cx-radius);
        border: 1px solid var(--cx-border);
        background: var(--cx-surface);
        box-shadow: var(--cx-shadow);
    }
    .sb-col--library { grid-area: library; max-height: 460px; overflow-y: auto; overscroll-behavior: contain; scrollbar-width: thin; }
    .sb-col--canvas { grid-area: canvas; height: 420px; overflow: hidden; }
    .sb-col--inspector { grid-area: inspector; overflow: hidden; }

    @container sb (min-width: 760px) {
        .sb { grid-template-columns: 300px minmax(0, 1fr); grid-template-areas: "canvas canvas" "library inspector"; }
        .sb-col--canvas { height: 480px; }
        .sb-col--library { max-height: 760px; }
    }
    /* Desktop: três colunas que ocupam a altura da tela, cada uma com rolagem própria */
    @container sb (min-width: 1020px) {
        .sb {
            grid-template-columns: 272px minmax(0, 1fr) 368px;
            grid-template-areas: "library canvas inspector";
            height: max(620px, calc(100dvh - 10.25rem));
        }
        .sb-col--library, .sb-col--canvas, .sb-col--inspector { height: auto; max-height: none; min-height: 0; }
        .sb-col--inspector { display: flex; flex-direction: column; }
    }
    @container sb (min-width: 1280px) {
        .sb { grid-template-columns: 296px minmax(0, 1fr) 400px; }
    }
    @container sb (min-width: 1560px) {
        .sb { grid-template-columns: 320px minmax(0, 1fr) 440px; }
    }

    /* ───────── Canvas ───────── */
    .sb-canvas { container: sbc / inline-size; display: flex; flex-direction: column; height: 100%; background: var(--cx-surface); }
    .sb-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 8px 10px;
        border-bottom: 1px solid var(--cx-border);
    }
    /* Prévia estreita: só ícones na barra */
    @container sbc (max-width: 620px) {
        .sb-seg-btn:not(.sb-seg-btn--icon) span, .sb-status span:last-child, .sb-zoom .sb-icon-btn { display: none; }
        .sb-seg-btn:not(.sb-seg-btn--icon) { padding: 0 9px; }
        .sb-status { padding: 0 9px; }
        .sb-seg-btn--icon { width: 30px; }
    }
    .sb-toolbar-group { display: flex; align-items: center; gap: 6px; min-width: 0; }
    .sb-toolbar-group--end { justify-content: flex-end; }

    .sb-seg { display: inline-flex; padding: 3px; gap: 2px; border-radius: 11px; background: var(--cx-track); }
    .sb-seg.is-disabled { opacity: .45; pointer-events: none; }
    .sb-seg-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 28px;
        padding: 0 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        color: var(--cx-muted);
        transition: background-color .15s, color .15s, box-shadow .15s;
    }
    .sb-seg-btn svg { width: 16px; height: 16px; flex: none; }
    .sb-seg-btn--icon { padding: 0; width: 34px; justify-content: center; }
    .sb-seg-btn--icon svg { width: 18px; height: 18px; }
    .sb-seg-btn:hover { color: var(--cx-text); }
    .sb-seg-btn.is-active { color: rgb(var(--sb-accent)); background: #fff; box-shadow: 0 1px 3px rgba(15, 23, 42, .12); }
    .dark .sb-seg-btn.is-active { background: rgba(255, 255, 255, .1); box-shadow: none; }
    .sb-seg-btn:focus-visible { outline: 2px solid rgb(var(--sb-accent)); outline-offset: 1px; }

    .sb-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        height: 26px;
        padding: 0 10px 0 9px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 600;
        white-space: nowrap;
        color: var(--cx-muted);
        background: var(--cx-track);
    }
    .sb-status-dot { position: relative; width: 7px; height: 7px; border-radius: 999px; background: rgb(var(--gray-400)); }
    .sb-status[data-status="ready"] { color: rgb(var(--success-700)); background: rgba(var(--success-500), .12); }
    .dark .sb-status[data-status="ready"] { color: rgb(var(--success-400)); }
    .sb-status[data-status="ready"] .sb-status-dot { background: rgb(var(--success-500)); }
    .sb-status[data-status="loading"] { color: rgb(var(--sb-accent)); background: var(--sb-accent-soft); }
    .sb-status[data-status="loading"] .sb-status-dot { background: rgb(var(--sb-accent)); animation: sb-pulse 1s ease-in-out infinite; }
    .sb-status[data-status="error"] { color: rgb(var(--danger-600)); background: rgba(var(--danger-500), .1); }
    .sb-status[data-status="error"] .sb-status-dot { background: rgb(var(--danger-500)); }

    .sb-zoom { display: inline-flex; align-items: center; gap: 0; margin-right: 2px; }
    .sb-zoom-value {
        min-width: 42px;
        height: 28px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 600;
        font-variant-numeric: tabular-nums;
        color: var(--cx-muted);
    }
    .sb-zoom-value:hover { color: var(--cx-text); background: var(--cx-hover); }
    .sb-icon-btn {
        display: grid;
        place-items: center;
        flex: none;
        width: 30px;
        height: 30px;
        border-radius: 9px;
        color: var(--cx-muted);
        transition: background-color .15s, color .15s;
    }
    .sb-icon-btn svg { width: 17px; height: 17px; }
    .sb-icon-btn:hover, .sb-icon-btn.is-on { color: rgb(var(--sb-accent)); background: var(--sb-accent-soft); }
    .sb-icon-btn:focus-visible { outline: 2px solid rgb(var(--sb-accent)); outline-offset: 1px; }
    .sb-spin { animation: sb-spin .8s linear infinite; }

    .sb-viewport {
        position: relative;
        flex: 1;
        min-height: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 12px;
        overflow: auto;
        background-color: rgb(var(--gray-100));
        background-image: radial-gradient(rgba(var(--gray-400), .45) 1px, transparent 1px);
        background-size: 18px 18px;
    }
    .dark .sb-viewport { background-color: rgb(var(--gray-950)); background-image: radial-gradient(rgba(255, 255, 255, .07) 1px, transparent 1px); }

    .sb-device-frame {
        position: relative;
        flex-shrink: 0;
        overflow: hidden;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 0 0 1px rgba(15, 23, 42, .06), 0 24px 48px -16px rgba(15, 23, 42, .3);
        transition: width .3s cubic-bezier(.2, .8, .2, 1), border-radius .3s;
    }
    .sb-viewport.is-zoomed { align-items: flex-start; justify-content: flex-start; }
    .sb-viewport.is-zoomed .sb-device-frame { margin: auto; }
    .sb-device-frame.is-tablet, .sb-device-frame.is-mobile {
        border-radius: 26px;
        box-shadow: 0 0 0 8px rgb(var(--gray-900)), 0 0 0 9px rgba(255, 255, 255, .08), 0 30px 60px -20px rgba(15, 23, 42, .45);
    }
    .sb-device-inner { position: relative; transform-origin: 0 0; }
    .sb-frame { position: absolute; inset: 0; width: 100%; height: 100%; border: 0; background: #fff; opacity: 0; pointer-events: none; transition: opacity .18s ease; }
    .sb-frame.is-active { opacity: 1; pointer-events: auto; z-index: 1; }

    .sb-loading-bar { position: absolute; top: 0; left: 0; right: 0; z-index: 2; height: 2px; overflow: hidden; background: var(--sb-accent-soft); }
    .sb-loading-bar::after {
        content: '';
        position: absolute;
        inset: 0;
        width: 40%;
        background: linear-gradient(90deg, transparent, rgb(var(--sb-accent)), transparent);
        animation: sb-slide 1s ease-in-out infinite;
    }

    /* Estrutura (skeleton ampliado) */
    .sb-skeleton {
        position: absolute;
        inset: 0;
        z-index: 3;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 16px;
        padding: 24px;
        background: inherit;
        background-color: rgb(var(--gray-100));
    }
    .dark .sb-skeleton { background-color: rgb(var(--gray-950)); }
    .sb-skeleton-stage { width: calc(320px * var(--k, 1)); height: calc(170px * var(--k, 1)); }
    .sb-skeleton-thumb {
        --stp-thumb-h: 170px;
        width: 320px;
        transform: scale(var(--k, 1));
        transform-origin: 0 0;
        background: var(--cx-surface);
        box-shadow: 0 24px 48px -24px rgba(15, 23, 42, .35);
    }
    .sb-skeleton-note {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        max-width: 520px;
        padding: 8px 12px;
        border-radius: 10px;
        font-size: 12.5px;
        color: var(--cx-muted);
        background: var(--cx-surface);
        border: 1px solid var(--cx-border);
    }
    .sb-skeleton-note svg { width: 16px; height: 16px; flex: none; color: rgb(var(--sb-accent)); }

    .sb-empty {
        position: absolute;
        inset: 0;
        z-index: 4;
        display: grid;
        align-content: center;
        justify-items: center;
        gap: 6px;
        padding: 24px;
        text-align: center;
    }
    .sb-empty-icon {
        display: grid;
        place-items: center;
        width: 52px;
        height: 52px;
        margin-bottom: 8px;
        border-radius: 16px;
        color: rgb(var(--sb-accent));
        background: var(--sb-accent-soft);
        border: 1px solid rgba(var(--primary-500), .25);
    }
    .sb-empty-icon svg { width: 26px; height: 26px; }
    .sb-empty-title { font-size: 15px; font-weight: 600; color: var(--cx-text); }
    .sb-empty-desc { max-width: 340px; font-size: 13px; line-height: 1.5; color: var(--cx-muted); }

    .sb-viewport-error {
        position: absolute;
        left: 50%;
        bottom: 20px;
        z-index: 5;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 14px;
        transform: translateX(-50%);
        border-radius: 12px;
        font-size: 13px;
        white-space: nowrap;
        color: rgb(var(--danger-600));
        background: var(--cx-surface);
        border: 1px solid var(--cx-border);
        box-shadow: 0 10px 30px -10px rgba(15, 23, 42, .35);
    }
    .sb-viewport-error svg { width: 18px; height: 18px; }
    .sb-viewport-error button { font-weight: 600; color: rgb(var(--sb-accent)); }

    /* Prévia expandida */
    .sb-col--canvas:has(.sb-canvas.is-expanded) { overflow: visible; z-index: 40; }
    .sb-canvas.is-expanded {
        position: fixed;
        inset: 16px;
        z-index: 40;
        height: auto;
        border-radius: var(--cx-radius);
        border: 1px solid var(--cx-border);
        overflow: hidden;
        box-shadow: 0 0 0 100vmax rgba(2, 6, 23, .6), 0 30px 80px -20px rgba(0, 0, 0, .6);
    }
    html.sb-noscroll { overflow: hidden; }

    /* ───────── Inspetor ───────── */
    .sb-inspector { display: flex; flex-direction: column; height: 100%; min-height: 0; }
    .sb-steps {
        display: flex;
        justify-content: space-between;
        gap: 2px;
        padding: 8px;
        border-bottom: 1px solid var(--cx-border);
    }
    .sb-step { flex: 0 1 auto; min-width: 0; }
    .sb-step-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        padding: 7px 8px;
        border-radius: 10px;
        text-align: start;
        transition: background-color .15s;
    }
    .sb-step-btn:not(:disabled):hover { background: var(--cx-hover); }
    .sb-step-btn:disabled { cursor: default; }
    .sb-step-btn:focus-visible { outline: 2px solid rgb(var(--sb-accent)); outline-offset: -2px; }
    .sb-step-num {
        display: grid;
        place-items: center;
        flex: none;
        width: 24px;
        height: 24px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 700;
        color: var(--cx-muted);
        border: 1.5px solid var(--cx-border-strong);
    }
    .sb-step-num svg { width: 13px; height: 13px; }
    .sb-step-text { display: grid; min-width: 0; }
    .sb-step-label { overflow: hidden; font-size: 12px; font-weight: 600; line-height: 1.3; white-space: nowrap; text-overflow: ellipsis; color: var(--cx-muted); }
    .sb-step-desc { overflow: hidden; font-size: 11px; line-height: 1.3; white-space: nowrap; text-overflow: ellipsis; color: var(--cx-subtle); }
    .sb-step.is-active .sb-step-btn { background: var(--sb-accent-soft); }
    .sb-step.is-active .sb-step-num { color: #fff; border-color: transparent; background: rgb(var(--primary-600)); box-shadow: 0 4px 12px -4px rgba(var(--primary-600), .8); }
    .sb-step.is-active .sb-step-label { color: rgb(var(--sb-accent)); }
    .sb-step.is-done .sb-step-num { color: rgb(var(--sb-accent)); border-color: rgba(var(--primary-500), .45); background: var(--sb-accent-soft); }
    .sb-step.is-done .sb-step-label { color: var(--cx-text); }
    @container sb (max-width: 1559px) { .sb-step-desc { display: none; } }

    .sb-inspector-body { flex: 1; min-height: 0; overflow-y: auto; overscroll-behavior: contain; scrollbar-width: thin; }
    .sb-inspector .fi-fo-wizard-step.fi-active { padding: 16px; margin: 0; }

    .sb-inspector-foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 12px 14px;
        border-top: 1px solid var(--cx-border);
        background: var(--cx-surface-2);
    }
    .sb-foot-start, .sb-foot-end { display: flex; align-items: center; gap: 8px; }
    .sb-foot-end { flex-wrap: wrap; justify-content: flex-end; }
    .sb-foot-submit { display: inline-flex; flex-wrap: wrap; gap: 8px; justify-content: flex-end; }

    /* Cabeçalho do componente (etapa 1) */
    .sb-summary {
        display: flex;
        gap: 12px;
        padding: 14px;
        border-radius: 12px;
        border: 1px solid rgba(var(--primary-500), .25);
        background: linear-gradient(180deg, rgba(var(--primary-500), .08), rgba(var(--primary-500), .02));
    }
    .sb-summary--empty { border-style: dashed; border-color: var(--cx-border-strong); background: transparent; }
    .sb-summary-icon {
        display: grid;
        place-items: center;
        flex: none;
        width: 40px;
        height: 40px;
        border-radius: 11px;
        color: rgb(var(--sb-accent));
        background: var(--sb-accent-soft);
        border: 1px solid rgba(var(--primary-500), .25);
    }
    .sb-summary--empty .sb-summary-icon { color: var(--cx-subtle); background: var(--cx-track); border-color: transparent; }
    .sb-summary-icon svg { width: 20px; height: 20px; }
    .sb-summary-text { display: grid; gap: 3px; min-width: 0; }
    .sb-summary-title { font-size: 15px; font-weight: 600; color: var(--cx-text); }
    .sb-summary-desc { font-size: 12.5px; line-height: 1.5; color: var(--cx-muted); }
    .sb-summary-tags { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 6px; }
    .sb-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 1px 7px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        line-height: 18px;
        color: var(--cx-text);
        background: var(--cx-track);
    }
    .sb-tag svg { width: 11px; height: 11px; }
    .sb-tag--soft { font-weight: 500; color: var(--cx-muted); }
    code.sb-tag { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; }

    @keyframes sb-spin { to { transform: rotate(360deg); } }
    @keyframes sb-pulse { 50% { opacity: .35; } }
    @keyframes sb-slide { from { transform: translateX(-100%); } to { transform: translateX(250%); } }

    @media (prefers-reduced-motion: reduce) {
        .sb-device-frame, .sb-frame, .sb-seg-btn { transition: none; }
        .sb-spin, .sb-loading-bar::after, .sb-status-dot { animation: none !important; }
    }
</style>
@endonce
