{{-- Hero: mídia de fundo, menu, selo, título, texto, dois botões e estatísticas --}}
<div class="stp-pv" style="padding: 0">
    <div class="sk-img" style="position: absolute; inset: 0; border-radius: 0; place-items: end; padding: 10px; background: var(--stp-sk-soft)">
        <svg viewBox="0 0 100 50" preserveAspectRatio="none" style="position: absolute; right: 0; bottom: 0; width: 58%; height: 70%; color: var(--stp-sk)" fill="currentColor">
            <path d="M0 50 L0 30 L20 12 L38 28 L60 6 L82 24 L100 14 L100 50 Z"/>
        </svg>
    </div>
    <div class="sk-col" style="position: relative; --g: 5px; height: 100%; padding: 9px 12px">
        <div class="sk-row" style="--g: 5px">
            <span class="sk sk-dot" style="--s: 8px; background: var(--stp-sk-strong)"></span>
            <span class="sk sk-line" style="--w: 16%; height: 4px; background: var(--stp-sk-strong)"></span>
            <span class="sk-grow"></span>
            @foreach ([12, 14, 12, 10] as $w)
                <span class="sk sk-line" style="--w: {{ $w }}px; height: 3px"></span>
            @endforeach
            <span class="sk sk-btn is-accent" style="--w: 22px; height: 8px"></span>
        </div>
        <div class="sk-col" style="--g: 4px; margin-top: auto; width: 54%">
            <span class="sk" style="width: 34px; height: 7px; border-radius: 999px; border: 1px solid rgba(var(--primary-500), .5); background: transparent"></span>
            <span class="sk sk-title" style="--w: 100%; height: 9px"></span>
            <span class="sk sk-title" style="--w: 78%; height: 9px"></span>
            <span class="sk sk-line" style="--w: 92%; margin-top: 2px"></span>
            <span class="sk sk-line" style="--w: 70%"></span>
            <div class="sk-row" style="--g: 4px; margin-top: 3px">
                <span class="sk sk-btn is-accent" style="--w: 38px"></span>
                <span class="sk sk-btn" style="--w: 34px; background: transparent; border: 1px solid var(--stp-sk-strong)"></span>
            </div>
        </div>
        <div class="sk-row" style="--g: 10px; margin-top: 6px; padding-top: 6px; border-top: 1px solid var(--stp-sk)">
            @foreach ([1, 2, 3] as $i)
                <div class="sk-row" style="--g: 3px">
                    <span class="sk" style="width: 9px; height: 9px; border-radius: 3px; background: rgba(var(--primary-500), .45)"></span>
                    <div class="sk-col" style="--g: 2px">
                        <span class="sk sk-line" style="--w: 16px; height: 5px; background: var(--stp-sk-strong)"></span>
                        <span class="sk sk-line" style="--w: 22px; height: 3px"></span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
