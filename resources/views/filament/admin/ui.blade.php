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
