{{-- Estilos do editor de páginas (CSS puro, independente do build do Tailwind) --}}
<style>
    /* Ganho de tela: menos respiro vertical no wrapper da página do Filament */
    .fi-page.pe-page > section { padding-top: 12px; padding-bottom: 12px; row-gap: 0; }

    .pe-editor {
        --pe-border: rgba(15, 23, 42, .08);
        --pe-muted: rgb(var(--gray-500));
        --pe-text: rgb(var(--gray-950));
        --pe-surface: #fff;
        --pe-soft: rgb(var(--gray-100));
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .dark .pe-editor {
        --pe-border: rgba(255, 255, 255, .08);
        --pe-muted: rgb(var(--gray-400));
        --pe-text: #fff;
        --pe-surface: rgba(255, 255, 255, .03);
        --pe-soft: rgba(255, 255, 255, .06);
    }

    /* O builder ocupa toda a altura disponível abaixo do cabeçalho compacto */
    .pe-editor .pb-shell { height: calc(100dvh - 4rem - 24px - 64px - 12px); min-height: 560px; }

    /* ───────── Cabeçalho ───────── */
    .pe-header {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
        align-items: center;
        gap: 16px;
        min-height: 64px;
        padding: 10px 12px 10px 10px;
        background: var(--pe-surface);
        border: 1px solid var(--pe-border);
        border-radius: 16px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
    }

    .pe-identity { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .pe-back {
        display: grid;
        place-items: center;
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        color: var(--pe-muted);
        transition: background .15s, color .15s;
    }
    .pe-back svg { width: 18px; height: 18px; }
    .pe-back:hover { color: var(--pe-text); background: var(--pe-soft); }

    .pe-titles { min-width: 0; }
    .pe-title-row { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .pe-title {
        min-width: 0;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -.01em;
        color: var(--pe-text);
    }
    .pe-url {
        display: block;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        margin-top: 1px;
        font-size: 12px;
        color: var(--pe-muted);
    }
    .pe-url-host { opacity: .7; }

    /* Status do rascunho */
    .pe-status {
        --pe-status: var(--gray-500);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
        padding: 2px 9px 2px 7px;
        font-size: 11.5px;
        font-weight: 600;
        line-height: 18px;
        white-space: nowrap;
        border-radius: 999px;
        color: rgb(var(--pe-status));
        background: rgba(var(--pe-status), .12);
    }
    .pe-status-dot { width: 7px; height: 7px; border-radius: 999px; background: currentColor; }
    .pe-status[data-status="published"] { --pe-status: var(--success-600); }
    .pe-status[data-status="draft"] { --pe-status: var(--primary-600); }
    .pe-status[data-status="dirty"] { --pe-status: var(--warning-600); }
    .pe-status[data-status="error"] { --pe-status: var(--danger-600); }
    .pe-status[data-status="saving"] .pe-status-dot { animation: pe-pulse 1s ease-in-out infinite; }
    .dark .pe-status[data-status="published"] { --pe-status: var(--success-400); }
    .dark .pe-status[data-status="draft"] { --pe-status: var(--primary-400); }
    .dark .pe-status[data-status="dirty"] { --pe-status: var(--warning-400); }
    .dark .pe-status[data-status="error"] { --pe-status: var(--danger-400); }
    @keyframes pe-pulse { 50% { opacity: .3; } }

    /* Abas */
    .pe-tabs {
        display: inline-flex;
        gap: 2px;
        padding: 3px;
        border-radius: 12px;
        background: var(--pe-soft);
    }
    .pe-tab {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
        border-radius: 9px;
        color: var(--pe-muted);
        transition: background .15s, color .15s, box-shadow .15s;
    }
    .pe-tab svg { width: 16px; height: 16px; }
    .pe-tab:hover { color: var(--pe-text); }
    .pe-tab.is-active {
        color: var(--pe-text);
        background: var(--pe-surface);
        box-shadow: 0 1px 2px rgba(15, 23, 42, .1), 0 0 0 1px var(--pe-border);
    }
    .dark .pe-tab.is-active { background: rgb(var(--gray-800)); }

    .pe-actions { display: flex; justify-content: flex-end; min-width: 0; }

    @media (max-width: 1279px) {
        .pe-header { grid-template-columns: minmax(0, 1fr) auto; }
        .pe-tabs { grid-column: 1 / -1; grid-row: 2; justify-self: start; }
        .pe-editor .pb-shell { height: calc(100dvh - 4rem - 24px - 112px - 12px); }
    }
    @media (max-width: 639px) {
        .pe-tab span { display: none; }
        .pe-url { display: none; }
    }

    /* ───────── Aba Configurações e SEO ───────── */
    .pe-settings { max-width: 1120px; margin-inline: auto; width: 100%; }
    .pe-settings-aside { position: sticky; top: 80px; }
</style>
