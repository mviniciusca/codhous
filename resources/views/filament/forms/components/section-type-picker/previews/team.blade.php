{{-- Nosso time: cards de pessoa com foto, nome e cargo --}}
<div class="stp-pv" style="justify-content: center; gap: 10px">
    <div class="sk-col sk-center" style="--g: 4px">
        <span class="sk sk-title" style="--w: 34%"></span>
        <span class="sk sk-line" style="--w: 48%"></span>
    </div>
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px">
        @foreach ([1, 2, 3, 4] as $i)
            <div class="sk-box sk-col sk-center" style="--g: 4px; padding: 8px 4px">
                <span class="sk-img" style="width: 26px; height: 26px; border-radius: 999px">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width: 14px; height: 14px"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm0 2c-4 0-7 2-7 4.5V20h14v-1.5C19 16 16 14 12 14Z"/></svg>
                </span>
                <span class="sk sk-line" style="--w: 70%; height: 6px; background: var(--stp-sk-strong)"></span>
                <span class="sk sk-line" style="--w: 50%; height: 4px; background: rgba(var(--primary-500), .4)"></span>
            </div>
        @endforeach
    </div>
</div>
