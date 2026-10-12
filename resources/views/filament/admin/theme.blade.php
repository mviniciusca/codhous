{{-- Acabamento global do painel (CSS puro, sem build). Cores vêm da paleta do AdminPanelProvider --}}
<style>
    .fi-body { -webkit-font-smoothing: antialiased; }

    /* Topbar: superfície da página, separada por uma linha fina */
    .fi-topbar > nav {
        box-shadow: none;
        border-bottom: 1px solid rgba(var(--gray-950), .06);
        --tw-ring-shadow: 0 0 #0000;
    }
    .dark .fi-topbar > nav {
        background: rgba(var(--gray-950), .82);
        border-bottom-color: rgba(255, 255, 255, .06);
        -webkit-backdrop-filter: saturate(140%) blur(10px);
        backdrop-filter: saturate(140%) blur(10px);
    }

    /* Topbar: busca à esquerda e larga; ações e usuário à direita */
    .fi-topbar > nav > div[x-persist^="topbar.end"] { flex: 1; min-width: 0; gap: 12px; }
    .fi-topbar .fi-global-search { flex: 1; min-width: 0; max-width: 460px; margin-inline-end: auto; }
    .fi-topbar .fi-global-search > *,
    .fi-topbar .fi-global-search-field { flex: 1; min-width: 0; width: 100%; }
    .fi-topbar .fi-global-search-field .fi-input-wrp { border-radius: 10px; }
    .fi-topbar .fi-global-search-field .fi-input-wrp-suffix {
        padding-inline: 8px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .02em;
        color: rgb(var(--gray-400));
    }
    .fi-topbar .fi-global-search { position: relative; }
    @media (min-width: 640px) {
        /* Resultados alinhados à busca, que agora fica à esquerda */
        .fi-topbar .fi-global-search-results-ctn { inset-inline: 0 auto; width: 100%; max-width: none; }
    }

    .cx-topbar-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        flex: none;
        padding: 7px 12px;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 500;
        color: rgb(var(--gray-600));
        box-shadow: inset 0 0 0 1px rgba(var(--gray-950), .1);
        transition: background-color .15s ease, color .15s ease;
    }
    .cx-topbar-btn:hover { color: rgb(var(--gray-950)); background: rgba(var(--gray-950), .04); }
    .dark .cx-topbar-btn { color: rgb(var(--gray-300)); box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .1); }
    .dark .cx-topbar-btn:hover { color: #fff; background: rgba(255, 255, 255, .05); }

    .cx-topbar-user {
        display: none;
        flex-direction: column;
        align-items: flex-end;
        max-width: 200px;
        line-height: 1.25;
        padding-inline-start: 12px;
        border-inline-start: 1px solid rgba(var(--gray-950), .08);
    }
    .dark .cx-topbar-user { border-inline-start-color: rgba(255, 255, 255, .08); }
    .cx-topbar-user > span { max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .cx-topbar-user-name { font-size: 13px; font-weight: 600; color: rgb(var(--gray-950)); }
    .dark .cx-topbar-user-name { color: #fff; }
    .cx-topbar-user-mail { font-size: 11px; color: rgb(var(--gray-500)); }
    @media (min-width: 1024px) { .cx-topbar-user { display: flex; } }
    @media (max-width: 639px) {
        .cx-topbar-btn span { display: none; }
        .cx-topbar-btn { padding: 8px; }
        /* Sem teclado físico, o atalho só ocupa espaço */
        .fi-topbar .fi-global-search-field .fi-input-wrp-suffix { display: none; }
    }

    /* Sidebar: coluna própria no desktop */
    @media (min-width: 1024px) {
        .fi-sidebar { border-inline-end: 1px solid rgba(var(--gray-950), .06); }
        .dark .fi-sidebar {
            background: rgba(var(--gray-900), .45);
            border-inline-end-color: rgba(255, 255, 255, .06);
        }
    }

    /* Item ativo: fundo azul translúcido */
    .dark .fi-sidebar-item-active > .fi-sidebar-item-button { background: rgba(var(--primary-500), .1); }
    .dark .fi-sidebar-item-active .fi-sidebar-item-label { color: rgb(var(--primary-300)); }
    .fi-sidebar-group-label { letter-spacing: .04em; text-transform: uppercase; font-size: 11px; }
</style>

@include('filament.admin.ui')
