{{-- Configurações (CSS puro). Prefixo st-; tokens e primitivas vêm de filament/admin/ui --}}
<style>
    /* O botão Descartar (header da página) só aparece com alterações pendentes */
    html:not(.st-dirty) [data-st-discard] { display: none; }

    .st {
        background: var(--cx-surface);
        border: 1px solid var(--cx-border);
        border-radius: var(--cx-radius);
        box-shadow: var(--cx-shadow);
    }

    .st-form { padding: 0 28px; }
    .st-form > .fi-fo-component-ctn { row-gap: 0; }

    /*
        Seções "aside": título e descrição à esquerda, campos à direita, sem cartão
        dentro do cartão. Separadas por linhas finas.
    */
    .st-form .fi-section.fi-aside { gap: 8px 32px; padding: 28px 0; }
    /* Cada seção vem dentro de uma coluna do grid: a linha fica entre colunas vizinhas */
    .st-form > .fi-fo-component-ctn > * + * > .fi-section.fi-aside { border-top: 1px solid var(--cx-border); }
    .st-form .fi-aside > header { padding-top: 2px; }
    .st-form .fi-section-header-heading { font-size: 14px; font-weight: 600; }
    .st-form .fi-section-header-description { margin-top: 4px; font-size: 13px; line-height: 1.5; color: var(--cx-muted); }
    .st-form .fi-aside > .fi-section-content-ctn {
        background: transparent;
        box-shadow: none;
        border-radius: 0;
    }
    .st-form .fi-aside .fi-section-content { padding: 0; }

    /* Status e link do aviso de cookies */
    .st-status { display: inline-flex; align-items: center; gap: 8px; margin-right: 16px; font-size: 13px; font-weight: 500; color: var(--tone-fg); }
    .st-link { font-size: 13px; font-weight: 500; color: rgb(var(--primary-600)); }
    .st-link:hover { text-decoration: underline; }
    .dark .st-link { color: rgb(var(--primary-400)); }

    /* Rodapé: estado das alterações, fixo na base enquanto o formulário rola */
    .st-foot {
        position: sticky;
        bottom: 0;
        z-index: 5;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 10px 28px;
        font-size: 12px;
        border-top: 1px solid var(--cx-border);
        border-radius: 0 0 var(--cx-radius) var(--cx-radius);
        background: var(--cx-surface);
    }
    .st-state { display: inline-flex; align-items: center; gap: 8px; font-weight: 500; color: var(--cx-muted); }
    .st-state.is-saved { --tone: var(--success-500); }
    .st-state.is-dirty { --tone: var(--warning-500); color: var(--cx-text); }
    .st-hint { color: var(--cx-subtle); }
    .st-hint kbd {
        padding: 1px 5px;
        border-radius: 4px;
        font: inherit;
        font-size: 11px;
        box-shadow: inset 0 0 0 1px var(--cx-border-strong);
    }

    @media (max-width: 767px) {
        .st-form { padding: 0 16px; }
        .st-foot { padding-inline: 16px; }
        .st-hint { display: none; }
    }
</style>
