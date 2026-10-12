{{-- Como funciona: etapas numeradas ligadas por uma linha --}}
<div class="stp-pv" style="justify-content: center; gap: 12px">
    <div class="sk-col sk-center" style="--g: 4px">
        <span class="sk sk-title" style="--w: 40%"></span>
    </div>
    <div style="position: relative; display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px">
        <span style="position: absolute; top: 10px; left: 16%; right: 16%; border-top: 1.5px dashed var(--stp-sk-strong)"></span>
        @foreach ([1, 2, 3] as $step)
            <div class="sk-col sk-center" style="--g: 5px; position: relative">
                <span style="display: grid; place-items: center; width: 21px; height: 21px; border-radius: 999px; font-size: 8px; font-weight: 700; color: var(--cx-muted); background: var(--stp-thumb-bg); border: 1.5px solid {{ $step === 1 ? 'rgb(var(--primary-500))' : 'var(--stp-sk-strong)' }}; {{ $step === 1 ? 'color: rgb(var(--primary-500))' : '' }}">{{ $step }}</span>
                <span class="sk sk-line" style="--w: 64%; height: 6px; background: var(--stp-sk-strong)"></span>
                <span class="sk sk-line" style="--w: 84%"></span>
                <span class="sk sk-line" style="--w: 56%"></span>
            </div>
        @endforeach
    </div>
</div>
