{{-- Showcase de equipamentos: cards de máquina com foto, especificações e ação --}}
<div class="stp-pv" style="gap: 8px; justify-content: center">
    <div class="sk-row" style="justify-content: space-between">
        <span class="sk sk-title" style="--w: 38%"></span>
        <div class="sk-row" style="--g: 3px">
            <span class="sk sk-dot" style="--s: 11px"></span>
            <span class="sk sk-dot" style="--s: 11px; background: var(--stp-sk-strong)"></span>
        </div>
    </div>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px">
        @foreach ([1, 2, 3] as $i)
            <div class="sk-box sk-col" style="--g: 4px; padding: 5px">
                <div class="sk-img" style="height: 40px">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px"><path d="M3 17h2m14 0h2M5 17a2 2 0 1 0 4 0 2 2 0 1 0-4 0m10 0a2 2 0 1 0 4 0 2 2 0 1 0-4 0M9 17h6M3 17V9h10v8M13 11h4l3 3v3"/></svg>
                </div>
                <span class="sk sk-line" style="--w: 75%; height: 6px; background: var(--stp-sk-strong)"></span>
                <div class="sk-row" style="--g: 3px">
                    <span class="sk" style="width: 16px; height: 6px; border-radius: 999px"></span>
                    <span class="sk" style="width: 18px; height: 6px; border-radius: 999px"></span>
                </div>
                <span class="sk sk-btn is-accent" style="--w: 100%; height: 9px"></span>
            </div>
        @endforeach
    </div>
</div>
