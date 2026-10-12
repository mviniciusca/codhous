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
