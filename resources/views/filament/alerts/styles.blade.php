{{-- Alertas e notificações (CSS puro). Prefixo al-; tokens e primitivas vêm de filament/admin/ui --}}
@once
<style>
    /* ================= Editor (criar / editar) ================= */

    /* Prévia e status acompanham a rolagem do formulário no desktop */
    @media (min-width: 1280px) {
        .al-aside { position: sticky; top: calc(4rem + 1.5rem); }
    }
    .al-aside { display: grid; gap: 24px; align-content: start; }

    .al-panel {
        min-width: 0;
        background: var(--cx-surface);
        border: 1px solid var(--cx-border);
        border-radius: 12px;
        box-shadow: var(--cx-shadow);
    }
    .al-panel-head { display: flex; align-items: flex-start; gap: 12px; padding: 16px 20px; }
    .al-panel-icon { display: grid; place-items: center; flex: none; width: 24px; height: 24px; color: var(--cx-subtle); }
    .al-panel-icon svg { width: 22px; height: 22px; }
    .al-panel-text { flex: 1; min-width: 0; }
    .al-panel-title { margin: 0; font-size: 15px; font-weight: 600; line-height: 1.5; }
    .al-panel-desc { margin: 1px 0 0; font-size: 13px; color: var(--cx-muted); }

    .al-seg { display: inline-flex; flex: none; padding: 3px; gap: 2px; border-radius: 10px; background: var(--cx-track); }
    .al-seg-btn {
        display: inline-flex; align-items: center; gap: 6px;
        height: 30px; padding: 0 11px; border-radius: 7px;
        font-size: 13px; font-weight: 500; color: var(--cx-muted); white-space: nowrap;
        transition: color .15s ease, background-color .15s ease;
    }
    .al-seg-btn svg { width: 16px; height: 16px; flex: none; }
    .al-seg-btn:hover { color: var(--cx-text); }
    .al-seg-btn.is-active { color: #fff; background: rgb(var(--primary-600)); box-shadow: 0 1px 3px rgba(15, 23, 42, .18); }

    /* Palco: o site em escala (1440×900 no desktop, 390×844 no celular) */
    .al-stage {
        position: relative;
        display: flex; justify-content: center; align-items: flex-start;
        margin: 0 16px; padding: 16px;
        border-radius: 10px;
        background: var(--cx-surface-2);
        border: 1px solid var(--cx-border);
        overflow: hidden;
        transition: height .2s ease;
    }
    .al-device {
        position: relative; flex: none; overflow: hidden;
        border-radius: 6px;
        background: #fff;
        box-shadow: 0 0 0 1px var(--cx-border-strong), 0 16px 32px -20px rgba(0, 0, 0, .5);
    }
    .al-stage.is-mobile .al-device { border-radius: 18px; box-shadow: 0 0 0 4px rgba(var(--gray-950), .85), 0 16px 32px -20px rgba(0, 0, 0, .5); }
    .dark .al-stage.is-mobile .al-device { box-shadow: 0 0 0 4px rgba(255, 255, 255, .14), 0 16px 32px -20px rgba(0, 0, 0, .6); }
    .al-device-inner { position: absolute; top: 0; left: 0; transform-origin: top left; }
    .al-device-inner iframe { display: block; width: 100%; height: 100%; border: 0; background: #fff; }
    .al-stage-loading {
        position: absolute; inset: 0;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        font-size: 13px; color: var(--cx-muted);
        background: var(--cx-surface-2);
    }

    .al-preview-foot {
        display: flex; flex-wrap: wrap; align-items: center; gap: 6px 12px;
        padding: 10px 20px 14px;
        font-size: 12.5px; color: var(--cx-muted);
    }
    .al-preview-status { display: inline-flex; align-items: center; gap: 6px; }
    .al-dot { width: 7px; height: 7px; border-radius: 999px; background: rgb(var(--success-500)); }
    .al-preview-status[data-status="loading"] .al-dot { background: rgb(var(--warning-500)); }
    .al-preview-status[data-status="error"] .al-dot { background: rgb(var(--danger-500)); }
    .al-preview-hint { color: var(--cx-text); }
    .al-text-btn {
        display: inline-flex; align-items: center; gap: 4px; margin-left: auto;
        font-size: 12.5px; font-weight: 500; color: rgb(var(--primary-600));
    }
    .dark .al-text-btn { color: rgb(var(--primary-400)); }
    .al-text-btn:hover { text-decoration: underline; }
    .al-text-btn:disabled { opacity: .5; cursor: default; text-decoration: none; }

    .al-icon-btn {
        display: grid; place-items: center; flex: none; width: 36px; height: 36px; border-radius: 8px;
        color: var(--cx-muted);
    }
    .al-icon-btn:hover { color: var(--cx-text); background: var(--cx-hover); }
    .al-icon-btn svg { width: 18px; height: 18px; }

    /* Prévia expandida: ocupa a tela (Esc recolhe) */
    .al-preview.is-expanded {
        position: fixed; inset: 16px; z-index: 40;
        display: flex; flex-direction: column;
        box-shadow: 0 0 0 100vmax rgba(0, 0, 0, .55), 0 24px 64px -16px rgba(0, 0, 0, .6);
    }
    .al-preview.is-expanded .al-stage { flex: 1; align-items: center; }
    html.al-noscroll { overflow: hidden; }
    /* Coluna sticky cria contexto de empilhamento: solta enquanto a prévia está expandida */
    html.al-noscroll .al-aside { position: static; }

    /* Coluna estreita: segmentos só com ícone */
    .al-preview { container: alp / inline-size; }
    @container alp (max-width: 520px) {
        .al-seg-label { display: none; }
        .al-seg-btn { width: 34px; padding: 0; justify-content: center; }
    }

    /* Status e informações */
    .al-status .al-panel-head { align-items: center; }
    .al-facts { display: grid; margin: 0; padding: 0 20px 16px; gap: 8px; }
    .al-facts > div {
        display: flex; justify-content: space-between; gap: 12px;
        padding-top: 8px; border-top: 1px solid var(--cx-border);
        font-size: 13px;
    }
    .al-facts dt { display: inline-flex; align-items: center; gap: 6px; color: var(--cx-muted); }
    .al-facts dd { margin: 0; text-align: right; font-weight: 500; }

    /* Barra de ações inferior, sempre visível (Cancelar à esquerda) */
    .fi-resource-alerts .fi-form-actions {
        position: sticky; bottom: 12px; z-index: 25;
        padding: 12px 16px;
        border-radius: 12px;
        background: rgba(255, 255, 255, .94);
        box-shadow: 0 0 0 1px rgba(var(--gray-950), .08), 0 14px 32px -16px rgba(15, 23, 42, .35);
        backdrop-filter: blur(8px);
    }
    .dark .fi-resource-alerts .fi-form-actions {
        background: rgba(var(--gray-900), .94);
        box-shadow: 0 0 0 1px rgba(255, 255, 255, .08), 0 14px 32px -16px rgba(0, 0, 0, .7);
    }
    .fi-resource-alerts .fi-form-actions .al-cancel { margin-inline-end: auto; }
    @media (max-width: 639px) {
        .fi-resource-alerts .fi-form-actions { bottom: 0; margin-inline: -16px; border-radius: 0; }
        .fi-resource-alerts .fi-form-actions .fi-ac > * { flex: 1 1 auto; }
        .al-seg-label { display: none; }
        .al-seg-btn { width: 36px; padding: 0; justify-content: center; }
        .al-panel-head { padding: 14px 16px; }
        .al-stage { margin: 0 12px; padding: 10px; }
    }

    /* Pré-visualização na listagem (modal) */
    .al-modal-stage {
        position: relative; overflow: hidden;
        border-radius: 10px; background: #fff;
        box-shadow: 0 0 0 1px var(--cx-border-strong);
    }
    .al-modal-stage .al-device-inner { width: 1280px; height: 800px; }

    /* ================= Listagem ================= */

    /* Raiz usa também .cs: indicadores clicáveis, barra de filtros e faixas vêm de content-sections/styles */
    .al .cs-search { flex: 1 1 220px; }
    .al .cs-filter { flex: 0 1 190px; min-width: 150px; }
    .al .cs-toolbar-end { flex-wrap: nowrap; }
    .al-sort { flex: 0 1 200px; min-width: 170px; }

    .al-grid {
        display: grid; gap: 16px;
        grid-template-columns: repeat(auto-fill, minmax(max(min(100%, 300px), calc((100% - 32px) / 3)), 1fr));
    }
    .al-grid--list { grid-template-columns: minmax(0, 1fr); gap: 8px; }

    .al-card {
        display: grid;
        grid-template-areas: "thumb" "head" "badges" "desc" "meta" "foot";
        gap: 10px;
        min-width: 0;
        padding: 10px 12px 8px;
        border-radius: 12px;
        background: var(--cx-surface);
        border: 1px solid var(--cx-border);
        box-shadow: var(--cx-shadow);
        transition: border-color .15s ease;
    }
    .al-card:hover { border-color: var(--cx-border-strong); }
    .al-card.is-off .al-thumb { opacity: .62; }

    .al-head { grid-area: head; display: flex; align-items: center; gap: 10px; min-width: 0; }
    .al-name { flex: 1; min-width: 0; font-size: 14px; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .al-state { flex: none; font-size: 11.5px; padding: 2px 8px; }
    .al-state svg, .al-badges svg { width: 13px; height: 13px; }
    .al-badges { grid-area: badges; display: flex; flex-wrap: wrap; gap: 6px; }
    .al-badges .cx-badge { font-size: 11.5px; padding: 2px 8px; }
    .al-desc {
        grid-area: desc; margin: 0; font-size: 13px; line-height: 1.45; color: var(--cx-muted);
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        overflow-wrap: anywhere;
    }
    .al-meta {
        grid-area: meta; display: flex; flex-wrap: wrap; justify-content: space-between; gap: 4px 12px;
        font-size: 12px; color: var(--cx-muted);
    }
    .al-meta span { display: inline-flex; align-items: center; gap: 5px; white-space: nowrap; }
    .al-meta svg { width: 14px; height: 14px; color: var(--cx-subtle); }

    .al-foot { grid-area: foot; display: flex; align-items: center; gap: 8px; padding-top: 8px; border-top: 1px solid var(--cx-border); }
    .al-edit {
        flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        height: 34px; border-radius: 8px;
        font-size: 13px; font-weight: 500; color: var(--cx-text);
        background: var(--cx-surface-2); box-shadow: inset 0 0 0 1px var(--cx-border-strong);
        transition: background-color .15s ease;
    }
    .al-edit:hover { background: var(--cx-hover); }
    .al-edit.is-disabled { color: var(--cx-muted); box-shadow: none; }
    .al-more {
        display: grid; place-items: center; width: 34px; height: 34px; border-radius: 8px;
        color: var(--cx-muted); box-shadow: inset 0 0 0 1px var(--cx-border-strong);
    }
    .al-more:hover { color: var(--cx-text); background: var(--cx-hover); }
    .al-more svg { width: 18px; height: 18px; }

    /* Miniatura: página esquematizada + o alerta na posição real */
    .al-thumb {
        grid-area: thumb; position: relative; display: block; width: 100%; height: 132px;
        overflow: hidden; border-radius: 8px; text-align: left;
        background: var(--cx-surface-2); box-shadow: inset 0 0 0 1px var(--cx-border);
        cursor: zoom-in; transition: opacity .15s ease;
    }
    .al-thumb-page { position: absolute; inset: 0; display: grid; align-content: start; gap: 7px; padding: 10px 12px; }
    .al-thumb-nav { height: 8px; margin-bottom: 8px; border-radius: 3px; background: var(--cx-track); }
    .al-thumb-line { width: var(--w); height: 8px; border-radius: 3px; background: var(--cx-track); }
    .al-thumb-line.is-thin { height: 5px; }
    .al-thumb.is-modal::after { content: ""; position: absolute; inset: 0; background: rgba(15, 23, 42, .38); }

    .al-mini {
        position: absolute; z-index: 1;
        display: flex; align-items: flex-start; gap: 7px;
        padding: 8px 10px; border-radius: 8px;
        color: var(--cx-text); background: var(--cx-surface);
        box-shadow: 0 0 0 1px var(--cx-border-strong), 0 8px 18px -10px rgba(0, 0, 0, .45);
    }
    .al-thumb.is-modal .al-mini { left: 50%; top: 50%; width: 72%; transform: translate(-50%, -50%); padding: 10px 12px; }
    .al-thumb.is-top .al-mini, .al-thumb.is-bottom .al-mini { left: 0; right: 0; border-radius: 0; align-items: center; }
    .al-thumb.is-top .al-mini { top: 0; }
    .al-thumb.is-bottom .al-mini { bottom: 0; }
    .al-thumb.is-top-left .al-mini, .al-thumb.is-top-right .al-mini,
    .al-thumb.is-bottom-left .al-mini, .al-thumb.is-bottom-right .al-mini { width: 62%; }
    .al-thumb.is-top-left .al-mini { top: 8px; left: 8px; }
    .al-thumb.is-top-right .al-mini { top: 8px; right: 8px; }
    .al-thumb.is-bottom-left .al-mini { bottom: 8px; left: 8px; }
    .al-thumb.is-bottom-right .al-mini { bottom: 8px; right: 8px; }

    .al-mini-icon {
        display: grid; place-items: center; flex: none; width: 18px; height: 18px; border-radius: 999px;
        color: #fff; background: rgb(var(--sa-accent));
    }
    .al-mini-icon svg { width: 11px; height: 11px; }
    .al-mini-text { display: grid; gap: 2px; min-width: 0; flex: 1; }
    .al-mini-title { font-size: 11px; font-weight: 600; line-height: 1.25; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .al-mini-msg {
        font-size: 9.5px; line-height: 1.3; opacity: .8;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .al-thumb.is-top .al-mini-msg, .al-thumb.is-bottom .al-mini-msg { -webkit-line-clamp: 1; }
    .al-mini-cta { font-size: 9.5px; font-weight: 600; }
    .al-mini-close { width: 11px; height: 11px; flex: none; opacity: .55; }

    /* Miniaturas com as mesmas cores do site (variáveis --sa-* de App\Support\AlertAppearance) */
    .al-thumb.is-modal .al-mini { color: rgb(var(--sa-ink)); background: rgb(var(--sa-soft)); box-shadow: 0 0 0 1px rgb(var(--sa-accent) / .35), 0 8px 18px -8px rgb(var(--sa-accent) / .55); }
    .al-thumb.is-modal .al-mini-cta { color: rgb(var(--sa-button)); }
    .al-thumb.is-modal .al-mini-icon { width: 22px; height: 22px; }
    .al-thumb.is-modal .al-mini-icon svg { width: 13px; height: 13px; }
    .al-thumb.is-toast .al-mini { color: #f8fafc; background: rgb(15 23 42); box-shadow: 0 0 0 1px rgb(var(--sa-accent) / .65), 0 8px 18px -8px rgb(var(--sa-accent) / .6); }
    .al-thumb.is-toast .al-mini-msg { color: #cbd5e1; opacity: 1; }
    .al-thumb.is-toast .al-mini-cta { color: color-mix(in srgb, rgb(var(--sa-accent)) 55%, #fff); }
    .al-thumb.is-banner .al-mini { color: #fff; background: rgb(var(--sa-banner)); box-shadow: 0 6px 14px -8px rgb(0 0 0 / .5); }
    .al-thumb.is-banner .al-mini-icon { background: rgb(255 255 255 / .2); }

    /* Lista: uma linha por alerta */
    .al-grid--list .al-card {
        grid-template-columns: 148px minmax(0, 1.3fr) minmax(0, 1fr) auto;
        grid-template-areas: "thumb head meta foot" "thumb badges meta foot" "thumb desc meta foot";
        align-items: center; gap: 4px 16px; padding: 8px 10px;
    }
    .al-grid--list .al-thumb { height: 84px; }
    .al-grid--list .al-meta { flex-direction: column; align-items: flex-start; }
    .al-grid--list .al-desc { -webkit-line-clamp: 1; }
    .al-grid--list .al-foot { padding: 0; border: 0; }
    .al-grid--list .al-edit { padding: 0 14px; }

    @media (max-width: 900px) {
        .al-grid--list .al-card {
            grid-template-columns: 112px minmax(0, 1fr);
            grid-template-areas: "thumb head" "thumb badges" "thumb desc" "meta meta" "foot foot";
        }
        .al-grid--list .al-thumb { height: 72px; }
        .al-grid--list .al-meta { flex-direction: row; }
        .al-grid--list .al-foot { padding-top: 8px; border-top: 1px solid var(--cx-border); }
    }
    @media (max-width: 640px) {
        .al-sort, .al .cs-filter { flex: 1 1 160px; }
    }

    @media (prefers-reduced-motion: reduce) {
        .al-stage, .al-seg-btn, .al-card, .al-thumb, .al-edit { transition: none; }
    }
</style>
@endonce
