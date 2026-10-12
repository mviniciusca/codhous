{{-- Acabamento global do painel (CSS puro, sem build). Cores vêm da paleta do AdminPanelProvider --}}
<style>
    .fi-body { -webkit-font-smoothing: antialiased; }

    /* Topbar: superfície da página, separada por uma linha fina */
    .fi-topbar > nav {
        box-shadow: none;
        border-bottom: 1px solid rgba(var(--gray-950), .06);
        --tw-ring-shadow: 0 0 #0000;
    }
    /*
        O desfoque fica num pseudo-elemento: backdrop-filter no próprio <nav> o tornaria
        o bloco de contenção dos dropdowns (position: fixed) e eles pulariam de lugar
        ao trocar de tema. isolation cria o empilhamento sem afetar o posicionamento.
    */
    .dark .fi-topbar > nav {
        position: relative;
        isolation: isolate;
        background: transparent;
        border-bottom-color: rgba(255, 255, 255, .06);
    }
    .dark .fi-topbar > nav::before {
        content: "";
        position: absolute;
        inset: 0;
        z-index: -1;
        background: rgba(var(--gray-950), .82);
        -webkit-backdrop-filter: saturate(140%) blur(10px);
        backdrop-filter: saturate(140%) blur(10px);
        pointer-events: none;
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

    @media (max-width: 639px) {
        .cx-topbar-btn span { display: none; }
        .cx-topbar-btn { padding: 8px; }
        /* Sem teclado físico, o atalho só ocupa espaço */
        .fi-topbar .fi-global-search-field .fi-input-wrp-suffix { display: none; }
    }

    .cx-topbar-icon {
        display: grid;
        place-items: center;
        width: 36px;
        height: 36px;
        border-radius: 9px;
        color: rgb(var(--gray-500));
        transition: background-color .15s ease, color .15s ease;
    }
    .cx-topbar-icon:hover { color: rgb(var(--gray-950)); background: rgba(var(--gray-950), .05); }
    .dark .cx-topbar-icon { color: rgb(var(--gray-400)); }
    .dark .cx-topbar-icon:hover { color: #fff; background: rgba(255, 255, 255, .06); }

    /* Atalhos do Meu Workspace (tarefas, agenda, notas) */
    .cx-topbar-shortcuts {
        display: flex;
        align-items: center;
        gap: 2px;
        padding-right: 8px;
        margin-right: 4px;
        border-right: 1px solid rgba(var(--gray-950), .08);
    }
    .dark .cx-topbar-shortcuts { border-right-color: rgba(255, 255, 255, .08); }
    .cx-topbar-shortcuts .cx-topbar-icon { position: relative; }
    .cx-topbar-count {
        position: absolute;
        top: 1px;
        right: -2px;
        min-width: 15px;
        height: 15px;
        padding: 0 4px;
        border-radius: 999px;
        font-size: 9.5px;
        font-weight: 700;
        line-height: 15px;
        text-align: center;
        color: #fff;
        background: rgb(var(--primary-600));
        box-shadow: 0 0 0 2px #fff;
    }
    .dark .cx-topbar-count { box-shadow: 0 0 0 2px rgb(var(--gray-950)); }
    @media (max-width: 639px) {
        .cx-topbar-shortcuts { display: none; }
    }

    /* Menu do usuário */
    .cx-um-trigger {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 4px 8px 4px 4px;
        border-radius: 12px;
        text-align: left;
        transition: background-color .15s ease;
    }
    .cx-um-trigger:hover { background: rgba(var(--gray-950), .04); }
    .dark .cx-um-trigger:hover { background: rgba(255, 255, 255, .05); }
    .cx-um-trigger-text { display: none; flex-direction: column; max-width: 180px; line-height: 1.25; }
    .cx-um-trigger-text > span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .cx-um-trigger-name { font-size: 13px; font-weight: 600; color: rgb(var(--gray-950)); }
    .dark .cx-um-trigger-name { color: #fff; }
    .cx-um-trigger-role { font-size: 11px; color: rgb(var(--gray-500)); }
    .cx-um-chevron { display: none; width: 16px; height: 16px; color: rgb(var(--gray-400)); }
    @media (min-width: 1024px) {
        .cx-um-trigger-text { display: flex; }
        .cx-um-chevron { display: block; }
    }

    .cx-um-head { display: flex; align-items: center; gap: 12px; padding: 14px; }
    .cx-um-id { display: flex; flex-direction: column; min-width: 0; line-height: 1.3; }
    .cx-um-id strong { font-size: 14px; font-weight: 600; color: rgb(var(--gray-950)); }
    .dark .cx-um-id strong { color: #fff; }
    .cx-um-id > span { font-size: 12px; color: rgb(var(--gray-500)); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .cx-um-id .cx-um-role {
        align-self: flex-start;
        margin-top: 6px;
        padding: 1px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        color: rgb(var(--primary-700));
        background: rgba(var(--primary-500), .12);
    }
    .dark .cx-um-id .cx-um-role { color: rgb(var(--primary-200)); }
    .cx-um-section { padding: 10px 12px 12px; border-top: 1px solid rgba(var(--gray-950), .06); }
    .dark .cx-um-section { border-top-color: rgba(255, 255, 255, .06); }
    .cx-um-section-solo { border-top: 0; }
    .cx-um-version { padding: 8px 14px 10px; border-top: 1px solid rgba(var(--gray-950), .06); font-size: 11px; color: rgb(var(--gray-400)); font-variant-numeric: tabular-nums; }
    .dark .cx-um-version { border-top-color: rgba(255, 255, 255, .06); color: rgb(var(--gray-500)); }
    .cx-um-label { display: block; margin-bottom: 8px; font-size: 11px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: rgb(var(--gray-400)); }

    .cx-theme { display: grid; grid-template-columns: repeat(3, 1fr); gap: 4px; padding: 3px; border-radius: 10px; background: rgba(var(--gray-500), .1); }
    .cx-theme-opt {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        padding: 8px 4px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 500;
        color: rgb(var(--gray-500));
        transition: background-color .15s ease, color .15s ease;
    }
    .cx-theme-opt:hover { color: rgb(var(--gray-950)); }
    .dark .cx-theme-opt { color: rgb(var(--gray-400)); }
    .dark .cx-theme-opt:hover { color: #fff; }
    .cx-theme-opt[aria-checked="true"] { color: rgb(var(--primary-600)); background: #fff; box-shadow: 0 1px 2px rgba(0, 0, 0, .1); }
    .dark .cx-theme-opt[aria-checked="true"] { color: rgb(var(--primary-300)); background: rgb(var(--gray-800)); }
    .cx-theme-opt:focus-visible { outline: 2px solid rgb(var(--primary-500)); outline-offset: 1px; }

    /*
        Sidebar chapada: mesma superfície da topbar (branco / grafite 950), separada do conteúdo
        só por linhas finas. O cabeçalho da marca tem a altura da topbar e continua a linha dela.
    */
    .fi-sidebar { --cx-sb-line: rgba(var(--gray-950), .06); background: #fff; }
    .dark .fi-sidebar { --cx-sb-line: rgba(255, 255, 255, .06); background: rgb(var(--gray-950)); }
    @media (min-width: 1024px) {
        .fi-sidebar { border-inline-end: 1px solid var(--cx-sb-line); }
    }

    .fi-sidebar .fi-sidebar-header {
        height: 64px;
        padding-inline: 16px;
        background: transparent;
        box-shadow: none;
        border-bottom: 1px solid var(--cx-sb-line);
    }
    .fi-sidebar-header .fi-logo { font-size: 15px; font-weight: 600; letter-spacing: -.01em; }
    .fi-sidebar-header .fi-icon-btn { width: 32px; height: 32px; border-radius: 8px; }
    .fi-sidebar-header .fi-icon-btn svg { width: 18px; height: 18px; }

    /* Navegação: mais densa, rolagem discreta (a barra só aparece ao passar o mouse) */
    .fi-sidebar .fi-sidebar-nav {
        row-gap: 20px;
        padding: 16px 16px 24px;
        scrollbar-width: thin;
        scrollbar-color: transparent transparent;
    }
    .fi-sidebar .fi-sidebar-nav:hover { scrollbar-color: rgba(var(--gray-500), .35) transparent; }
    .fi-sidebar .fi-sidebar-nav-groups { row-gap: 20px; }
    .fi-sidebar .fi-sidebar-group { row-gap: 2px; }
    .fi-sidebar .fi-sidebar-group-items { row-gap: 2px; }

    /* Rótulo do grupo: caixa-alta pequena; a seta só aparece ao passar o mouse ou com o grupo fechado */
    .fi-sidebar .fi-sidebar-group-button { padding: 2px 8px; min-height: 28px; }
    .fi-sidebar .fi-sidebar-group-label {
        font-size: 11px;
        font-weight: 600;
        line-height: 20px;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: rgb(var(--gray-400));
    }
    .dark .fi-sidebar .fi-sidebar-group-label { color: rgb(var(--gray-500)); }
    .fi-sidebar .fi-sidebar-group-collapse-button { width: 20px; height: 20px; opacity: 0; transition: opacity .15s ease, transform .15s ease; }
    .fi-sidebar .fi-sidebar-group-collapse-button svg { width: 14px; height: 14px; }
    .fi-sidebar .fi-sidebar-group-button:hover .fi-sidebar-group-collapse-button,
    .fi-sidebar .fi-sidebar-group-collapse-button:focus-visible,
    .fi-sidebar .fi-sidebar-group-collapse-button.-rotate-180 { opacity: 1; }

    /* Itens */
    .fi-sidebar .fi-sidebar-item-button { gap: 10px; padding: 6px 8px; border-radius: 8px; }
    .fi-sidebar .fi-sidebar-item-icon { width: 18px; height: 18px; }
    .fi-sidebar .fi-sidebar-item-label { font-size: 13.5px; line-height: 20px; color: rgb(var(--gray-600)); transition: color .15s ease; }
    .dark .fi-sidebar .fi-sidebar-item-label { color: rgb(var(--gray-300)); }
    .fi-sidebar .fi-sidebar-item-button:hover { background: rgba(var(--gray-950), .04); }
    .fi-sidebar .fi-sidebar-item-button:hover .fi-sidebar-item-label { color: rgb(var(--gray-950)); }
    .dark .fi-sidebar .fi-sidebar-item-button:hover { background: rgba(255, 255, 255, .04); }
    .dark .fi-sidebar .fi-sidebar-item-button:hover .fi-sidebar-item-label { color: #fff; }

    /* Item ativo: fundo azul translúcido */
    .fi-sidebar .fi-sidebar-item.fi-active > .fi-sidebar-item-button { background: rgba(var(--primary-500), .08); }
    .dark .fi-sidebar .fi-sidebar-item.fi-active > .fi-sidebar-item-button { background: rgba(var(--primary-500), .1); }
    .fi-sidebar .fi-sidebar-item.fi-active > .fi-sidebar-item-button .fi-sidebar-item-label { color: rgb(var(--primary-700)); }
    .dark .fi-sidebar .fi-sidebar-item.fi-active > .fi-sidebar-item-button .fi-sidebar-item-label { color: rgb(var(--primary-300)); }

    /* Contadores: pílula compacta, sem contorno */
    .fi-sidebar .fi-sidebar-item .fi-badge {
        min-width: 20px;
        padding: 1px 6px;
        border-radius: 999px;
        font-size: 11px;
        font-variant-numeric: tabular-nums;
        box-shadow: none;
    }

    /*
        Cabeçalho de página compacto (todas as páginas): uma linha com trilha › título,
        subtítulo como dica no "?" e ações à direita. Markup em vendor/filament-panels/components/header.
        As margens negativas compensam o py-8 / gap-y-8 da página do Filament.
    */
    .cx-pagebar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 8px 12px;
        min-height: 36px;
        margin: -20px 0 -18px;
    }
    .cx-pagebar-start { display: flex; align-items: center; gap: 8px; min-width: 0; flex: 1 1 auto; }
    .cx-pagebar-crumb {
        flex: none;
        font-size: 14px;
        font-weight: 500;
        white-space: nowrap;
        color: rgb(var(--gray-500));
        transition: color .15s ease;
    }
    a.cx-pagebar-crumb:hover { color: rgb(var(--gray-950)); }
    .dark .cx-pagebar-crumb { color: rgb(var(--gray-400)); }
    .dark a.cx-pagebar-crumb:hover { color: #fff; }
    .cx-pagebar-sep { width: 16px; height: 16px; flex: none; color: rgb(var(--gray-400)); }
    .dark .cx-pagebar-sep { color: rgb(var(--gray-600)); }
    .cx-pagebar-title {
        min-width: 0;
        overflow: hidden;
        font-size: 18px;
        font-weight: 700;
        line-height: 1.3;
        letter-spacing: -.01em;
        white-space: nowrap;
        text-overflow: ellipsis;
        color: rgb(var(--gray-950));
    }
    .dark .cx-pagebar-title { color: #fff; }
    .cx-pagebar-hint {
        display: grid;
        place-items: center;
        flex: none;
        border-radius: 999px;
        color: rgb(var(--gray-400));
        cursor: help;
        transition: color .15s ease;
    }
    .cx-pagebar-hint:hover, .cx-pagebar-hint:focus-visible { color: rgb(var(--primary-500)); outline: none; }
    .cx-pagebar-hint svg { width: 16px; height: 16px; }
    .cx-pagebar-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; flex: none; }
    .cx-pagebar-actions:empty { display: none; }
    /* Celular: só o título; a trilha volta a aparecer a partir de 640px */
    @media (max-width: 639px) {
        .cx-pagebar { margin-top: -12px; }
        .cx-pagebar-crumb, .cx-pagebar-sep { display: none; }
    }
</style>

@include('filament.admin.ui')
