{{-- Calculadora: campos de medidas e card com o volume calculado --}}
<div class="stp-pv sk-row" style="--g: 10px; flex-direction: row; align-items: stretch">
    <div class="sk-col" style="--g: 6px; width: 50%; justify-content: center">
        <span class="sk sk-title" style="--w: 80%"></span>
        @foreach ([40, 30, 36] as $w)
            <div class="sk-col" style="--g: 3px">
                <span class="sk sk-line" style="--w: {{ $w }}%; height: 4px"></span>
                <div class="sk-row" style="--g: 0">
                    <span class="sk sk-input sk-grow" style="border-radius: 3px 0 0 3px"></span>
                    <span class="sk" style="width: 14px; height: 13px; border-radius: 0 3px 3px 0; background: var(--stp-sk-strong)"></span>
                </div>
            </div>
        @endforeach
    </div>
    <div class="sk-box sk-grow sk-col sk-center" style="--g: 6px; justify-content: center; border-color: rgba(var(--primary-500), .4); background: rgba(var(--primary-500), .06)">
        <span class="sk sk-line" style="--w: 50%; height: 4px"></span>
        <div class="sk-row" style="--g: 3px; align-items: flex-end">
            <span class="sk" style="width: 34px; height: 18px; border-radius: 4px; background: var(--stp-sk-strong)"></span>
            <span class="sk" style="width: 10px; height: 8px; border-radius: 2px; background: rgba(var(--primary-500), .55)"></span>
        </div>
        <span class="sk sk-btn is-accent" style="--w: 60%; margin-top: 3px"></span>
    </div>
</div>
