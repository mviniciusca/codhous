{{-- Estilos da visão geral (CSS puro, independente do build do Tailwind). Prefixo cx- --}}
<style>
    .cx {
        display: grid;
        gap: 20px;
    }

    /* ---------- Faixa de indicadores ---------- */
    .cx-kpis {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        background: var(--cx-surface);
        border: 1px solid var(--cx-border);
        border-radius: var(--cx-radius);
        box-shadow: var(--cx-shadow);
        overflow: hidden;
    }
    .cx-kpi {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 0;
        padding: 18px 20px 20px;
        color: inherit;
        text-decoration: none;
        transition: background-color .15s ease;
    }
    .cx-kpi + .cx-kpi { border-left: 1px solid var(--cx-border); }
    .cx-kpi:hover { background: var(--cx-hover); }
    .cx-kpi::before {
        content: "";
        position: absolute;
        inset: 0 auto auto 0;
        width: 100%;
        height: 2px;
        background: rgb(var(--tone));
        opacity: 0;
        transition: opacity .15s ease;
    }
    .cx-kpi:hover::before { opacity: .9; }
    .cx-kpi-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 500;
        color: var(--cx-muted);
    }
    .cx-kpi-go {
        width: 14px; height: 14px;
        margin-left: auto;
        opacity: 0;
        transform: translate(-2px, 2px);
        transition: opacity .15s ease, transform .15s ease;
    }
    .cx-kpi:hover .cx-kpi-go,
    .cx-kpi:focus-visible .cx-kpi-go { opacity: .8; transform: none; }
    .cx-kpi-value {
        font-size: 28px;
        line-height: 1.15;
        font-weight: 600;
        letter-spacing: -.02em;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .cx-kpi-note { font-size: 13px; color: var(--cx-muted); }
    .cx-tone-danger .cx-kpi-note,
    .cx-tone-warning .cx-kpi-note { color: var(--tone-fg); }

    .cx-spark {
        display: flex;
        align-items: flex-end;
        gap: 3px;
        height: 28px;
        margin-top: 6px;
    }
    .cx-spark-bar {
        flex: 1;
        border-radius: 2px;
        background: rgba(var(--gray-400), .45);
    }
    .cx-spark-bar.is-empty { background: var(--cx-track); }
    .cx-spark-bar.is-last { background: rgb(var(--tone)); }

    .cx-delta {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: 6px;
        font-size: 12px;
        font-weight: 600;
        color: var(--cx-muted);
    }
    .cx-delta.is-up { color: rgb(var(--success-600)); }
    .cx-delta.is-down { color: rgb(var(--danger-600)); }
    .dark .cx-delta.is-up { color: rgb(var(--success-400)); }
    .dark .cx-delta.is-down { color: rgb(var(--danger-400)); }
    .cx-delta-label { font-weight: 400; color: var(--cx-muted); }

    .cx-meter {
        display: block;
        height: 6px;
        margin-top: 10px;
        border-radius: 999px;
        background: var(--cx-track);
        overflow: hidden;
    }
    .cx-meter > span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: rgb(var(--tone));
    }

    /* ---------- Grade ---------- */
    .cx-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 380px);
        gap: 20px;
        align-items: start;
    }

    /* ---------- Painel ---------- */
    .cx-panel {
        min-width: 0;
        background: var(--cx-surface);
        border: 1px solid var(--cx-border);
        border-radius: var(--cx-radius);
        box-shadow: var(--cx-shadow);
    }
    .cx-panel-head {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        border-bottom: 1px solid var(--cx-border);
    }
    .cx-panel-icon {
        display: grid;
        place-items: center;
        width: 36px;
        height: 36px;
        flex: none;
        border-radius: 10px;
        color: rgb(var(--primary-600));
        background: rgba(var(--primary-500), .1);
        box-shadow: inset 0 0 0 1px rgba(var(--primary-500), .14);
    }
    .dark .cx-panel-icon { color: rgb(var(--primary-300)); background: rgba(var(--primary-500), .12); }
    .cx-panel-titles { min-width: 0; flex: 1; }
    .cx-panel-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 15px;
        font-weight: 600;
        line-height: 1.3;
    }
    .cx-panel-count {
        min-width: 20px;
        padding: 1px 7px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
        color: rgb(var(--danger-600));
        background: rgba(var(--danger-500), .12);
    }
    .dark .cx-panel-count { color: rgb(var(--danger-300)); background: rgba(var(--danger-500), .18); }
    .cx-panel-desc { margin-top: 2px; font-size: 13px; color: var(--cx-muted); }
    /* ---------- Agenda ---------- */
    .cx-week {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 8px;
        padding: 16px 20px;
        border-bottom: 1px solid var(--cx-border);
    }
    .cx-week-day {
        display: flex;
        flex-direction: column;
        gap: 4px;
        min-width: 0;
        padding: 10px 12px;
        border: 1px solid var(--cx-border);
        border-radius: 10px;
        text-align: left;
        background: var(--cx-surface-2);
        color: inherit;
        cursor: pointer;
        transition: border-color .15s ease, background-color .15s ease;
    }
    .cx-week-day:hover { border-color: var(--cx-border-strong); }
    .cx-week-day.is-active {
        border-color: rgba(var(--primary-500), .7);
        background: rgba(var(--primary-500), .08);
        box-shadow: inset 0 0 0 1px rgba(var(--primary-500), .35);
    }
    .cx-week-name {
        font-size: 12px;
        font-weight: 500;
        color: var(--cx-muted);
    }
    .cx-week-day.is-today .cx-week-name { color: rgb(var(--primary-600)); font-weight: 600; }
    .dark .cx-week-day.is-today .cx-week-name { color: rgb(var(--primary-400)); }
    .cx-week-num { font-size: 20px; font-weight: 600; line-height: 1.1; letter-spacing: -.01em; }
    .cx-week-load {
        display: block;
        height: 4px;
        margin: 4px 0 2px;
        border-radius: 999px;
        background: var(--cx-track);
        overflow: hidden;
    }
    .cx-week-load > span { display: block; height: 100%; border-radius: inherit; background: rgb(var(--primary-500)); }
    .cx-week-load.is-idle > span { display: none; }
    .cx-week-meta {
        font-size: 12px;
        color: var(--cx-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .cx-week-meta.is-idle { color: var(--cx-subtle); }

    .cx-day-head {
        display: flex;
        flex-wrap: wrap;
        align-items: baseline;
        justify-content: space-between;
        gap: 4px 12px;
        padding: 14px 20px 6px;
        font-size: 13px;
        color: var(--cx-muted);
    }
    .cx-day-head strong { font-weight: 600; color: var(--cx-text); }

    .cx-delivery {
        display: grid;
        grid-template-columns: 76px minmax(0, 1.3fr) minmax(0, 1fr) 72px 132px 16px;
        align-items: center;
        gap: 16px;
        padding: 12px 20px;
        color: inherit;
        text-decoration: none;
        border-top: 1px solid var(--cx-border);
        transition: background-color .15s ease;
    }
    .cx-day-head + .cx-delivery { border-top-color: transparent; }
    .cx-delivery:hover { background: var(--cx-hover); }
    .cx-delivery:last-child { border-radius: 0 0 var(--cx-radius) var(--cx-radius); }

    .cx-period {
        justify-self: start;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        color: var(--cx-muted);
        background: var(--cx-track);
    }
    .cx-period.is-manha { color: rgb(var(--warning-700)); background: rgba(var(--warning-500), .14); }
    .cx-period.is-tarde { color: rgb(var(--primary-700)); background: rgba(var(--primary-500), .12); }
    .dark .cx-period.is-manha { color: rgb(var(--warning-300)); }
    .dark .cx-period.is-tarde { color: rgb(var(--primary-300)); }

    .cx-delivery-main,
    .cx-delivery-items { display: flex; flex-direction: column; gap: 3px; min-width: 0; }
    .cx-delivery-customer {
        font-size: 14px;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .cx-delivery-sub {
        display: flex;
        align-items: center;
        gap: 6px;
        min-width: 0;
        font-size: 12px;
        color: var(--cx-muted);
        white-space: nowrap;
        overflow: hidden;
    }
    .cx-delivery-items > span:first-child {
        font-size: 13px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .cx-delivery-volume { font-size: 15px; font-weight: 600; text-align: right; white-space: nowrap; }
    .cx-tag {
        align-self: flex-start;
        padding: 1px 6px;
        border-radius: 5px;
        font-size: 11px;
        font-weight: 500;
        color: var(--cx-muted);
        box-shadow: inset 0 0 0 1px var(--cx-border-strong);
    }
    .cx-place { min-width: 0; overflow: hidden; text-overflow: ellipsis; }
    .cx-sep { width: 3px; height: 3px; border-radius: 999px; background: var(--cx-subtle); flex: none; }
    .cx-code { font-size: 12px; font-weight: 500; letter-spacing: .01em; color: var(--cx-muted); }
    .cx-code-link { color: rgb(var(--primary-600)); font-weight: 600; text-decoration: none; white-space: nowrap; }
    .cx-code-link:hover { text-decoration: underline; text-underline-offset: 3px; }
    .dark .cx-code-link { color: rgb(var(--primary-400)); }
    .cx-row-go { width: 16px; height: 16px; color: var(--cx-subtle); transition: transform .15s ease, color .15s ease; }
    a:hover > .cx-row-go { color: var(--cx-text); transform: translateX(2px); }

    /* ---------- Pendências ---------- */
    .cx-alerts { display: grid; gap: 8px; padding: 12px; }
    .cx-alert {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border: 1px solid var(--cx-border);
        border-radius: 10px;
        color: inherit;
        text-decoration: none;
        background: var(--cx-surface-2);
        transition: border-color .15s ease, background-color .15s ease;
    }
    .cx-alert:hover { border-color: rgba(var(--tone), .45); background: rgba(var(--tone), .06); }
    .cx-alert-icon {
        display: grid;
        place-items: center;
        width: 36px;
        height: 36px;
        flex: none;
        border-radius: 9px;
        color: var(--tone-fg);
        background: rgba(var(--tone), .13);
    }
    .cx-alert-text { display: flex; flex-direction: column; gap: 2px; min-width: 0; flex: 1; }
    .cx-alert-title { font-size: 14px; font-weight: 500; }
    .cx-alert-title b { font-weight: 700; color: var(--tone-fg); }
    .cx-alert-note { font-size: 12px; color: var(--cx-muted); }

    /* ---------- Tabela ---------- */
    .cx-table-wrap { overflow-x: auto; }
    .cx-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .cx-table th {
        padding: 10px 20px;
        font-size: 12px;
        font-weight: 500;
        text-align: left;
        white-space: nowrap;
        color: var(--cx-muted);
        background: var(--cx-surface-2);
        border-bottom: 1px solid var(--cx-border);
    }
    .cx-table td { padding: 12px 20px; vertical-align: middle; border-top: 1px solid var(--cx-border); }
    .cx-table tbody tr:first-child td { border-top: 0; }
    .cx-table tbody tr { transition: background-color .15s ease; }
    .cx-table tbody tr:hover { background: var(--cx-hover); }
    .cx-table .cx-num { text-align: right; white-space: nowrap; }
    .cx-cell-main { display: block; font-weight: 500; }
    .cx-cell-sub { display: block; margin-top: 2px; font-size: 12px; color: var(--cx-muted); white-space: nowrap; }
    .cx-cell-clip { max-width: 0; width: 34%; }
    .cx-cell-clip > span { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .cx-cell-clip > .cx-cell-sub:only-child { margin-top: 0; }

    /* ---------- Ranking ---------- */
    .cx-rank { display: grid; gap: 16px; padding: 18px 20px 20px; }
    .cx-rank-row { display: grid; gap: 6px; }
    .cx-rank-head { display: flex; justify-content: space-between; gap: 12px; font-size: 13px; }
    .cx-rank-name { font-weight: 500; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .cx-rank-value { font-weight: 600; white-space: nowrap; }
    .cx-rank-bar { display: block; height: 6px; border-radius: 999px; background: var(--cx-track); overflow: hidden; }
    .cx-rank-bar > span { display: block; height: 100%; border-radius: inherit; background: rgb(var(--primary-500)); }
    .cx-rank-row:not(:first-child) .cx-rank-bar > span { background: rgba(var(--primary-500), .55); }
    .cx-rank-sub { font-size: 12px; color: var(--cx-muted); }

    /* ---------- Responsivo ----------
       Container queries: o layout responde à largura útil (sidebar aberta ou não),
       não ao viewport. .cx é o container da página; cada .cx-panel, do seu conteúdo. */
    .cx { container: cx / inline-size; }
    .cx-panel { container-type: inline-size; }

    @container cx (max-width: 1099px) {
        .cx-row { grid-template-columns: minmax(0, 1fr); }
    }
    @container cx (max-width: 760px) {
        .cx-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .cx-kpi:nth-child(3) { border-left: 0; }
        .cx-kpi:nth-child(n + 3) { border-top: 1px solid var(--cx-border); }
    }
    @container cx (max-width: 480px) {
        .cx-kpis { grid-template-columns: minmax(0, 1fr); }
        .cx-kpi + .cx-kpi { border-left: 0; border-top: 1px solid var(--cx-border); }
        .cx-kpi { padding: 16px; }
        .cx-kpi-value { font-size: 24px; }
    }

    @container (max-width: 860px) {
        .cx-delivery { grid-template-columns: 72px minmax(0, 1fr) 64px auto 16px; }
        .cx-delivery-items { display: none; }
        .cx-hide-lg { display: none; }
    }
    @container (max-width: 640px) {
        .cx-hide-md { display: none; }
    }
    @container (max-width: 560px) {
        .cx-panel-head { padding: 14px 16px; align-items: flex-start; }
        .cx-panel-icon { display: none; }
        .cx-week {
            display: flex;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            padding: 12px 16px;
        }
        .cx-week-day { flex: 0 0 104px; scroll-snap-align: start; }
        .cx-day-head { padding: 12px 16px 4px; }
        .cx-delivery {
            grid-template-columns: minmax(0, 1fr) auto;
            grid-template-areas:
                "main volume"
                "period status";
            gap: 8px 12px;
            padding: 12px 16px;
        }
        .cx-delivery-main { grid-area: main; }
        .cx-delivery-volume { grid-area: volume; }
        .cx-period { grid-area: period; }
        .cx-delivery .cx-badge { grid-area: status; justify-self: end; }
        .cx-delivery .cx-row-go { display: none; }
        .cx-table th, .cx-table td { padding-left: 16px; padding-right: 16px; }
        .cx-hide-sm { display: none; }
        .cx-alerts { padding: 10px; }
        .cx-rank { padding: 16px; }
    }
    @media (max-width: 639px) {
        .cx { gap: 16px; }
    }

    @media (prefers-reduced-motion: reduce) {
        .cx *, .cx *::before { transition: none !important; }
    }
</style>
