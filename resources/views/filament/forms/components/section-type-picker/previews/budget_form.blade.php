{{-- Formulário de orçamento: etapas no topo, campos e envio --}}
<div class="stp-pv" style="justify-content: center; gap: 8px">
    <div class="sk-box sk-col" style="--g: 7px; padding: 10px">
        <div class="sk-row" style="--g: 4px">
            @foreach ([1, 2, 3] as $step)
                <span style="width: 12px; height: 12px; border-radius: 999px; flex: none; {{ $step === 1 ? 'background: rgb(var(--primary-500))' : 'border: 1.5px solid var(--stp-sk-strong)' }}"></span>
                <span class="sk sk-line" style="--w: 26px; height: 4px"></span>
                @if (! $loop->last)<span style="flex: 1; border-top: 1px solid var(--stp-sk-strong)"></span>@endif
            @endforeach
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px">
            @foreach ([1, 2, 3, 4] as $i)
                <div class="sk-col" style="--g: 3px">
                    <span class="sk sk-line" style="--w: 40%; height: 4px"></span>
                    <span class="sk sk-input"></span>
                </div>
            @endforeach
        </div>
        <div class="sk-row" style="justify-content: space-between">
            <span class="sk sk-btn" style="--w: 36px"></span>
            <span class="sk sk-btn is-accent" style="--w: 52px"></span>
        </div>
    </div>
</div>
