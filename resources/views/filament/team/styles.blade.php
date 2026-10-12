{{-- Equipe e acessos (CSS puro). Prefixo tm-; tokens e primitivas vêm de filament/admin/ui --}}
<style>
    .tm { display: grid; gap: 20px; }

    /* Sem container query aqui: o painel envolve dropdowns e modais do Filament (position: fixed) */
    .tm-panel {
        min-width: 0;
        background: var(--cx-surface);
        border: 1px solid var(--cx-border);
        border-radius: var(--cx-radius);
        box-shadow: var(--cx-shadow);
        overflow: hidden;
    }

    .tm-tabs {
        display: flex;
        gap: 2px;
        padding: 0 12px;
        border-bottom: 1px solid var(--cx-border);
        overflow-x: auto;
        scrollbar-width: none;
    }
    .tm-tabs::-webkit-scrollbar { display: none; }
    .tm-tab {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        flex: none;
        margin-bottom: -1px;
        padding: 14px 12px;
        border-bottom: 2px solid transparent;
        font-size: 13px;
        font-weight: 500;
        color: var(--cx-muted);
        transition: color .15s ease, border-color .15s ease;
    }
    .tm-tab:hover { color: var(--cx-text); }
    .tm-tab.is-active { color: rgb(var(--primary-600)); border-bottom-color: rgb(var(--primary-500)); }
    .dark .tm-tab.is-active { color: rgb(var(--primary-300)); }
    .tm-tab-count {
        min-width: 18px;
        padding: 0 6px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        line-height: 18px;
        text-align: center;
        color: rgb(var(--warning-700));
        background: rgba(var(--warning-500), .15);
    }
    .dark .tm-tab-count { color: rgb(var(--warning-300)); }

    .tm-body { transition: opacity .15s ease; }
    .tm-body.is-loading { opacity: .55; }

    /* Tabelas do Filament dentro do painel: sem segundo cartão */
    .tm-body .fi-ta-ctn { border-radius: 0; box-shadow: none; background: transparent; }
    .tm-body .fi-ta-header-toolbar { padding-inline: 16px; }

    /* ---------- Perfis ---------- */
    .tm-roles { display: grid; gap: 16px; padding: 20px; }
    .tm-roles-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px 24px; }
    .tm-roles-head p { max-width: 640px; font-size: 13px; line-height: 1.6; color: var(--cx-muted); }
    .tm-role-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 12px; }
    .tm-role {
        display: flex;
        flex-direction: column;
        gap: 14px;
        padding: 16px;
        border: 1px solid var(--cx-border);
        border-radius: 12px;
        background: var(--cx-surface-2);
    }
    .tm-role.is-system { border-color: rgba(var(--primary-500), .35); background: rgba(var(--primary-500), .05); }
    .tm-role-top { display: flex; gap: 12px; }
    .tm-role-icon {
        display: grid;
        place-items: center;
        width: 36px;
        height: 36px;
        flex: none;
        border-radius: 10px;
        color: rgb(var(--primary-600));
        background: rgba(var(--primary-500), .1);
    }
    .dark .tm-role-icon { color: rgb(var(--primary-300)); }
    .tm-role-titles { min-width: 0; }
    .tm-role-titles h3 { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; font-size: 14px; font-weight: 600; }
    .tm-role-titles p { margin-top: 2px; font-size: 12px; color: var(--cx-muted); }
    .tm-role-lock {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 1px 7px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 500;
        color: rgb(var(--primary-700));
        background: rgba(var(--primary-500), .12);
    }
    .dark .tm-role-lock { color: rgb(var(--primary-200)); }
    .tm-areas { display: grid; gap: 6px; }
    .tm-area { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--cx-subtle); }
    .tm-area.is-on { color: var(--cx-text); }
    .tm-area.is-on svg { color: rgb(var(--success-500)); }
    .tm-role-foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-top: auto;
        padding-top: 12px;
        border-top: 1px solid var(--cx-border);
    }
    .tm-role-count { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; color: var(--cx-muted); }
    .tm-role-actions { display: inline-flex; gap: 4px; }

    @media (max-width: 639px) {
        .tm-roles { padding: 16px; }
        .tm-tab { padding: 12px 10px; }
    }
</style>
