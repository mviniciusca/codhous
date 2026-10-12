{{-- Caixa de entrada (CSS puro). Prefixo ix-; tokens e primitivas vêm de filament/admin/ui --}}
<style>
    .ix {
        --ix-height: max(560px, calc(100vh - 240px));
        container: ix / inline-size;
        display: flex;
        flex-direction: column;
        background: var(--cx-surface);
        border: 1px solid var(--cx-border);
        border-radius: var(--cx-radius);
        box-shadow: var(--cx-shadow);
        overflow: hidden;
    }

    /* ---------- Pastas ---------- */
    .ix-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border-bottom: 1px solid var(--cx-border);
    }
    .ix-tabs { display: flex; gap: 4px; overflow-x: auto; scrollbar-width: none; }
    .ix-tabs::-webkit-scrollbar { display: none; }
    .ix-tab {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        flex: none;
        padding: 7px 12px;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 500;
        color: var(--cx-muted);
        transition: background-color .15s ease, color .15s ease;
    }
    .ix-tab:hover { color: var(--cx-text); background: var(--cx-hover); }
    .ix-tab.is-active {
        color: rgb(var(--primary-700));
        background: rgba(var(--primary-500), .1);
        box-shadow: inset 0 0 0 1px rgba(var(--primary-500), .25);
    }
    .dark .ix-tab.is-active { color: rgb(var(--primary-200)); background: rgba(var(--primary-500), .16); }
    .ix-tab-count {
        min-width: 20px;
        padding: 0 6px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        line-height: 18px;
        text-align: center;
        color: var(--cx-muted);
        background: var(--cx-track);
    }
    .ix-tab-count.is-strong { color: #fff; background: rgb(var(--primary-600)); }

    /* ---------- Painéis ---------- */
    .ix-shell {
        display: grid;
        grid-template-columns: minmax(320px, 400px) minmax(0, 1fr);
        height: var(--ix-height);
    }
    .ix-list {
        display: flex;
        flex-direction: column;
        min-height: 0;
        border-right: 1px solid var(--cx-border);
    }
    .ix-search { padding: 12px; border-bottom: 1px solid var(--cx-border); }
    .ix-items { flex: 1; overflow-y: auto; transition: opacity .15s ease; }
    .ix-items.is-loading,
    .ix-reader.is-loading { opacity: .55; }
    .ix-more { display: flex; justify-content: center; padding: 12px; border-top: 1px solid var(--cx-border); }

    /* Item da lista: o botão invisível cobre a linha; a estrela fica por cima */
    .ix-item {
        position: relative;
        display: grid;
        grid-template-columns: 6px 36px minmax(0, 1fr) 28px;
        align-items: start;
        gap: 10px;
        padding: 12px 12px 12px 8px;
        border-bottom: 1px solid var(--cx-border);
        transition: background-color .15s ease;
    }
    .ix-item:hover { background: var(--cx-hover); }
    .ix-item.is-active { background: rgba(var(--primary-500), .08); }
    .ix-item.is-active::before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 3px;
        background: rgb(var(--primary-500));
    }
    .ix-item-hit { position: absolute; inset: 0; z-index: 1; width: 100%; }
    .ix-item-hit:focus-visible { outline-offset: -2px; }
    .ix-unread-dot {
        width: 6px; height: 6px;
        margin-top: 15px;
        border-radius: 999px;
    }
    .ix-item.is-unread .ix-unread-dot { background: rgb(var(--primary-500)); }

    .ix-avatar {
        display: grid;
        place-items: center;
        width: 36px;
        height: 36px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: .02em;
        color: hsl(var(--ix-h) 60% 32%);
        background: hsl(var(--ix-h) 70% 92%);
    }
    .dark .ix-avatar { color: hsl(var(--ix-h) 70% 82%); background: hsl(var(--ix-h) 30% 22%); }
    .ix-avatar-lg { width: 44px; height: 44px; font-size: 15px; }
    .ix-avatar-0 { --ix-h: 222; }
    .ix-avatar-1 { --ix-h: 160; }
    .ix-avatar-2 { --ix-h: 28; }
    .ix-avatar-3 { --ix-h: 270; }
    .ix-avatar-4 { --ix-h: 345; }
    .ix-avatar-5 { --ix-h: 195; }

    .ix-item-body { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
    .ix-item-top { display: flex; align-items: baseline; gap: 8px; }
    .ix-item-name {
        flex: 1;
        min-width: 0;
        font-size: 14px;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ix-item-date { flex: none; font-size: 12px; color: var(--cx-muted); }
    .ix-item-subject,
    .ix-item-preview {
        font-size: 13px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ix-item-preview { font-size: 12px; color: var(--cx-muted); }
    .ix-item.is-unread .ix-item-name,
    .ix-item.is-unread .ix-item-subject { font-weight: 700; }
    .ix-item.is-unread .ix-item-date { color: rgb(var(--primary-600)); font-weight: 600; }
    .dark .ix-item.is-unread .ix-item-date { color: rgb(var(--primary-400)); }
    .ix-item:not(.is-unread) .ix-item-subject { color: var(--cx-muted); }

    .ix-star {
        position: relative;
        z-index: 2;
        display: grid;
        place-items: center;
        width: 28px;
        height: 28px;
        margin-top: 18px;
        border-radius: 7px;
        color: var(--cx-subtle);
        transition: color .15s ease, background-color .15s ease;
    }
    .ix-star:hover { color: rgb(var(--warning-500)); background: rgba(var(--warning-500), .1); }
    .ix-star.is-on { color: rgb(var(--warning-500)); }

    /* ---------- Leitura ---------- */
    .ix-reader {
        container: ixr / inline-size;
        display: flex;
        flex-direction: column;
        min-width: 0;
        min-height: 0;
        transition: opacity .15s ease;
    }
    .ix-placeholder { margin: auto; }
    .ix-toolbar {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 8px 12px;
        border-bottom: 1px solid var(--cx-border);
    }
    .ix-toolbar > .is-danger:last-child { margin-left: auto; }
    .ix-tool {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        color: var(--cx-muted);
        transition: color .15s ease, background-color .15s ease;
    }
    .ix-tool:hover { color: var(--cx-text); background: var(--cx-hover); }
    .ix-tool.is-on { color: rgb(var(--warning-600)); }
    .dark .ix-tool.is-on { color: rgb(var(--warning-400)); }
    .ix-tool.is-danger:hover { color: rgb(var(--danger-500)); background: rgba(var(--danger-500), .1); }
    .ix-back { display: none; }

    .ix-scroll {
        flex: 1;
        overflow-y: auto;
        display: grid;
        grid-template-columns: minmax(0, 1fr) 280px;
        grid-template-areas:
            "message aside"
            "composer aside";
        align-content: start;
        gap: 0 24px;
        padding: 24px;
    }
    .ix-message { grid-area: message; min-width: 0; }
    .ix-aside { grid-area: aside; display: grid; gap: 12px; align-content: start; }
    .ix-composer { grid-area: composer; }

    .ix-subject { font-size: 20px; font-weight: 600; line-height: 1.3; letter-spacing: -.01em; }
    .ix-from {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: start;
        gap: 12px;
        margin-top: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--cx-border);
    }
    .ix-from-text { display: flex; flex-direction: column; gap: 1px; min-width: 0; font-size: 13px; }
    .ix-from-text strong { font-size: 14px; font-weight: 600; }
    .ix-from-text a { color: var(--cx-muted); overflow: hidden; text-overflow: ellipsis; }
    .ix-from-text a:hover { color: rgb(var(--primary-500)); }
    .ix-from-meta { font-size: 12px; color: var(--cx-subtle); }
    .ix-flag { color: rgb(var(--danger-500)); font-weight: 600; }
    .ix-when { font-size: 12px; color: var(--cx-muted); white-space: nowrap; }

    .ix-body {
        padding: 20px 0 24px;
        font-size: 14px;
        line-height: 1.7;
        overflow-wrap: anywhere;
    }
    .ix-body p { margin: 0 0 .75em; }
    .ix-body a { color: rgb(var(--primary-500)); text-decoration: underline; }
    .ix-body ul { list-style: disc; padding-left: 1.25em; }
    .ix-body ol { list-style: decimal; padding-left: 1.25em; }

    /* Cartões do contato */
    .ix-card {
        padding: 14px;
        border: 1px solid var(--cx-border);
        border-radius: 12px;
        background: var(--cx-surface-2);
    }
    .ix-card-title {
        margin-bottom: 10px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: var(--cx-subtle);
    }
    .ix-contact { display: grid; gap: 8px; font-size: 13px; }
    .ix-contact li { display: flex; align-items: center; gap: 8px; min-width: 0; }
    .ix-contact svg { color: var(--cx-subtle); }
    .ix-contact a { min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ix-contact a:hover { color: rgb(var(--primary-500)); }
    .ix-muted { color: var(--cx-muted); }
    .ix-whatsapp {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 12px;
        padding: 8px 10px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        color: rgb(var(--success-700));
        background: rgba(var(--success-500), .12);
        box-shadow: inset 0 0 0 1px rgba(var(--success-500), .25);
        transition: background-color .15s ease;
    }
    .ix-whatsapp:hover { background: rgba(var(--success-500), .2); }
    .dark .ix-whatsapp { color: rgb(var(--success-300)); }

    .ix-links { display: grid; gap: 2px; margin: 0 -6px; }
    .ix-links a,
    .ix-links button {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        padding: 6px;
        border-radius: 7px;
        font-size: 13px;
        text-align: left;
        transition: background-color .15s ease;
    }
    .ix-links a:hover,
    .ix-links button:hover { background: var(--cx-hover); }
    .ix-link-main { flex: 1; min-width: 0; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ix-link-sub { flex: none; font-size: 12px; color: var(--cx-muted); }
    .ix-links .cx-badge { padding: 1px 7px; font-size: 11px; }
    .ix-dir { color: var(--cx-subtle); }
    .ix-dir.is-out { color: rgb(var(--primary-500)); }

    /* Resposta */
    .ix-composer {
        display: grid;
        gap: 12px;
        padding: 16px;
        border: 1px solid var(--cx-border-strong);
        border-radius: 12px;
        background: var(--cx-surface-2);
    }
    .ix-composer:focus-within { border-color: rgba(var(--primary-500), .6); }
    .ix-composer-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: var(--cx-muted);
    }
    .ix-composer-head strong { color: var(--cx-text); font-weight: 600; }
    .ix-composer .fi-fo-rich-editor trix-editor { min-height: 120px; }
    .ix-composer-foot { display: flex; align-items: center; justify-content: flex-end; gap: 8px; flex-wrap: wrap; }

    /* ---------- Responsivo (pela largura da caixa, não do viewport) ---------- */
    @container ixr (max-width: 760px) {
        .ix-scroll {
            grid-template-columns: minmax(0, 1fr);
            grid-template-areas: "message" "aside" "composer";
            gap: 0;
            padding: 20px;
        }
        .ix-aside { grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-bottom: 20px; }
        .ix-tool-label { display: none; }
    }
    @container ix (max-width: 820px) {
        .ix-shell { grid-template-columns: minmax(0, 1fr); height: auto; }
        .ix-list { border-right: 0; }
        .ix-items { max-height: none; }
        .ix-reader { display: none; }
        .ix.has-open .ix-list { display: none; }
        .ix.has-open .ix-reader { display: flex; }
        .ix-back { display: inline-flex; }
        .ix-scroll { overflow: visible; }
    }
    @container ixr (max-width: 480px) {
        .ix-scroll { padding: 16px; }
        .ix-from { grid-template-columns: auto minmax(0, 1fr); }
        .ix-when { grid-column: 2; }
        .ix-subject { font-size: 18px; }
    }

    @media (prefers-reduced-motion: reduce) {
        .ix *, .ix *::before { transition: none !important; }
    }
</style>
