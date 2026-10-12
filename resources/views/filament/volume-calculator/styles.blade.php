{{-- Calculadora de volume (CSS puro). Prefixo vc-; tokens e primitivas vêm de filament/admin/ui --}}
@once
<style>
    .vc { display: grid; gap: 16px; }
    .vc-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.25fr) minmax(0, 1fr);
        gap: 16px;
        align-items: start;
    }
    .vc--modal .vc-grid { grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr); }
    @media (max-width: 900px) {
        .vc-grid, .vc--modal .vc-grid { grid-template-columns: minmax(0, 1fr); }
    }

    .vc-card {
        display: grid;
        gap: 16px;
        padding: 20px;
        background: var(--cx-surface);
        border: 1px solid var(--cx-border);
        border-radius: var(--cx-radius);
        box-shadow: var(--cx-shadow);
    }
    .vc--modal .vc-card { box-shadow: none; }
    .vc-side { display: grid; gap: 16px; }

    /* Abas de formato */
    .vc-tabs {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
        padding: 4px;
        border-radius: 12px;
        background: var(--cx-surface-2);
        box-shadow: inset 0 0 0 1px var(--cx-border);
    }
    .vc-tab {
        display: inline-flex;
        flex: 1 0 auto;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
        color: var(--cx-muted);
        transition: background-color .15s ease, color .15s ease;
    }
    .vc-tab:hover { color: var(--cx-text); background: var(--cx-hover); }
    .vc-tab.is-active {
        color: #fff;
        background: rgb(var(--primary-600));
        box-shadow: 0 6px 16px -8px rgba(var(--primary-500), .8);
    }
    .vc-desc { font-size: 13px; line-height: 1.5; color: var(--cx-muted); margin-top: -4px; }

    /* Campos */
    .vc-fields { display: grid; gap: 14px; }
    .vc-field, .vc-margin { display: flex; gap: 12px; align-items: flex-start; }
    .vc-field-icon {
        display: grid;
        place-items: center;
        flex: none;
        width: 40px;
        height: 40px;
        margin-top: 22px;
        border-radius: 10px;
        color: var(--cx-muted);
        background: var(--cx-surface-2);
        box-shadow: inset 0 0 0 1px var(--cx-border);
    }
    .vc-margin .vc-field-icon { margin-top: 0; }
    .vc-field-body { flex: 1; min-width: 0; display: grid; gap: 6px; }
    .vc-label {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
    }
    .vc-unit { font-size: 11px; font-weight: 500; letter-spacing: .04em; text-transform: uppercase; color: var(--cx-subtle); }
    .vc-input-wrap { position: relative; }
    .vc-input, .vc-select {
        width: 100%;
        min-height: 42px;
        padding: 9px 12px;
        border: 0;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 500;
        color: var(--cx-text);
        background: var(--cx-surface-2);
        box-shadow: inset 0 0 0 1px var(--cx-border-strong);
        transition: box-shadow .15s ease;
    }
    .vc-input-wrap .vc-input { padding-right: 44px; font-variant-numeric: tabular-nums; }
    .vc-input::placeholder { color: var(--cx-subtle); font-weight: 400; }
    .vc-input:focus, .vc-select:focus { outline: none; box-shadow: inset 0 0 0 2px rgb(var(--primary-500)); }
    .vc-suffix {
        position: absolute;
        top: 50%;
        right: 12px;
        transform: translateY(-50%);
        font-size: 13px;
        color: var(--cx-muted);
        pointer-events: none;
    }
    .has-error .vc-input { box-shadow: inset 0 0 0 2px rgb(var(--danger-500)); }
    .vc-help { font-size: 12px; line-height: 1.5; color: var(--cx-muted); }
    .vc-error { font-size: 12px; font-weight: 500; color: rgb(var(--danger-600)); }
    .dark .vc-error { color: rgb(var(--danger-400)); }

    .vc-margin {
        align-items: center;
        padding: 12px;
        border-radius: 12px;
        background: var(--cx-surface-2);
        box-shadow: inset 0 0 0 1px var(--cx-border);
    }
    .vc-margin .vc-field-icon { background: var(--cx-surface); }
    .vc-margin-text { flex: 1; min-width: 0; display: grid; gap: 2px; }
    .vc-select-wrap { position: relative; flex: none; }
    .vc-select-wrap > .cx-icon-sm { position: absolute; top: 50%; right: 12px; transform: translateY(-50%); color: var(--cx-muted); pointer-events: none; }
    .vc-select {
        width: auto;
        min-width: 150px;
        padding-right: 36px;
        font-size: 13px;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        background-color: var(--cx-surface);
    }
    /* Vence o select:not([size]) do Filament, que desenha a própria seta */
    .vc .vc-select { background-image: none; }
    @media (max-width: 560px) {
        .vc-margin { flex-wrap: wrap; }
        .vc-select-wrap, .vc-select { width: 100%; }
    }

    .vc-alert {
        display: flex;
        gap: 8px;
        align-items: flex-start;
        padding: 10px 12px;
        border-radius: 10px;
        font-size: 13px;
        color: rgb(var(--danger-700));
        background: rgba(var(--danger-500), .1);
    }
    .dark .vc-alert { color: rgb(var(--danger-300)); }

    .vc-save { display: grid; gap: 10px; padding-top: 16px; border-top: 1px solid var(--cx-border); }
    .vc-save > .vc-input { font-size: 13px; }
    .vc-actions { display: flex; flex-wrap: wrap; gap: 8px; }
    .vc-actions .fi-btn:first-child { flex: 1; }

    /* Resultado */
    .vc-result {
        position: relative;
        display: grid;
        gap: 6px;
        padding: 20px;
        overflow: hidden;
        border-radius: var(--cx-radius);
        color: #fff;
        background:
            radial-gradient(120% 140% at 100% 0%, rgba(var(--primary-400), .35), transparent 55%),
            linear-gradient(160deg, rgb(var(--primary-800)), rgb(var(--primary-950)));
        box-shadow: 0 18px 40px -24px rgba(var(--primary-600), .9);
    }
    .vc-result.is-empty {
        color: var(--cx-text);
        background: var(--cx-surface);
        box-shadow: inset 0 0 0 1px var(--cx-border);
    }
    .vc-result-head { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
    .vc-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .08em;
        text-transform: uppercase;
        opacity: .8;
    }
    .vc-copy {
        display: grid;
        place-items: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        color: inherit;
        background: rgba(255, 255, 255, .1);
        transition: background-color .15s ease;
    }
    .vc-copy:hover { background: rgba(255, 255, 255, .2); }
    .vc-result-value {
        display: flex;
        align-items: baseline;
        gap: 8px;
        font-size: 52px;
        line-height: 1.05;
        font-weight: 700;
        letter-spacing: -.03em;
        font-variant-numeric: tabular-nums;
    }
    .vc-result-value small { font-size: 22px; font-weight: 500; letter-spacing: 0; opacity: .85; }
    .vc-result.is-empty .vc-result-value { color: var(--cx-subtle); }
    .vc-result-note { font-size: 13px; opacity: .8; }
    .vc-result.is-empty .vc-result-note { color: var(--cx-muted); opacity: 1; }

    .vc-breakdown {
        display: grid;
        gap: 6px;
        margin-top: 10px;
        padding-top: 12px;
        border-top: 1px solid rgba(255, 255, 255, .15);
        font-size: 13px;
    }
    .vc-breakdown > div { display: flex; justify-content: space-between; gap: 12px; }
    .vc-breakdown dt { opacity: .75; }
    .vc-breakdown dd { font-variant-numeric: tabular-nums; font-weight: 500; }
    .vc-breakdown .is-total { margin-top: 2px; padding-top: 8px; border-top: 1px dashed rgba(255, 255, 255, .2); font-weight: 600; }
    .vc-breakdown .is-total dt { opacity: 1; }

    /* Pedido mínimo do caminhão */
    .vc-advice {
        gap: 12px;
        background: linear-gradient(rgba(var(--tone), .08), rgba(var(--tone), .08)), var(--cx-surface);
        border-color: rgba(var(--tone), .35);
    }
    .vc-advice-head { display: flex; gap: 12px; align-items: flex-start; }
    .vc-advice-head .vc-title { color: var(--tone-fg); }
    .vc-advice-head .vc-help { margin-top: 2px; color: var(--cx-text); opacity: .85; }
    .vc-advice-icon {
        display: grid;
        place-items: center;
        flex: none;
        width: 32px;
        height: 32px;
        border-radius: 9px;
        color: var(--tone-fg);
        background: rgba(var(--tone), .16);
    }
    .vc-advice-list { display: grid; gap: 8px; padding-left: 44px; font-size: 12.5px; line-height: 1.45; }
    .vc-advice-list li { display: flex; gap: 8px; align-items: flex-start; }
    .vc-advice-list .cx-icon-sm { margin-top: 1px; color: var(--tone-fg); }
    .vc-advice-list strong { font-weight: 600; }
    @media (max-width: 560px) { .vc-advice-list { padding-left: 0; } }

    /* Memória de cálculo */
    .vc-title { display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 600; }
    .vc-title .cx-icon-sm { color: var(--cx-muted); }
    .vc-steps { display: grid; gap: 12px; counter-reset: vc-step; }
    .vc-steps > li {
        position: relative;
        display: grid;
        gap: 4px;
        padding-left: 30px;
        counter-increment: vc-step;
    }
    .vc-steps > li::before {
        content: counter(vc-step);
        position: absolute;
        left: 0;
        top: 0;
        display: grid;
        place-items: center;
        width: 20px;
        height: 20px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        color: rgb(var(--primary-600));
        background: rgba(var(--primary-500), .12);
    }
    .dark .vc-steps > li::before { color: rgb(var(--primary-300)); }
    .vc-step-label { font-size: 12px; font-weight: 600; color: var(--cx-muted); }
    .vc-code {
        display: block;
        padding: 6px 10px;
        border-radius: 8px;
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-size: 12.5px;
        line-height: 1.5;
        overflow-wrap: anywhere;
        background: var(--cx-surface-2);
        box-shadow: inset 0 0 0 1px var(--cx-border);
    }
    .vc-code.is-inline { display: inline; padding: 1px 6px; }
    .vc-conversions { display: grid; gap: 2px; font-size: 13px; }
    .vc-footnote {
        padding-top: 12px;
        border-top: 1px solid var(--cx-border);
        font-size: 11.5px;
        line-height: 1.5;
        color: var(--cx-subtle);
    }
    .vc-version { display: block; margin-top: 4px; font-weight: 500; color: var(--cx-muted); }

    /* Ilustração */
    .vc-shape { width: 100%; max-width: 320px; height: auto; margin: 0 auto; }
    .vc-face-top { fill: rgba(var(--primary-500), .28); stroke: rgb(var(--primary-400)); stroke-width: 1.2; stroke-linejoin: round; }
    .vc-face-left { fill: rgba(var(--primary-500), .16); stroke: rgb(var(--primary-400)); stroke-width: 1.2; stroke-linejoin: round; }
    .vc-face-right { fill: rgba(var(--primary-500), .09); stroke: rgb(var(--primary-400)); stroke-width: 1.2; stroke-linejoin: round; }
    .vc-grid-line { fill: none; stroke: rgba(var(--primary-400), .25); stroke-width: .8; }
    .vc-dim { fill: none; stroke: var(--cx-muted); stroke-width: 1.2; stroke-dasharray: 3 3; }
    .vc-dim-label { fill: var(--cx-muted); font-size: 12px; font-weight: 500; }
    .vc-dim-note { fill: var(--cx-subtle); font-size: 11px; }
    .vc-formula-glyph { fill: rgb(var(--primary-400)); font-size: 30px; font-style: italic; font-weight: 600; }

    /* Últimos cálculos (página) */
    .vc-recent ul { display: grid; }
    .vc-recent li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 10px 0;
        border-top: 1px solid var(--cx-border);
    }
    .vc-recent-main { display: grid; gap: 2px; min-width: 0; }
    .vc-recent-main strong { font-size: 13px; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .vc-recent-main small { font-size: 12px; color: var(--cx-muted); }
    .vc-recent-volume { font-size: 14px; font-weight: 600; font-variant-numeric: tabular-nums; white-space: nowrap; }

    .vc-loading { padding: 48px 16px; text-align: center; font-size: 13px; color: rgb(var(--gray-500)); }
</style>
@endonce
