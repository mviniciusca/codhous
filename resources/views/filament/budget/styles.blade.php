{{-- Estilos da tela de orçamento (CSS puro, independente do build do Tailwind) --}}
<style>
    .bq-pipeline {
        --bq-border: rgba(15, 23, 42, .08);
        --bq-surface: #fff;
        --bq-muted: rgb(var(--gray-500));
        --bq-text: rgb(var(--gray-950));
        --bq-accent: rgb(var(--primary-600));
        --bq-accent-soft: rgba(var(--primary-500), .1);
        --bq-success: rgb(var(--success-600));
        --bq-success-soft: rgba(var(--success-500), .12);

        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(220px, 320px);
        gap: 12px 24px;
        align-items: center;
        padding: 16px 20px;
        background: var(--bq-surface);
        border: 1px solid var(--bq-border);
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
    }
    .dark .bq-pipeline {
        --bq-border: rgba(255, 255, 255, .1);
        --bq-surface: rgb(var(--gray-900));
        --bq-muted: rgb(var(--gray-400));
        --bq-text: #fff;
        --bq-accent: rgb(var(--primary-400));
        --bq-success: rgb(var(--success-400));
    }

    .bq-steps { display: flex; align-items: center; gap: 8px; min-width: 0; }
    .bq-step {
        display: flex; align-items: center; gap: 10px;
        padding: 6px 10px 6px 6px;
        border-radius: 999px;
        background: transparent;
        border: 1px solid transparent;
        color: var(--bq-muted);
        text-align: left;
        cursor: pointer;
        transition: background .15s, border-color .15s;
        white-space: nowrap;
    }
    .bq-step:hover:not(:disabled) { background: var(--bq-accent-soft); }
    .bq-step:disabled { cursor: default; }
    .bq-step-dot {
        display: inline-flex; align-items: center; justify-content: center;
        width: 28px; height: 28px; flex-shrink: 0;
        border-radius: 999px;
        font-size: 12px; font-weight: 700;
        border: 2px solid var(--bq-border);
        color: var(--bq-muted);
    }
    .bq-step-text { display: flex; flex-direction: column; line-height: 1.2; }
    .bq-step-text strong { font-size: 13px; font-weight: 600; }
    .bq-step-text small { font-size: 11px; color: var(--bq-muted); }

    .bq-step--done { color: var(--bq-text); }
    .bq-step--done .bq-step-dot { background: var(--bq-success); border-color: var(--bq-success); color: #fff; }
    .bq-step--current { color: var(--bq-text); background: var(--bq-accent-soft); border-color: rgba(var(--primary-500), .25); }
    .bq-step--current .bq-step-dot { background: var(--bq-accent); border-color: var(--bq-accent); color: #fff; }

    .bq-step-line { flex: 1 1 24px; min-width: 16px; height: 2px; background: var(--bq-border); border-radius: 2px; }
    .bq-step-line--done { background: var(--bq-success); }

    .bq-next { border-left: 1px solid var(--bq-border); padding-left: 20px; }
    .bq-next-label { display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: var(--bq-muted); }
    .bq-next p { margin: 2px 0 0; font-size: 13px; color: var(--bq-text); }

    .bq-chips { grid-column: 1 / -1; display: flex; flex-wrap: wrap; align-items: center; gap: 8px; padding-top: 12px; border-top: 1px solid var(--bq-border); }
    .bq-chip {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 12px; font-weight: 500;
        color: var(--bq-muted);
        background: rgba(var(--gray-500), .08);
    }
    .bq-chip--ok { color: var(--bq-success); background: var(--bq-success-soft); }
    .bq-chip--warn { color: rgb(var(--warning-700)); background: rgba(var(--warning-500), .14); }
    .dark .bq-chip--warn { color: rgb(var(--warning-400)); }
    .bq-chip svg { width: 14px; height: 14px; }
    .bq-chips-spacer { flex: 1; }
    .bq-link-button {
        font-size: 12px; font-weight: 600;
        color: var(--bq-muted);
        background: none; border: 0; cursor: pointer;
        padding: 3px 6px; border-radius: 6px;
    }
    .bq-link-button:hover { color: var(--bq-text); background: rgba(var(--gray-500), .08); }

    .bq-total {
        display: flex; align-items: baseline; justify-content: space-between; gap: 12px;
        padding: 12px 14px;
        border-radius: 10px;
        background: rgba(var(--primary-500), .08);
    }
    .bq-total span { font-size: 13px; font-weight: 600; color: rgb(var(--gray-600)); }
    .bq-total strong { font-size: 24px; font-weight: 700; color: rgb(var(--primary-700)); font-variant-numeric: tabular-nums; }
    .dark .bq-total span { color: rgb(var(--gray-300)); }
    .dark .bq-total strong { color: rgb(var(--primary-300)); }

    .bq-money { font-weight: 600; font-variant-numeric: tabular-nums; }
    .bq-hint { font-size: 12px; color: rgb(var(--gray-500)); }

    @media (min-width: 1024px) {
        /* Coluna lateral acompanha a rolagem, como em um ERP */
        .bq-sticky { position: sticky; top: 5rem; align-self: start; }
    }

    @media (max-width: 900px) {
        .bq-pipeline { grid-template-columns: 1fr; }
        .bq-next { border-left: 0; padding-left: 0; }
        .bq-steps { overflow-x: auto; }
        .bq-step-text small { display: none; }
    }
</style>
