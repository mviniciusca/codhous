{{-- Serviços: cabeçalho e cards com ícone, nome, descrição e link --}}
<div class="stp-pv" style="justify-content: center; gap: 9px">
    <div class="sk-col sk-center" style="--g: 4px">
        <span class="sk sk-title" style="--w: 38%"></span>
        <span class="sk sk-line" style="--w: 56%"></span>
    </div>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px">
        @foreach ([1, 2, 3] as $i)
            <div class="sk-box sk-col" style="--g: 4px; padding: 8px 7px">
                <span class="sk" style="width: 16px; height: 16px; border-radius: 5px; background: rgba(var(--primary-500), .35)"></span>
                <span class="sk sk-line" style="--w: 75%; height: 6px; margin-top: 2px; background: var(--stp-sk-strong)"></span>
                <span class="sk sk-line" style="--w: 95%"></span>
                <span class="sk sk-line" style="--w: 70%"></span>
                <span class="sk sk-line" style="--w: 45%; height: 4px; margin-top: 3px; background: rgba(var(--primary-500), .45)"></span>
            </div>
        @endforeach
    </div>
</div>
