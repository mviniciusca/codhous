{{-- Depoimentos: carrossel com avatar, estrelas e citação --}}
<div class="stp-pv sk-row" style="--g: 8px; flex-direction: row; align-items: center">
    <span class="sk sk-dot" style="--s: 14px; opacity: .7"></span>
    <div class="sk-grow sk-col" style="--g: 6px; height: 100%; justify-content: center">
        <div class="sk-box sk-col sk-center" style="--g: 5px; padding: 12px 10px">
            <span class="sk sk-dot" style="--s: 22px; background: var(--stp-sk-strong)"></span>
            <div class="sk-row" style="--g: 2px">
                @for ($i = 0; $i < 5; $i++)
                    <svg viewBox="0 0 20 20" fill="currentColor" style="width: 7px; height: 7px; color: rgba(250, 204, 21, .7)"><path d="M10 1.5l2.6 5.3 5.9.9-4.2 4.1 1 5.8L10 14.8l-5.3 2.8 1-5.8L1.5 7.7l5.9-.9L10 1.5z"/></svg>
                @endfor
            </div>
            <span class="sk sk-line" style="--w: 86%"></span>
            <span class="sk sk-line" style="--w: 70%"></span>
            <span class="sk sk-line" style="--w: 34%; height: 6px; margin-top: 2px; background: var(--stp-sk-strong)"></span>
        </div>
        <div class="sk-row" style="--g: 3px; justify-content: center">
            <span class="sk" style="width: 12px; height: 4px; border-radius: 2px; background: rgba(var(--primary-500), .6)"></span>
            <span class="sk sk-dot" style="--s: 4px"></span>
            <span class="sk sk-dot" style="--s: 4px"></span>
        </div>
    </div>
    <span class="sk sk-dot" style="--s: 14px; opacity: .7"></span>
</div>
