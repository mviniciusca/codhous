{{-- Newsletter: enviar mensagem (CSS puro). Prefixo nl-; tokens e primitivas vêm de filament/admin/ui --}}
@once
<style>
    .nl-host { container: nl / inline-size; }
    .nl { display: grid; gap: 16px; }
    .nl-muted { color: var(--cx-muted); }

    /* ---------- Etapas ---------- */
    .nl-steps {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        margin: 0;
        padding: 0;
        list-style: none;
        background: var(--cx-surface);
        border: 1px solid var(--cx-border);
        border-radius: var(--cx-radius);
        box-shadow: var(--cx-shadow);
        overflow: hidden;
    }
    .nl-step {
        position: relative;
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
        padding: 14px 20px;
    }
    .nl-step + .nl-step { border-left: 1px solid var(--cx-border); }
    .nl-step.is-current { background: rgba(var(--primary-500), .07); }
    .nl-step.is-current::after {
        content: "";
        position: absolute;
        inset: auto 0 0 0;
        height: 2px;
        background: rgb(var(--primary-600));
    }
    .nl-step-num {
        display: grid;
        place-items: center;
        flex: none;
        width: 36px;
        height: 36px;
        border-radius: 999px;
        font-size: 14px;
        font-weight: 600;
        color: var(--cx-muted);
        background: var(--cx-track);
        box-shadow: inset 0 0 0 1px var(--cx-border-strong);
    }
    .nl-step.is-current .nl-step-num { color: #fff; background: rgb(var(--primary-600)); box-shadow: 0 0 0 4px rgba(var(--primary-500), .18); }
    .nl-step.is-done .nl-step-num { color: rgb(var(--primary-600)); background: rgba(var(--primary-500), .12); box-shadow: inset 0 0 0 1px rgba(var(--primary-500), .35); }
    .dark .nl-step.is-done .nl-step-num { color: rgb(var(--primary-300)); }
    .nl-step-text { display: grid; gap: 1px; min-width: 0; }
    .nl-step-label { font-size: 13.5px; font-weight: 600; }
    .nl-step.is-next .nl-step-label { color: var(--cx-muted); }
    .nl-step-desc { font-size: 12.5px; color: var(--cx-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    /* ---------- Workspace ---------- */
    .nl-work {
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr);
        gap: 16px;
        align-items: stretch;
    }
    .nl-panel {
        display: flex;
        flex-direction: column;
        min-width: 0;
        background: var(--cx-surface);
        border: 1px solid var(--cx-border);
        border-radius: var(--cx-radius);
        box-shadow: var(--cx-shadow);
    }
    .nl-panel-head { padding: 18px 20px 0; }
    .nl-panel-title { margin: 0; font-size: 15px; font-weight: 600; }
    .nl-panel-desc { margin: 2px 0 0; font-size: 12.5px; color: var(--cx-muted); }
    .nl-panel-body { display: grid; gap: 16px; padding: 16px 20px 20px; }

    /* Helper do editor: frase à esquerda, contador à direita */
    .nl-helper { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 4px 12px; }
    .nl-helper-count { font-variant-numeric: tabular-nums; white-space: nowrap; }
    .nl-form .fi-fo-rich-editor trix-editor { min-height: 14rem; max-height: 28rem; overflow-y: auto; }

    /* ---------- Prévia ---------- */
    .nl-preview-head {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px 16px;
    }
    .nl-seg { display: inline-flex; flex: none; padding: 3px; gap: 2px; border-radius: 11px; background: var(--cx-track); }
    .nl-seg-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 30px;
        padding: 0 11px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        color: var(--cx-muted);
        white-space: nowrap;
        transition: color .15s ease, background-color .15s ease;
    }
    .nl-seg-btn svg { width: 16px; height: 16px; flex: none; }
    .nl-seg-btn:hover { color: var(--cx-text); }
    .nl-seg-btn.is-active { color: #fff; background: rgb(var(--primary-600)); box-shadow: 0 1px 3px rgba(15, 23, 42, .18); }

    .nl-stage {
        position: relative;
        display: flex;
        justify-content: center;
        flex: 1;
        min-height: 560px;
        margin: 16px 20px 20px;
        padding: 16px;
        border-radius: 12px;
        background: var(--cx-surface-2);
        border: 1px solid var(--cx-border);
        overflow: hidden;
    }
    .nl-frame {
        position: relative;
        max-width: 100%;
        border-radius: 8px;
        background: #f3f4f6;
        box-shadow: 0 0 0 1px var(--cx-border), 0 16px 32px -20px rgba(0, 0, 0, .45);
        overflow: hidden;
        transition: width .2s ease;
    }
    .nl-frame iframe { position: absolute; inset: 0; width: 100%; height: 100%; border: 0; background: #f3f4f6; }

    /* ---------- Revisão ---------- */
    .nl-summary { display: grid; margin: 0; border: 1px solid var(--cx-border); border-radius: 12px; overflow: hidden; }
    .nl-summary-row {
        display: grid;
        grid-template-columns: 140px minmax(0, 1fr);
        gap: 12px;
        padding: 12px 14px;
        font-size: 13.5px;
    }
    .nl-summary-row + .nl-summary-row { border-top: 1px solid var(--cx-border); }
    .nl-summary dt { display: flex; align-items: center; gap: 8px; align-self: start; font-size: 12.5px; color: var(--cx-muted); }
    .nl-summary dd { display: grid; gap: 8px; margin: 0; min-width: 0; overflow-wrap: anywhere; }
    .nl-summary-strong { font-weight: 600; }
    .nl-people { display: grid; gap: 6px; margin: 0; padding: 0; list-style: none; }
    .nl-people li { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .nl-avatar {
        display: grid;
        place-items: center;
        flex: none;
        width: 28px;
        height: 28px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        color: rgb(var(--primary-600));
        background: rgba(var(--primary-500), .12);
    }
    .dark .nl-avatar { color: rgb(var(--primary-300)); }
    .nl-person { display: grid; min-width: 0; font-size: 13px; line-height: 1.35; }
    .nl-person > span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    .nl-alert {
        display: flex;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 12px;
        color: var(--tone-fg);
        background: rgba(var(--tone), .08);
        box-shadow: inset 0 0 0 1px rgba(var(--tone), .3);
    }
    .nl-alert-body { display: grid; gap: 6px; font-size: 13px; color: var(--cx-text); }
    .nl-alert-body strong { color: var(--tone-fg); font-weight: 600; }
    .nl-check { display: inline-flex; align-items: center; gap: 8px; font-weight: 500; cursor: pointer; }
    .nl-check input { width: 16px; height: 16px; border-radius: 4px; accent-color: rgb(var(--primary-600)); }
    .nl-error { font-size: 12.5px; color: rgb(var(--danger-600)); }
    .dark .nl-error { color: rgb(var(--danger-400)); }
    .nl-note { display: flex; gap: 8px; margin: 0; font-size: 12.5px; line-height: 1.5; color: var(--cx-muted); }
    .nl-note svg { margin-top: 2px; }

    /* ---------- Barra inferior ---------- */
    .nl-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 14px 24px;
        padding: 14px 20px;
        background: var(--cx-surface);
        border: 1px solid var(--cx-border);
        border-radius: var(--cx-radius);
        box-shadow: var(--cx-shadow);
    }
    .nl-bar-count { display: flex; align-items: center; gap: 14px; flex: none; }
    .nl-bar-count > span:last-child { display: grid; line-height: 1.2; }
    .nl-bar-icon {
        display: grid;
        place-items: center;
        width: 48px;
        height: 48px;
        border-radius: 12px;
        color: rgb(var(--primary-600));
        background: rgba(var(--primary-500), .12);
    }
    .dark .nl-bar-icon { color: rgb(var(--primary-300)); }
    .nl-bar-icon svg { width: 24px; height: 24px; }
    .nl-bar-label { font-size: 12.5px; color: var(--cx-muted); }
    .nl-bar-count > span:last-child .nl-bar-label:first-child { font-weight: 600; color: var(--cx-text); }
    .nl-bar-value { font-size: 24px; font-weight: 600; letter-spacing: -.02em; }
    .nl-bar-text { flex: 1 1 260px; max-width: 460px; margin: 0; font-size: 13px; color: var(--cx-muted); }
    .nl-bar-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin-left: auto; }

    /* ---------- Conclusão ---------- */
    .nl-result { gap: 18px; padding: 22px 24px 24px; }
    .nl-result-head { display: flex; align-items: flex-start; gap: 14px; }
    .nl-result-icon {
        display: grid;
        place-items: center;
        flex: none;
        width: 44px;
        height: 44px;
        border-radius: 999px;
        color: var(--tone-fg);
        background: rgba(var(--tone), .12);
    }
    .nl-result-icon svg { width: 24px; height: 24px; }
    .nl-result-title { margin: 0; font-size: 17px; font-weight: 600; }
    .nl-result-subject { display: grid; gap: 2px; font-size: 13.5px; }
    .nl-result-subject .nl-muted { font-size: 12px; }

    .nl-progress { height: 8px; border-radius: 999px; background: var(--cx-track); overflow: hidden; }
    .nl-progress > span { display: block; height: 100%; border-radius: inherit; background: rgb(var(--tone)); transition: width .4s ease; }

    .nl-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        margin: 0;
        border: 1px solid var(--cx-border);
        border-radius: 12px;
        overflow: hidden;
    }
    .nl-stats > div { display: grid; gap: 4px; padding: 12px 16px; }
    .nl-stats > div + div { border-left: 1px solid var(--cx-border); }
    .nl-stats dt { font-size: 12.5px; color: var(--cx-muted); }
    .nl-stats dd { margin: 0; font-size: 22px; font-weight: 600; letter-spacing: -.02em; }
    .nl-stats .cx-tone-success dd,
    .nl-stats .cx-tone-danger dd { color: var(--tone-fg); }

    .nl-failures { display: grid; gap: 10px; font-size: 13px; }
    .nl-failures-title { display: flex; align-items: center; gap: 8px; margin: 0; font-size: 13.5px; font-weight: 600; }
    .nl-failures-title svg { color: rgb(var(--danger-500)); }
    .nl-table-wrap { border: 1px solid var(--cx-border); border-radius: 12px; overflow-x: auto; }
    .nl-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .nl-table th { padding: 10px 14px; font-weight: 600; text-align: left; white-space: nowrap; background: var(--cx-surface-2); }
    .nl-table td { padding: 10px 14px; vertical-align: top; border-top: 1px solid var(--cx-border); }
    .nl-table td:first-child { white-space: nowrap; }
    .nl-result-actions { display: flex; flex-wrap: wrap; justify-content: flex-end; gap: 10px; }

    /* ---------- Responsivo (largura disponível ao lado do menu) ---------- */
    @container nl (max-width: 1080px) {
        .nl-work { grid-template-columns: minmax(0, 1fr); }
        .nl-stage { min-height: 520px; }
    }
    @container nl (max-width: 760px) {
        .nl-step { padding: 12px; gap: 10px; }
        .nl-step-num { width: 30px; height: 30px; font-size: 13px; }
        .nl-step-desc { display: none; }
        .nl-seg-label { display: none; }
        .nl-seg-btn { width: 36px; padding: 0; justify-content: center; }
        .nl-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .nl-stats > div:nth-child(3) { border-left: 0; }
        .nl-stats > div:nth-child(n + 3) { border-top: 1px solid var(--cx-border); }
        .nl-summary-row { grid-template-columns: minmax(0, 1fr); gap: 6px; }
    }
    @container nl (max-width: 520px) {
        .nl-steps { grid-template-columns: repeat(3, auto); justify-content: space-between; }
        .nl-step + .nl-step { border-left: 0; }
        .nl-step:not(.is-current) .nl-step-label { display: none; }
        .nl-panel-head { padding: 16px 16px 0; }
        .nl-panel-body { padding: 14px 16px 16px; }
        .nl-stage { min-height: 480px; margin: 14px 12px 12px; padding: 8px; }
        .nl-bar { padding: 14px 16px; }
        .nl-bar-actions { width: 100%; }
        .nl-bar-actions > * { flex: 1 1 auto; }
        .nl-result { padding: 18px 16px; }
    }

    @media (prefers-reduced-motion: reduce) {
        .nl-frame, .nl-progress > span, .nl-seg-btn { transition: none; }
    }
</style>
@endonce
