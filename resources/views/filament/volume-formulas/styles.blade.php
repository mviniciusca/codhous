{{-- Editor de fórmulas de volume (CSS puro). Prefixo vf-; tokens vêm de filament/admin/ui --}}
@once
<style>
    .vf-test { display: grid; gap: 12px; font-size: 13px; }
    .vf-status { display: flex; align-items: center; gap: 6px; font-weight: 600; color: var(--tone-fg); }
    .vf-box {
        display: grid;
        gap: 6px;
        padding: 10px 12px;
        border-radius: 10px;
        color: var(--tone-fg);
        background: rgba(var(--tone), .1);
        box-shadow: inset 0 0 0 1px rgba(var(--tone), .2);
    }
    .vf-box ul { display: grid; gap: 2px; padding-left: 18px; list-style: disc; }
    .vf-muted { color: var(--cx-muted); }
    .vf-result {
        display: grid;
        gap: 2px;
        padding: 14px 16px;
        border-radius: 12px;
        color: #fff;
        background: linear-gradient(160deg, rgb(var(--primary-700)), rgb(var(--primary-950)));
    }
    .vf-result span { font-size: 11px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; opacity: .8; }
    .vf-result strong { font-size: 28px; font-weight: 700; letter-spacing: -.02em; font-variant-numeric: tabular-nums; }
    .vf-rows { display: grid; gap: 4px; }
    .vf-rows > div { display: flex; justify-content: space-between; gap: 12px; }
    .vf-rows dt { color: var(--cx-muted); }
    .vf-rows dd { text-align: right; font-variant-numeric: tabular-nums; }
    .vf-rows small { color: var(--cx-subtle); font-size: 12px; }
    .vf-steps { display: grid; gap: 8px; padding-top: 10px; border-top: 1px solid var(--cx-border); }
    .vf-steps span { display: block; font-size: 12px; font-weight: 600; color: var(--cx-muted); }
    .vf-steps code, .vf-code {
        display: inline-block;
        padding: 2px 6px;
        border-radius: 6px;
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-size: 12px;
        overflow-wrap: anywhere;
        background: var(--cx-surface-2);
        box-shadow: inset 0 0 0 1px var(--cx-border);
    }
    .vf-vars { display: flex; flex-wrap: wrap; gap: 6px 12px; font-size: 13px; }
    .vf-vars small { color: var(--cx-muted); }
</style>
@endonce
