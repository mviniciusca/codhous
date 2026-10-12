{{-- Meu Workspace (CSS puro, sobre os tokens .cx-ui e o painel da visão geral). Prefixo ws- --}}
<style>
    .ws {
        display: grid;
        gap: 20px;
        container: ws / inline-size;
    }

    /* ---------- Grade ---------- */
    .ws-split {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(320px, 400px);
        gap: 20px;
        align-items: start;
    }
    .ws-nav { margin: 0; justify-self: start; }

    .ws .cx-panel-count { color: rgb(var(--primary-600)); background: rgba(var(--primary-500), .12); }
    .dark .ws .cx-panel-count { color: rgb(var(--primary-300)); background: rgba(var(--primary-500), .18); }
    .ws .cx-panel-actions { flex: none; }

    /* ---------- Calendário ---------- */
    .ws-cal-nav { display: flex; align-items: center; gap: 4px; }
    .ws-cal-nav .fi-btn { margin-right: 4px; }
    .ws-cal-month {
        min-width: 120px;
        font-size: 14px;
        font-weight: 600;
        text-align: center;
        white-space: nowrap;
    }

    .ws-cal { padding: 12px 16px 16px; }
    .ws-cal-weekdays,
    .ws-cal-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
    }
    .ws-cal-weekdays span {
        padding: 0 0 8px;
        font-size: 12px;
        font-weight: 500;
        text-align: center;
        color: var(--cx-muted);
    }
    .ws-cal-grid {
        border-top: 1px solid var(--cx-border);
        border-left: 1px solid var(--cx-border);
        border-radius: 10px;
        overflow: hidden;
    }
    .ws-day {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 4px;
        min-width: 0;
        height: 96px;
        padding: 6px;
        overflow: hidden;
        border-right: 1px solid var(--cx-border);
        border-bottom: 1px solid var(--cx-border);
        text-align: left;
        color: inherit;
        background: transparent;
        cursor: pointer;
        transition: background-color .15s ease;
    }
    .ws-day:hover { background: var(--cx-hover); }
    .ws-day.is-out { background: var(--cx-surface-2); }
    .ws-day.is-out .ws-day-num { color: var(--cx-subtle); font-weight: 400; }
    .ws-day.is-selected { background: rgba(var(--primary-500), .08); }
    .ws-day.is-selected::after {
        content: "";
        position: absolute;
        inset: -1px;
        border: 1.5px solid rgba(var(--primary-500), .75);
        border-radius: 8px;
        pointer-events: none;
    }
    .ws-day-num {
        display: grid;
        place-items: center;
        align-self: flex-start;
        min-width: 24px;
        height: 24px;
        padding: 0 4px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
    }
    .ws-day.is-today .ws-day-num { color: #fff; background: rgb(var(--primary-600)); }
    .ws-day-events { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
    .ws-day-event {
        display: flex;
        align-items: center;
        gap: 5px;
        min-width: 0;
        font-size: 11.5px;
        line-height: 1.35;
        color: var(--cx-text);
    }
    .ws-day-event-title { min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ws-day-event .cx-dot { width: 6px; height: 6px; }
    .ws-day-more { font-size: 11px; font-weight: 500; color: var(--cx-muted); }
    .ws-day-dots { display: none; gap: 3px; }
    .ws-day-dots .cx-dot { width: 5px; height: 5px; }


    /* ---------- Tarefas ---------- */
    .ws .ws-tabs { border-bottom: 1px solid var(--cx-border); }
    .ws-tasks { padding: 6px 0; }
    .ws-task {
        display: grid;
        grid-template-columns: 20px minmax(0, 1fr) auto 96px 28px;
        align-items: center;
        gap: 12px;
        padding: 8px 12px 8px 20px;
        transition: background-color .15s ease;
    }
    .ws-task:hover { background: var(--cx-hover); }
    .ws-check {
        display: grid;
        place-items: center;
        width: 20px;
        height: 20px;
        border-radius: 6px;
        border: 1.5px solid var(--cx-border-strong);
        color: #fff;
        background: transparent;
        transition: background-color .15s ease, border-color .15s ease;
    }
    .ws-check:hover { border-color: rgb(var(--primary-500)); }
    .ws-check-icon { width: 14px; height: 14px; opacity: 0; transition: opacity .15s ease; }
    .ws-check[aria-checked="true"] { border-color: rgb(var(--primary-600)); background: rgb(var(--primary-600)); }
    .ws-check[aria-checked="true"] .ws-check-icon { opacity: 1; }
    .ws-task-title {
        min-width: 0;
        padding: 2px 0;
        font-size: 14px;
        font-weight: 500;
        text-align: left;
        color: inherit;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .ws-task-title:hover { color: rgb(var(--primary-600)); }
    .dark .ws-task-title:hover { color: rgb(var(--primary-300)); }
    .ws-task.is-done .ws-task-title { color: var(--cx-muted); text-decoration: line-through; text-decoration-color: var(--cx-subtle); }
    .ws-task-meta { display: contents; }
    .ws-task-badge { padding: 2px 8px 2px 6px; gap: 4px; }
    .ws-task-badge .cx-icon-xs { opacity: 1; }
    .ws-due {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        color: var(--cx-muted);
        white-space: nowrap;
        overflow: hidden;
    }
    .ws-due.is-empty { color: var(--cx-subtle); }
    .ws-due.is-overdue { color: rgb(var(--danger-600)); font-weight: 500; }
    .dark .ws-due.is-overdue { color: rgb(var(--danger-400)); }

    /* ---------- Próximos compromissos ---------- */
    .ws-events { padding: 6px 0; }
    .ws-event {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 12px 8px 20px;
        transition: background-color .15s ease;
    }
    .ws-event:hover { background: var(--cx-hover); }
    .ws-date-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        flex: none;
        border-radius: 9px;
        color: var(--tone-fg);
        background: rgba(var(--tone), .1);
        box-shadow: inset 0 0 0 1px rgba(var(--tone), .18);
    }
    .ws-date-box b { font-size: 16px; font-weight: 700; line-height: 1.1; }
    .ws-date-box small { font-size: 10px; font-weight: 600; letter-spacing: .04em; }
    .ws-event-main {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
        flex: 1;
        text-align: left;
        color: inherit;
    }
    .ws-time-box {
        display: flex;
        flex-direction: column;
        justify-content: center;
        width: 52px;
        flex: none;
        padding-left: 10px;
        border-left: 2px solid rgb(var(--tone));
        line-height: 1.25;
    }
    .ws-time-box b { font-size: 13px; font-weight: 600; }
    .ws-time-box small { font-size: 12px; color: var(--cx-muted); }
    .ws-event-title .cx-dot { display: none; }
    .ws-event-title { font-size: 14px; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ws-event-main:hover .ws-event-title { color: rgb(var(--primary-600)); }
    .dark .ws-event-main:hover .ws-event-title { color: rgb(var(--primary-300)); }
    .ws-event-meta {
        display: flex;
        align-items: center;
        gap: 6px;
        min-width: 0;
        font-size: 12px;
        color: var(--cx-muted);
        white-space: nowrap;
    }
    .ws-event-place { min-width: 0; overflow: hidden; text-overflow: ellipsis; }

    /* ---------- Notas ---------- */
    .ws-notes {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 12px;
        padding: 16px 20px 20px;
    }
    .ws-note {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
        min-height: 148px;
        padding: 12px 12px 14px 16px;
        border: 1px solid var(--cx-border);
        border-left: 3px solid rgba(var(--tone), .85);
        border-radius: 12px;
        background: var(--cx-surface-2);
        transition: border-color .15s ease;
    }
    .ws-note.cx-tone-gray { border-left-color: var(--cx-border-strong); }
    .ws-note:hover { border-top-color: var(--cx-border-strong); border-right-color: var(--cx-border-strong); border-bottom-color: var(--cx-border-strong); }
    .ws-note-head { display: flex; align-items: flex-start; gap: 8px; }
    .ws-note-title {
        flex: 1;
        min-width: 0;
        padding-top: 3px;
        font-size: 14px;
        font-weight: 600;
        text-align: left;
        color: inherit;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .ws-note-title:hover { color: rgb(var(--primary-600)); }
    .dark .ws-note-title:hover { color: rgb(var(--primary-300)); }
    .ws-note-time { font-size: 12px; color: var(--cx-muted); }
    .ws-note-body {
        display: -webkit-box;
        margin-top: 8px;
        font-size: 13px;
        line-height: 1.5;
        color: var(--cx-text);
        opacity: .85;
        white-space: pre-line;
        overflow: hidden;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 4;
        line-clamp: 4;
    }
    .ws-note-new {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        min-height: 148px;
        padding: 16px;
        border: 1px dashed var(--cx-border-strong);
        border-radius: 12px;
        text-align: center;
        color: inherit;
        transition: border-color .15s ease, background-color .15s ease;
    }
    .ws-note-new:hover { border-color: rgba(var(--primary-500), .6); background: rgba(var(--primary-500), .05); }
    .ws-note-new strong { font-size: 13px; font-weight: 600; }
    .ws-note-new > span:last-child { max-width: 200px; font-size: 12px; color: var(--cx-muted); }
    .ws-note-new-icon {
        display: grid;
        place-items: center;
        width: 32px;
        height: 32px;
        margin-bottom: 4px;
        border-radius: 999px;
        color: var(--cx-muted);
        box-shadow: inset 0 0 0 1px var(--cx-border-strong);
    }

    .ws-menu { flex: none; }

    /* ---------- Responsivo ---------- */
    @container ws (max-width: 899px) {
        .ws-split { grid-template-columns: minmax(0, 1fr); }
    }

    /* Calendário estreito: só os pontos nos dias */
    @container (max-width: 520px) {
        .ws-cal { padding: 12px 12px 0; }
        .ws-day { height: 56px; padding: 6px 4px; align-items: center; }
        .ws-day-num { align-self: center; }
        .ws-day-events { display: none; }
        .ws-day-dots { display: flex; }
    }
    @container (max-width: 560px) {
        .ws-cal-nav { flex-wrap: wrap; justify-content: flex-end; }
        .ws-cal-month { min-width: 0; }
        .ws .cx-panel-head { flex-wrap: wrap; }
        .ws-task {
            grid-template-columns: 20px minmax(0, 1fr) 28px;
            grid-template-areas:
                "check title menu"
                ". meta meta";
            row-gap: 4px;
            padding-left: 16px;
        }
        .ws-check { grid-area: check; }
        .ws-task-title { grid-area: title; }
        .ws-task .ws-menu { grid-area: menu; }
        .ws-task-meta { grid-area: meta; display: flex; align-items: center; gap: 10px; min-width: 0; }
        .ws-event { padding-left: 16px; }
        .ws-notes { padding: 12px 16px 16px; }
    }

    @media (max-width: 639px) {
        .ws, .ws-split { gap: 16px; }
    }

    @media (prefers-reduced-motion: reduce) {
        .ws *, .ws *::before, .ws *::after { transition: none !important; }
    }
</style>
