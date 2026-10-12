{{-- Primitivas visuais compartilhadas (tokens, tons, badge, estado vazio). Use a classe .cx-ui na raiz da tela --}}
<style>
    .cx-ui {
        --cx-surface: #fff;
        --cx-surface-2: rgb(var(--gray-50));
        --cx-hover: rgba(var(--gray-500), .06);
        --cx-border: rgba(var(--gray-950), .08);
        --cx-border-strong: rgba(var(--gray-950), .14);
        --cx-text: rgb(var(--gray-950));
        --cx-muted: rgb(var(--gray-500));
        --cx-subtle: rgb(var(--gray-400));
        --cx-track: rgba(var(--gray-500), .14);
        --cx-shadow: 0 1px 2px rgba(15, 23, 42, .04), 0 1px 1px rgba(15, 23, 42, .02);
        --cx-radius: 14px;

        color: var(--cx-text);
        font-variant-numeric: tabular-nums;
    }
    .dark .cx-ui {
        --cx-surface: rgb(var(--gray-900));
        --cx-surface-2: rgba(255, 255, 255, .025);
        --cx-hover: rgba(255, 255, 255, .035);
        --cx-border: rgba(255, 255, 255, .07);
        --cx-border-strong: rgba(255, 255, 255, .12);
        --cx-text: rgb(var(--gray-50));
        --cx-muted: rgb(var(--gray-400));
        --cx-subtle: rgb(var(--gray-500));
        --cx-track: rgba(255, 255, 255, .07);
        --cx-shadow: 0 1px 0 rgba(255, 255, 255, .03) inset, 0 12px 32px -16px rgba(0, 0, 0, .6);
    }

    /* Tons semânticos: --tone (cor base) e --tone-fg (texto legível no tema) */
    .cx-tone-gray    { --tone: var(--gray-500);    --tone-fg: rgb(var(--gray-600)); }
    .cx-tone-primary { --tone: var(--primary-500); --tone-fg: rgb(var(--primary-600)); }
    .cx-tone-success { --tone: var(--success-500); --tone-fg: rgb(var(--success-700)); }
    .cx-tone-warning { --tone: var(--warning-500); --tone-fg: rgb(var(--warning-700)); }
    .cx-tone-danger  { --tone: var(--danger-500);  --tone-fg: rgb(var(--danger-600)); }
    .cx-tone-info    { --tone: var(--info-500);    --tone-fg: rgb(var(--info-600)); }
    .dark .cx-tone-gray    { --tone-fg: rgb(var(--gray-300)); }
    .dark .cx-tone-primary { --tone-fg: rgb(var(--primary-300)); }
    .dark .cx-tone-success { --tone-fg: rgb(var(--success-400)); }
    .dark .cx-tone-warning { --tone-fg: rgb(var(--warning-400)); }
    .dark .cx-tone-danger  { --tone-fg: rgb(var(--danger-400)); }
    .dark .cx-tone-info    { --tone-fg: rgb(var(--info-400)); }

    .cx-icon    { width: 20px; height: 20px; flex: none; }
    .cx-icon-sm { width: 16px; height: 16px; flex: none; }
    .cx-icon-xs { width: 14px; height: 14px; flex: none; opacity: .7; }
    .cx-sr {
        position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
        overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;
    }
    .cx-dot {
        width: 7px; height: 7px; border-radius: 999px; flex: none;
        background: rgb(var(--tone, var(--gray-400)));
    }
    .cx-ui a:focus-visible,
    .cx-ui button:focus-visible {
        outline: 2px solid rgb(var(--primary-500));
        outline-offset: 2px;
    }

    .cx-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        flex: none;
        padding: 6px 10px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        color: rgb(var(--primary-600));
        text-decoration: none;
        transition: background-color .15s ease;
    }
    .cx-link:hover { background: rgba(var(--primary-500), .1); }
    .dark .cx-link { color: rgb(var(--primary-400)); }

    /* ---------- Faixa de indicadores ---------- */
    /*
        Envolva em .cx-kpis-ctn: as colunas se ajustam à largura disponível.
        Atenção: container-type cria bloco de contenção para position: fixed. Não use
        container queries em elementos que envolvem dropdowns, tabelas ou modais do Filament.
    */
    .cx-kpis-ctn { container: kpis / inline-size; }
    .cx-kpis {
        display: grid;
        grid-auto-flow: column;
        grid-auto-columns: minmax(0, 1fr);
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

    @container kpis (max-width: 760px) {
        .cx-kpis { grid-auto-flow: row; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .cx-kpi:nth-child(odd) { border-left: 0; }
        .cx-kpi:nth-child(n + 3) { border-top: 1px solid var(--cx-border); }
    }
    @container kpis (max-width: 480px) {
        .cx-kpis { grid-template-columns: minmax(0, 1fr); }
        .cx-kpi + .cx-kpi { border-left: 0; border-top: 1px solid var(--cx-border); }
        .cx-kpi { padding: 16px; }
        .cx-kpi-value { font-size: 24px; }
    }

    /* ---------- Badge ---------- */
    .cx-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        justify-self: start;
        padding: 3px 9px 3px 8px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 500;
        white-space: nowrap;
        color: var(--tone-fg);
        background: rgba(var(--tone), .12);
        box-shadow: inset 0 0 0 1px rgba(var(--tone), .2);
    }

    /* ---------- Estado vazio ---------- */
    .cx-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        padding: 32px 20px;
        text-align: center;
    }
    .cx-empty-icon {
        display: grid;
        place-items: center;
        width: 44px;
        height: 44px;
        margin-bottom: 8px;
        border-radius: 999px;
        color: var(--tone-fg);
        background: rgba(var(--tone), .12);
    }
    .cx-empty-title { font-size: 14px; font-weight: 600; }
    .cx-empty-desc { max-width: 300px; font-size: 13px; color: var(--cx-muted); }
    .cx-empty .fi-btn { margin-top: 12px; }

</style>
