{{-- Estilos do quadro de orçamentos (CSS puro, independente do build do Tailwind) --}}
<style>
    .bb-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 8px 16px; }
    .bb-search { flex: 1 1 320px; max-width: 480px; }
    .bb-hint { font-size: 12px; color: rgb(var(--gray-500)); }

    .bb-board {
        --bb-border: rgba(15, 23, 42, .08);
        --bb-col: rgba(var(--gray-500), .06);
        --bb-card: #fff;
        display: grid;
        grid-template-columns: repeat(4, minmax(230px, 1fr));
        gap: 14px;
        overflow-x: auto;
        padding-bottom: 8px;
        align-items: start;
    }
    .dark .bb-board {
        --bb-border: rgba(255, 255, 255, .1);
        --bb-col: rgba(255, 255, 255, .03);
        --bb-card: rgb(var(--gray-900));
    }

    .bb-col {
        --bb-accent: var(--gray-400);
        display: flex; flex-direction: column;
        min-height: 240px; max-height: calc(100vh - 15rem);
        background: var(--bb-col);
        border: 1px solid var(--bb-border);
        border-top: 3px solid rgb(var(--bb-accent));
        border-radius: 12px;
        transition: background .15s, box-shadow .15s;
    }
    .bb-col--warning { --bb-accent: var(--warning-500); }
    .bb-col--primary { --bb-accent: var(--primary-500); }
    .bb-col--success { --bb-accent: var(--success-500); }
    .bb-col--gray { --bb-accent: var(--gray-400); }
    .bb-col--over { background: rgba(var(--bb-accent), .1); box-shadow: inset 0 0 0 2px rgba(var(--bb-accent), .5); }

    .bb-col-head { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 12px 14px 10px; }
    .bb-col-title { display: flex; align-items: center; gap: 8px; min-width: 0; }
    .bb-col-title h2 { margin: 0; font-size: 14px; font-weight: 600; color: rgb(var(--gray-950)); }
    .dark .bb-col-title h2 { color: #fff; }
    .bb-dot { width: 8px; height: 8px; border-radius: 999px; background: rgb(var(--bb-accent)); flex-shrink: 0; }
    .bb-count { font-size: 12px; font-weight: 600; padding: 0 7px; border-radius: 999px; background: rgba(var(--gray-500), .14); color: rgb(var(--gray-600)); }
    .dark .bb-count { color: rgb(var(--gray-300)); }
    .bb-sum { font-size: 12px; font-weight: 600; color: rgb(var(--gray-500)); font-variant-numeric: tabular-nums; white-space: nowrap; }

    .bb-cards { display: flex; flex-direction: column; gap: 8px; padding: 0 10px 12px; overflow-y: auto; }
    .bb-card {
        padding: 10px 12px;
        background: var(--bb-card);
        border: 1px solid var(--bb-border);
        border-radius: 10px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .05);
        cursor: grab;
        transition: box-shadow .15s, opacity .15s, transform .15s;
    }
    .bb-card:hover { box-shadow: 0 4px 14px -6px rgba(15, 23, 42, .2); }
    .bb-card:active { cursor: grabbing; }
    .bb-card--dragging { opacity: .45; transform: rotate(1deg); }
    .bb-col--gray .bb-card { opacity: .75; }

    .bb-card-top { display: flex; align-items: center; gap: 8px; }
    .bb-code { font-size: 12px; font-weight: 600; color: rgb(var(--primary-600)); font-variant-numeric: tabular-nums; }
    .dark .bb-code { color: rgb(var(--primary-400)); }
    .bb-code:hover { text-decoration: underline; }
    .bb-age { flex: 1; font-size: 11px; color: rgb(var(--gray-500)); }
    .bb-menu { display: inline-flex; padding: 2px; border-radius: 6px; color: rgb(var(--gray-400)); }
    .bb-menu:hover { color: rgb(var(--gray-700)); background: rgba(var(--gray-500), .1); }
    .bb-menu svg { width: 18px; height: 18px; }

    .bb-card-link { display: flex; flex-direction: column; gap: 1px; margin-top: 4px; color: inherit; }
    .bb-customer { font-size: 14px; font-weight: 600; color: rgb(var(--gray-950)); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .dark .bb-customer { color: #fff; }
    .bb-place, .bb-items { font-size: 12px; color: rgb(var(--gray-500)); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    .bb-card-foot { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-top: 8px; padding-top: 8px; border-top: 1px dashed var(--bb-border); }
    .bb-total { font-size: 14px; font-weight: 700; color: rgb(var(--gray-950)); font-variant-numeric: tabular-nums; }
    .dark .bb-total { color: #fff; }
    .bb-flags { display: flex; align-items: center; gap: 4px; flex-wrap: wrap; justify-content: flex-end; }
    .bb-flag {
        display: inline-flex; align-items: center; gap: 3px;
        padding: 1px 6px; border-radius: 999px;
        font-size: 11px; font-weight: 600;
        color: rgb(var(--gray-500)); background: rgba(var(--gray-500), .1);
    }
    .bb-flag svg { width: 13px; height: 13px; }
    .bb-flag--ok { color: rgb(var(--success-600)); background: rgba(var(--success-500), .12); }
    .bb-flag--warn { color: rgb(var(--warning-700)); background: rgba(var(--warning-500), .16); }
    .dark .bb-flag--ok { color: rgb(var(--success-400)); }
    .dark .bb-flag--warn { color: rgb(var(--warning-400)); }

    .bb-empty { margin: 8px 4px; font-size: 13px; color: rgb(var(--gray-500)); text-align: center; }

    @media (max-width: 1100px) {
        .bb-board { grid-template-columns: repeat(4, minmax(260px, 80vw)); }
        .bb-col { max-height: none; }
    }
</style>
