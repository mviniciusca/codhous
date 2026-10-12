{{-- Seções do site (CSS puro). Prefixo cs-; tokens e primitivas vêm de filament/admin/ui; skeletons (sk-/stp-) vêm da biblioteca de componentes --}}
@once
<style>
    .cs { display: grid; gap: 16px; }

    /* Indicadores clicáveis (botões que aplicam filtro) */
    .cs .cx-kpi { font: inherit; text-align: left; border: 0; background: none; cursor: pointer; }
    .cs .cx-kpi.is-active::before { opacity: .9; }
    .cs .cx-kpi-label svg { color: var(--tone-fg); }
    .cs .cx-kpi-value { font-size: 24px; }
    /* No celular os quatro indicadores ficam em 2×2, em vez de empilhados */
    @container kpis (max-width: 480px) {
        .cs .cx-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .cs .cx-kpi { padding: 12px 14px; }
        .cs .cx-kpi:nth-child(even) { border-left: 1px solid var(--cx-border); }
        .cs .cx-kpi:nth-child(2) { border-top: 0; }
        .cs .cx-kpi-value { font-size: 20px; }
    }

    /* ---------- Barra de ferramentas ---------- */
    .cs-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }
    .cs-search { flex: 1 1 260px; min-width: 0; }
    .cs-filter { flex: 0 1 220px; min-width: 160px; }
    .cs-toolbar-end { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin-left: auto; }

    .cs-seg { display: inline-flex; padding: 3px; gap: 2px; border-radius: 11px; background: var(--cx-track); }
    .cs-seg-btn {
        display: inline-flex; align-items: center; gap: 6px;
        height: 30px; padding: 0 12px; border-radius: 8px;
        font-size: 13px; font-weight: 500; color: var(--cx-muted);
        white-space: nowrap; transition: color .15s ease, background-color .15s ease;
    }
    .cs-seg-btn svg { width: 18px; height: 18px; flex: none; }
    .cs-seg-btn--icon { width: 34px; padding: 0; justify-content: center; }
    .cs-seg-btn:hover { color: var(--cx-text); }
    .cs-seg-btn.is-active { color: rgb(var(--primary-600)); background: #fff; box-shadow: 0 1px 3px rgba(15, 23, 42, .12); }
    .dark .cs-seg-btn.is-active { color: rgb(var(--primary-300)); background: rgba(255, 255, 255, .1); box-shadow: none; }
    .cs-seg-count { font-size: 11px; font-weight: 600; opacity: .75; }

    /* ---------- Faixas de aviso e de seleção ---------- */
    .cs-bar {
        display: flex; flex-wrap: wrap; align-items: center; gap: 8px 12px;
        padding: 8px 10px 8px 14px; border-radius: 12px;
        font-size: 13px; color: var(--cx-muted);
        background: var(--cx-surface); border: 1px solid var(--cx-border);
    }
    .cs-bar svg { width: 16px; height: 16px; flex: none; }
    .cs-bar strong { color: var(--cx-text); font-weight: 600; }
    .cs-bar-actions { display: flex; flex-wrap: wrap; gap: 6px; margin-left: auto; }
    .cs-bar--selection { border-color: rgba(var(--primary-500), .4); background: rgba(var(--primary-500), .06); }
    .cs-text-btn { font-size: 13px; font-weight: 500; color: rgb(var(--primary-600)); }
    .dark .cs-text-btn { color: rgb(var(--primary-400)); }
    .cs-text-btn:hover { text-decoration: underline; }

    /* ---------- Resultados ---------- */
    .cs-results { position: relative; transition: opacity .15s ease; }
    .cs-results.is-loading { opacity: .55; pointer-events: none; }
    .cs-results.is-loading::before {
        content: ""; position: absolute; inset: -8px 0 auto; height: 2px; border-radius: 2px;
        background: linear-gradient(90deg, transparent, rgb(var(--primary-500)), transparent);
        background-size: 50% 100%; background-repeat: no-repeat;
        animation: cs-progress 1s linear infinite;
    }
    @keyframes cs-progress { from { background-position: -50% 0; } to { background-position: 150% 0; } }

    /* Grade: até 3 colunas, cada uma com no mínimo 300px */
    .cs-grid {
        display: grid;
        gap: 16px;
        grid-template-columns: repeat(auto-fill, minmax(max(min(100%, 300px), calc((100% - 32px) / 3)), 1fr));
    }
    .cs-grid--list { grid-template-columns: minmax(0, 1fr); gap: 8px; }

    /* ---------- Card ---------- */
    .cs-card {
        position: relative;
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        grid-template-areas: "lead switch" "thumb thumb" "info info" "usage usage" "foot foot";
        gap: 10px;
        padding: 10px 12px 6px;
        min-width: 0;
        border-radius: 12px;
        background: var(--cx-surface);
        border: 1px solid var(--cx-border);
        box-shadow: var(--cx-shadow);
        transition: border-color .15s ease, background-color .15s ease, opacity .15s ease;
    }
    .cs-card:hover { border-color: var(--cx-border-strong); }
    .cs-card.is-inactive .cs-thumb,
    .cs-card.is-inactive .cs-info { opacity: .6; }
    .cs-card.is-selected { border-color: rgb(var(--primary-500)); box-shadow: 0 0 0 1px rgb(var(--primary-500)), 0 8px 24px -12px rgba(var(--primary-500), .5); }
    .cs-card.is-dragging { opacity: .45; border-style: dashed; border-color: rgb(var(--primary-500)); }

    .cs-lead { grid-area: lead; display: flex; align-items: center; gap: 6px; min-width: 0; }
    .cs-check { width: 16px; height: 16px; border-radius: 4px; }
    .cs-handle {
        display: grid; place-items: center; width: 24px; height: 26px; border-radius: 6px;
        color: var(--cx-subtle); cursor: grab;
    }
    .cs-handle:hover { color: var(--cx-text); background: var(--cx-hover); }
    .cs-handle:active { cursor: grabbing; }
    .cs-handle svg { width: 16px; height: 16px; }
    .cs-pos {
        display: inline-grid; place-items: center; min-width: 22px; height: 22px; padding: 0 6px;
        border-radius: 6px; font-size: 11px; font-weight: 600;
        color: var(--cx-muted); background: var(--cx-track);
    }

    /* Interruptor ativo/inativo */
    .cs-switch-wrap { grid-area: switch; display: flex; align-items: center; }
    .cs-switch {
        position: relative; width: 38px; height: 22px; flex: none; border-radius: 999px;
        background: var(--cx-track); box-shadow: inset 0 0 0 1px var(--cx-border-strong);
        transition: background-color .18s ease;
    }
    .cs-switch-knob {
        position: absolute; top: 3px; left: 3px; display: grid; place-items: center;
        width: 16px; height: 16px; border-radius: 999px; background: #fff;
        box-shadow: 0 1px 2px rgba(0, 0, 0, .25); transition: transform .18s ease;
    }
    .cs-switch-knob svg { width: 11px; height: 11px; color: rgb(var(--primary-600)); opacity: 0; }
    .cs-switch.is-on { background: rgb(var(--primary-600)); box-shadow: none; }
    .cs-switch.is-on .cs-switch-knob { transform: translateX(16px); }
    .cs-switch.is-on .cs-switch-knob svg { opacity: 1; }
    .cs-switch:disabled { opacity: .5; cursor: not-allowed; }

    /* Miniatura: o skeleton é desenhado para ~300×158px */
    .cs-thumb { grid-area: thumb; display: block; --stp-thumb-h: 150px; pointer-events: auto; }
    .cs-thumb-frame { position: absolute; inset: 0; }
    .cs-card:hover .cs-thumb { --stp-sk: rgba(var(--gray-500), .21); }
    .dark .cs-card:hover .cs-thumb { --stp-sk: rgba(255, 255, 255, .12); }

    .cs-info { grid-area: info; display: flex; align-items: flex-start; gap: 10px; min-width: 0; }
    .cs-icon {
        display: grid; place-items: center; width: 32px; height: 32px; flex: none; border-radius: 9px;
        color: var(--cx-muted); background: var(--cx-surface-2); border: 1px solid var(--cx-border);
    }
    .cs-icon svg { width: 17px; height: 17px; }
    .cs-text { flex: 1; min-width: 0; display: grid; gap: 2px; }
    .cs-name { font-size: 13.5px; font-weight: 600; line-height: 1.3; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .cs-slug {
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 11.5px;
        color: var(--cx-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    /* O nome tem prioridade: o selo do tipo encolhe com reticências (o rótulo completo fica no title) */
    .cs-badges { display: flex; justify-content: flex-end; gap: 6px; min-width: 0; max-width: 50%; }
    .cs-badges .cx-badge { font-size: 11px; padding: 2px 8px; flex: none; }
    .cs-badges .cs-type { flex: 0 1 auto; min-width: 0; display: block; overflow: hidden; text-overflow: ellipsis; }
    .cs-badges .cx-badge svg { width: 12px; height: 12px; flex: none; }

    .cs-usage {
        grid-area: usage; display: flex; align-items: center; gap: 6px; min-width: 0;
        font-size: 12px; color: var(--cx-muted);
    }
    .cs-usage svg { width: 14px; height: 14px; flex: none; color: var(--tone-fg); }
    .cs-usage span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .cs-usage.cx-tone-primary, .cs-usage.cx-tone-warning { color: var(--tone-fg); }

    .cs-foot {
        grid-area: foot; display: flex; align-items: center; justify-content: space-between; gap: 8px;
        margin: 0 -6px; padding-top: 4px; border-top: 1px solid var(--cx-border);
    }
    .cs-icon-btn {
        display: grid; place-items: center; width: 32px; height: 32px; border-radius: 8px; color: var(--cx-muted);
    }
    .cs-icon-btn:hover { color: var(--cx-text); background: var(--cx-hover); }
    .cs-icon-btn svg { width: 18px; height: 18px; }

    /* ---------- Lista ---------- */
    .cs-grid--list .cs-card {
        grid-template-columns: auto 128px minmax(0, 1.4fr) minmax(0, 1fr) auto auto;
        grid-template-areas: "lead thumb info usage switch foot";
        align-items: center;
        gap: 14px;
        padding: 8px 10px;
    }
    .cs-grid--list .cs-thumb { --stp-thumb-h: 66px; width: 128px; border-radius: 6px; }
    .cs-grid--list .cs-thumb-frame { inset: 0 auto auto 0; width: 320px; height: 165px; transform: scale(.4); transform-origin: top left; }
    .cs-grid--list .cs-info { align-items: center; }
    .cs-grid--list .cs-badges { max-width: none; }
    .cs-grid--list .cs-foot { margin: 0; padding: 0; border: 0; }

    @media (max-width: 1100px) {
        .cs-grid--list .cs-card {
            grid-template-columns: auto 104px minmax(0, 1fr) auto auto;
            grid-template-areas: "lead thumb info switch foot" "lead thumb usage switch foot";
            row-gap: 4px;
        }
        .cs-grid--list .cs-thumb { width: 104px; --stp-thumb-h: 54px; }
        .cs-grid--list .cs-thumb-frame { transform: scale(.325); }
    }
    @media (max-width: 640px) {
        .cs-toolbar-end { margin-left: 0; width: 100%; justify-content: space-between; }
        .cs-filter { flex: 1 1 160px; }
        .cs-info { flex-wrap: wrap; }
        .cs-badges { max-width: none; justify-content: flex-start; padding-left: 42px; flex-wrap: wrap; }
        .cs-grid--list .cs-card {
            grid-template-columns: minmax(0, 1fr) auto;
            grid-template-areas: "lead switch" "info info" "usage usage" "foot foot";
            row-gap: 8px;
        }
        .cs-grid--list .cs-thumb { display: none; }
        .cs-grid--list .cs-foot { padding-top: 4px; border-top: 1px solid var(--cx-border); }
    }

    @media (prefers-reduced-motion: reduce) {
        .cs-card, .cs-switch, .cs-switch-knob, .cs-results, .cs-seg-btn { transition: none; }
        .cs-results.is-loading::before { animation: none; }
    }
</style>
@endonce
