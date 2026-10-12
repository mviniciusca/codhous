{{-- Biblioteca de componentes (CSS puro). Prefixo stp-; tokens vêm de filament/admin/ui. Primitivas do esqueleto: sk- --}}
@once
<style>
    /* .stp-tokens: miniaturas fora da biblioteca (ex.: modo Estrutura da prévia) */
    .stp, .stp-tokens {
        --stp-accent: var(--primary-500);
        --stp-thumb-h: 132px;
        --stp-sk: rgba(var(--gray-500), .16);
        --stp-sk-soft: rgba(var(--gray-500), .08);
        --stp-sk-strong: rgba(var(--gray-500), .28);
        --stp-thumb-bg: rgb(var(--gray-50));
        --stp-thumb-border: rgba(var(--gray-950), .06);
    }
    .stp { display: flex; flex-direction: column; }
    .dark .stp, .dark .stp-tokens {
        --stp-accent: var(--primary-400);
        --stp-sk: rgba(255, 255, 255, .09);
        --stp-sk-soft: rgba(255, 255, 255, .04);
        --stp-sk-strong: rgba(255, 255, 255, .16);
        --stp-thumb-bg: rgba(255, 255, 255, .025);
        --stp-thumb-border: rgba(255, 255, 255, .06);
    }

    /* ---------- Cabeçalho (fixo ao rolar a coluna) ---------- */
    .stp-head {
        position: sticky;
        top: 0;
        z-index: 3;
        display: grid;
        gap: 10px;
        padding: 16px 14px 12px;
        background: var(--cx-surface);
        border-bottom: 1px solid var(--cx-border);
    }
    .stp-title { font-size: 15px; font-weight: 600; color: var(--cx-text); }
    .stp-desc { margin-top: -6px; font-size: 12.5px; line-height: 1.45; color: var(--cx-muted); }

    .stp-search {
        display: flex;
        align-items: center;
        gap: 8px;
        height: 36px;
        padding: 0 11px;
        border-radius: 10px;
        border: 1px solid var(--cx-border-strong);
        background: var(--cx-surface);
        color: var(--cx-subtle);
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .stp-search:focus-within { border-color: rgb(var(--stp-accent)); box-shadow: 0 0 0 3px rgba(var(--primary-500), .18); }
    .stp-search svg { width: 16px; height: 16px; flex: none; }
    .stp-search input {
        flex: 1;
        min-width: 0;
        padding: 0;
        border: 0;
        background: transparent;
        font-size: 13px;
        color: var(--cx-text);
        box-shadow: none !important;
        outline: none;
    }
    .stp-search input::placeholder { color: var(--cx-subtle); }

    .stp-cats { display: flex; flex-wrap: wrap; gap: 5px; }
    .stp-cat {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        height: 26px;
        padding: 0 4px 0 9px;
        border-radius: 999px;
        border: 1px solid var(--cx-border);
        font-size: 12px;
        font-weight: 500;
        white-space: nowrap;
        color: var(--cx-muted);
        transition: background-color .15s ease, color .15s ease, border-color .15s ease;
    }
    .stp-cat svg { width: 13px; height: 13px; flex: none; margin-left: -2px; }
    .stp-cat:hover { color: var(--cx-text); background: var(--cx-hover); }
    .stp-cat:focus-visible { outline: 2px solid rgb(var(--stp-accent)); outline-offset: 1px; }
    .stp-cat.is-empty { opacity: .45; }
    .stp-cat.is-active { color: #fff; border-color: transparent; background: rgb(var(--primary-600)); }
    .stp-cat-count {
        min-width: 18px;
        padding: 0 5px;
        border-radius: 999px;
        font-size: 10.5px;
        font-weight: 600;
        line-height: 18px;
        text-align: center;
        background: var(--cx-track);
    }
    .stp-cat.is-active .stp-cat-count { background: rgba(255, 255, 255, .22); color: #fff; }

    /* ---------- Lista ---------- */
    .stp-list { display: grid; gap: 10px; padding: 12px; }

    .stp-card {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 8px 8px 12px;
        border-radius: 12px;
        border: 1px solid var(--cx-border);
        background: var(--cx-surface);
        cursor: pointer;
        user-select: none;
        transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
    }
    .stp-card:hover { border-color: var(--cx-border-strong); background: var(--cx-hover); }
    .stp-card:focus-visible { outline: none; border-color: rgba(var(--primary-500), .7); box-shadow: 0 0 0 3px rgba(var(--primary-500), .25); }
    .stp-card.is-selected {
        border-color: rgb(var(--stp-accent));
        background: rgba(var(--primary-500), .05);
        box-shadow: 0 0 0 1px rgb(var(--stp-accent)), 0 10px 26px -16px rgba(var(--primary-600), .8);
    }
    .stp-card.is-locked { cursor: default; }

    .stp-check {
        position: absolute;
        top: 14px;
        right: 14px;
        z-index: 2;
        display: grid;
        place-items: center;
        width: 22px;
        height: 22px;
        border-radius: 999px;
        color: #fff;
        background: rgb(var(--primary-600));
        box-shadow: 0 0 0 3px var(--cx-surface);
        opacity: 0;
        transform: scale(.6);
        transition: opacity .15s ease, transform .15s ease;
    }
    .stp-check svg { width: 13px; height: 13px; }
    .stp-card.is-selected .stp-check { opacity: 1; transform: scale(1); }

    .stp-meta { display: flex; align-items: flex-start; gap: 10px; padding-inline: 4px; }
    .stp-meta-icon {
        display: grid;
        place-items: center;
        flex: none;
        width: 32px;
        height: 32px;
        border-radius: 9px;
        border: 1px solid var(--cx-border);
        color: var(--cx-muted);
        background: var(--cx-surface-2);
        transition: color .15s ease, background-color .15s ease, border-color .15s ease;
    }
    .stp-meta-icon svg { width: 17px; height: 17px; }
    .stp-card:hover .stp-meta-icon { color: var(--cx-text); }
    .stp-card.is-selected .stp-meta-icon { color: rgb(var(--stp-accent)); border-color: rgba(var(--primary-500), .3); background: rgba(var(--primary-500), .1); }
    .stp-meta-text { display: grid; gap: 1px; min-width: 0; }
    .stp-meta-title { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; font-size: 13.5px; font-weight: 600; line-height: 1.35; color: var(--cx-text); }
    .stp-meta-desc { font-size: 12px; line-height: 1.45; color: var(--cx-muted); }
    .stp-badge {
        padding: 0 6px;
        border-radius: 999px;
        font-size: 10.5px;
        font-weight: 600;
        line-height: 17px;
        color: var(--cx-muted);
        background: var(--cx-track);
    }

    /* ---------- Miniatura ---------- */
    .stp-thumb {
        position: relative;
        height: var(--stp-thumb-h);
        overflow: hidden;
        border-radius: 8px;
        border: 1px solid var(--stp-thumb-border);
        background: var(--stp-thumb-bg);
        font-size: 8px;
        line-height: 1.3;
        pointer-events: none;
    }
    .stp-card:hover .stp-thumb { --stp-sk: rgba(var(--gray-500), .21); }
    .dark .stp-card:hover .stp-thumb { --stp-sk: rgba(255, 255, 255, .12); }
    .stp-pv { position: absolute; inset: 0; padding: 12px; display: flex; flex-direction: column; gap: 6px; }

    /* ---------- Primitivas do esqueleto ---------- */
    .sk { display: block; flex: none; border-radius: 3px; background: var(--stp-sk); transition: background-color .18s ease; }
    .sk-line { height: 5px; width: var(--w, 100%); }
    .sk-title { height: 8px; width: var(--w, 60%); background: var(--stp-sk-strong); }
    .sk-btn { height: 12px; width: var(--w, 40px); border-radius: 3px; }
    .sk-btn.is-accent { background: rgba(var(--primary-500), .55); }
    .sk-input { height: 13px; width: var(--w, 100%); border-radius: 3px; background: transparent; border: 1px solid var(--stp-sk-strong); }
    .sk-dot { width: var(--s, 8px); height: var(--s, 8px); border-radius: 999px; }
    .sk-box { border-radius: 5px; background: var(--stp-sk-soft); border: 1px solid var(--stp-sk); }
    .sk-img { display: grid; place-items: center; border-radius: 5px; background: var(--stp-sk); color: var(--stp-sk-strong); }
    .sk-img svg { width: 16px; height: 16px; }
    .sk-row { display: flex; align-items: center; gap: var(--g, 6px); }
    .sk-col { display: flex; flex-direction: column; gap: var(--g, 4px); min-width: 0; }
    .sk-grow { flex: 1; min-width: 0; }
    .sk-center { align-items: center; text-align: center; }

    /* ---------- Vazio ---------- */
    .stp-empty {
        display: grid;
        justify-items: center;
        gap: 4px;
        padding: 36px 12px;
        border-radius: 12px;
        border: 1px dashed var(--cx-border-strong);
        text-align: center;
    }
    .stp-empty > svg { width: 24px; height: 24px; margin-bottom: 4px; color: var(--cx-subtle); }
    .stp-empty-title { font-size: 13.5px; font-weight: 600; color: var(--cx-text); }
    .stp-empty-desc { font-size: 12.5px; color: var(--cx-muted); }
    .stp-empty-btn { margin-top: 8px; padding: 5px 11px; border-radius: 8px; font-size: 12.5px; font-weight: 500; color: rgb(var(--stp-accent)); background: rgba(var(--primary-500), .1); }
    .stp-empty-btn:hover { background: rgba(var(--primary-500), .16); }

    @media (prefers-reduced-motion: reduce) {
        .stp-card, .stp-check, .stp-cat, .sk { transition: none; }
    }
</style>
@endonce
