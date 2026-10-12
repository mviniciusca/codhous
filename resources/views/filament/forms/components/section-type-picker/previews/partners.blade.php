{{-- Clientes: cabeçalho centralizado e faixa de logotipos --}}
<div class="stp-pv" style="justify-content: center; gap: 12px">
    <div class="sk-col sk-center" style="--g: 5px">
        <span class="sk sk-line" style="--w: 22%; height: 4px; background: rgba(var(--primary-500), .5)"></span>
        <span class="sk sk-title" style="--w: 46%"></span>
        <span class="sk sk-line" style="--w: 34%"></span>
    </div>
    <div class="sk-row" style="--g: 8px">
        @foreach ([1, 2, 3, 4, 5] as $i)
            <div class="sk-box sk-row sk-grow" style="--g: 4px; height: 30px; justify-content: center">
                <span class="sk sk-dot" style="--s: 9px"></span>
                <span class="sk sk-line" style="--w: {{ [42, 30, 46, 34, 38][$i - 1] }}%; height: 6px"></span>
            </div>
        @endforeach
    </div>
    <div class="sk-row" style="--g: 8px; opacity: .55; padding-inline: 18px">
        @foreach ([1, 2, 3, 4] as $i)
            <div class="sk-box sk-grow" style="height: 20px"></div>
        @endforeach
    </div>
</div>
