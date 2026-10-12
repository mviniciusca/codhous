{{-- Diferenciais: cabeçalho e quatro vantagens com ícone --}}
<div class="stp-pv" style="justify-content: center; gap: 10px">
    <div class="sk-col sk-center" style="--g: 4px">
        <span class="sk sk-title" style="--w: 44%"></span>
        <span class="sk sk-line" style="--w: 30%"></span>
    </div>
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px">
        @foreach (['M12 3l2.5 5.5 6 .6-4.5 4 1.3 5.9L12 16l-5.3 3 1.3-5.9-4.5-4 6-.6z', 'M12 3v18M3 12h18', 'M5 13l4 4L19 7', 'M12 8v4l3 2M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z'] as $path)
            <div class="sk-box sk-col sk-center" style="--g: 4px; padding: 9px 4px">
                <span style="display: grid; place-items: center; width: 20px; height: 20px; border-radius: 6px; border: 1px solid var(--stp-sk-strong); color: var(--stp-sk-strong)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 11px; height: 11px"><path d="{{ $path }}"/></svg>
                </span>
                <span class="sk sk-line" style="--w: 70%; height: 6px; background: var(--stp-sk-strong)"></span>
                <span class="sk sk-line" style="--w: 85%"></span>
                <span class="sk sk-line" style="--w: 60%"></span>
            </div>
        @endforeach
    </div>
</div>
