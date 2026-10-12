{{-- Contato: canais de atendimento à esquerda e formulário à direita --}}
<div class="stp-pv sk-row" style="--g: 10px; flex-direction: row; align-items: stretch">
    <div class="sk-col" style="--g: 7px; width: 42%; justify-content: center">
        <span class="sk sk-title" style="--w: 80%"></span>
        <span class="sk sk-line" style="--w: 90%"></span>
        @foreach ([70, 60, 80] as $w)
            <div class="sk-row" style="--g: 5px">
                <span class="sk" style="width: 13px; height: 13px; border-radius: 4px; background: rgba(var(--primary-500), .3)"></span>
                <span class="sk sk-line" style="--w: {{ $w }}%"></span>
            </div>
        @endforeach
    </div>
    <div class="sk-box sk-grow sk-col" style="--g: 5px; padding: 9px">
        <div class="sk-row" style="--g: 5px"><span class="sk sk-input sk-grow"></span><span class="sk sk-input sk-grow"></span></div>
        <span class="sk sk-input"></span>
        <span class="sk sk-input" style="height: 30px"></span>
        <span class="sk sk-btn is-accent" style="--w: 100%; margin-top: auto"></span>
    </div>
</div>
