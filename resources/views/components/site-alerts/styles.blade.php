{{--
    CSS dos alertas do site (prefixo sa-). As cores vêm das variáveis --sa-* que cada alerta
    recebe de App\Support\AlertAppearance; o mesmo CSS atende os seis estilos.
--}}
@once
<style>
    .sa-root { position: fixed; inset: 0; z-index: 100; pointer-events: none; }
    .sa { position: absolute; pointer-events: auto; font-family: inherit; -webkit-font-smoothing: antialiased; }
    .sa p { margin: 0; }
    .sa svg { display: block; }

    /* ---------- Peças comuns ---------- */
    .sa-icon {
        display: grid; place-items: center; flex: none;
        width: 48px; height: 48px; border-radius: 999px;
        color: #fff; background: rgb(var(--sa-accent));
        box-shadow: 0 0 0 6px rgb(var(--sa-accent) / .14);
    }
    .sa-icon svg { width: 24px; height: 24px; }
    .sa-body { flex: 1; min-width: 0; }
    .sa-title { font-size: 17px; font-weight: 700; line-height: 1.3; letter-spacing: -.01em; }
    .sa-msg { margin-top: 4px !important; font-size: 14px; line-height: 1.55; white-space: pre-line; overflow-wrap: anywhere; }
    .sa-title + .sa-msg { margin-top: 4px !important; }

    .sa-x {
        position: absolute; top: 12px; right: 12px;
        display: grid; place-items: center; width: 32px; height: 32px; border-radius: 8px;
        color: inherit; background: transparent; border: 0; opacity: .55; cursor: pointer;
        transition: opacity .15s ease, background-color .15s ease;
    }
    .sa-x svg { width: 18px; height: 18px; }
    .sa-x:hover { opacity: 1; background: rgb(0 0 0 / .06); }

    .sa-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        min-height: 40px; padding: 0 18px; border: 0; border-radius: 10px;
        font: inherit; font-size: 14px; font-weight: 600; line-height: 1.2; text-decoration: none;
        cursor: pointer; transition: filter .15s ease, transform .15s ease, background-color .15s ease;
    }
    .sa-btn svg { width: 16px; height: 16px; }
    .sa-btn:hover { filter: brightness(1.08); }
    .sa-btn:active { transform: translateY(1px); }
    .sa-btn--primary { color: #fff; background: rgb(var(--sa-button)); box-shadow: 0 6px 16px -8px rgb(var(--sa-button) / .8); }
    .sa-btn--dark { color: #fff; background: #1e293b; }
    .sa-btn--dark:hover { filter: none; background: #0f172a; }
    .sa-btn--light { color: rgb(var(--sa-banner)); background: #fff; }

    .sa a:focus-visible, .sa button:focus-visible { outline: 2px solid rgb(var(--sa-accent)); outline-offset: 2px; }

    /* ---------- Modal / Popup ---------- */
    .sa--modal { position: fixed; inset: 0; z-index: 110; display: flex; align-items: center; justify-content: center; padding: 16px; }
    .sa-overlay { position: absolute; inset: 0; background: rgb(2 6 23 / .62); backdrop-filter: blur(3px); }
    .sa-modal {
        position: relative; display: flex; gap: 18px; width: 100%; max-width: 480px;
        padding: 26px 26px 24px; border-radius: 18px;
        color: rgb(var(--sa-ink)); background: rgb(var(--sa-soft));
        border: 1px solid rgb(var(--sa-accent) / .28);
        box-shadow: 0 30px 70px -24px rgb(2 6 23 / .6), 0 0 70px -18px rgb(var(--sa-accent) / .55);
    }
    .sa-modal .sa-title { padding-right: 24px; }
    .sa-modal .sa-msg { opacity: .82; }
    .sa-inline-link { display: inline-block; margin-top: 6px; font-size: 14px; font-weight: 600; color: rgb(var(--sa-button)); text-decoration: underline; text-underline-offset: 3px; }
    .sa-actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 20px; }
    .sa-actions .sa-btn { flex: 1 1 0; min-width: 120px; }

    /* ---------- Toast (canto) ---------- */
    .sa--toast { width: min(390px, calc(100vw - 32px)); }
    .sa--top-left { top: 20px; left: 20px; }
    .sa--top-right { top: 20px; right: 20px; }
    .sa--bottom-left { bottom: 20px; left: 20px; }
    .sa--bottom-right { bottom: 20px; right: 20px; }
    .sa-toast {
        position: relative; display: flex; align-items: flex-start; gap: 12px;
        padding: 14px 44px 16px 14px; border-radius: 14px; overflow: hidden;
        color: #f8fafc; background: rgb(15 23 42 / .97);
        border: 1px solid rgb(var(--sa-accent) / .6);
        box-shadow: 0 22px 44px -20px rgb(2 6 23 / .75), 0 0 34px -14px rgb(var(--sa-accent) / .65);
    }
    .sa-toast .sa-icon { width: 38px; height: 38px; box-shadow: 0 0 0 4px rgb(var(--sa-accent) / .2); }
    .sa-toast .sa-icon svg { width: 19px; height: 19px; }
    .sa-toast .sa-title { font-size: 14.5px; font-weight: 600; }
    .sa-toast .sa-msg { margin-top: 2px !important; font-size: 13px; line-height: 1.5; color: #cbd5e1; }
    .sa-toast .sa-x { top: 8px; right: 8px; color: #cbd5e1; }
    .sa-toast .sa-x:hover { background: rgb(255 255 255 / .08); }
    .sa-toast-link {
        display: inline-flex; align-items: center; gap: 4px; margin-top: 8px;
        font-size: 13px; font-weight: 600; text-decoration: none;
        color: color-mix(in srgb, rgb(var(--sa-accent)) 55%, #fff);
    }
    .sa-toast-link svg { width: 14px; height: 14px; transition: transform .15s ease; }
    .sa-toast-link:hover svg { transform: translateX(2px); }
    .sa-line { position: absolute; left: 0; right: 0; bottom: 0; height: 3px; background: linear-gradient(90deg, rgb(var(--sa-accent)), rgb(var(--sa-accent) / .1)); }

    /* ---------- Banner fixo ---------- */
    .sa--banner { left: 0; right: 0; }
    .sa--top { top: 0; }
    .sa--bottom { bottom: 0; }
    .sa-banner { color: #fff; background: rgb(var(--sa-banner)); box-shadow: 0 10px 28px -14px rgb(2 6 23 / .6); }
    .sa--bottom .sa-banner { box-shadow: 0 -10px 28px -14px rgb(2 6 23 / .6); }
    .sa-banner-in { display: flex; align-items: center; gap: 14px; max-width: 80rem; min-height: 54px; margin: 0 auto; padding: 9px 16px 9px 20px; }
    .sa-banner .sa-icon { width: 32px; height: 32px; background: rgb(255 255 255 / .18); box-shadow: none; }
    .sa-banner .sa-icon svg { width: 18px; height: 18px; }
    .sa-banner-text { flex: 1; min-width: 0; display: flex; align-items: center; gap: 12px; font-size: 14px; line-height: 1.4; }
    .sa-banner-text strong { flex: none; font-weight: 700; }
    .sa-banner-text span { min-width: 0; color: rgb(255 255 255 / .88); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .sa-banner-text.has-title span { padding-left: 12px; border-left: 1px solid rgb(255 255 255 / .35); }
    .sa-banner .sa-btn { flex: none; min-height: 34px; padding: 0 14px; font-size: 13px; }
    .sa-banner .sa-x { position: static; color: #fff; opacity: .8; }
    .sa-banner .sa-x:hover { background: rgb(255 255 255 / .14); }

    /* ---------- Celular ---------- */
    @media (max-width: 640px) {
        .sa-modal { gap: 14px; padding: 22px 18px 18px; }
        .sa-modal .sa-icon { width: 40px; height: 40px; box-shadow: 0 0 0 5px rgb(var(--sa-accent) / .14); }
        .sa-modal .sa-icon svg { width: 20px; height: 20px; }
        .sa-title { font-size: 16px; }

        .sa--toast { width: auto; left: 12px; right: 12px; }
        .sa--top-left, .sa--top-right { top: 12px; }
        .sa--bottom-left, .sa--bottom-right { bottom: 12px; }

        .sa-banner-in { flex-wrap: wrap; gap: 10px; padding: 10px 12px 12px 14px; }
        .sa-banner-text { flex: 1 1 calc(100% - 90px); flex-direction: column; align-items: flex-start; gap: 2px; }
        .sa-banner-text span, .sa-banner-text.has-title span { padding-left: 0; border-left: 0; white-space: normal; }
        .sa-banner .sa-btn { order: 3; flex: 1 1 100%; }
    }

    @media (prefers-reduced-motion: reduce) {
        .sa-x, .sa-btn, .sa-toast-link svg { transition: none; }
    }
</style>
@endonce
