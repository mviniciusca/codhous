{{-- Oferta de pagamento: faixa de destaque com selo, título, meios aceitos e botão --}}
<div class="stp-pv" style="justify-content: center">
    <div class="sk-box sk-row" style="--g: 10px; padding: 14px 12px; border-radius: 8px; border-color: rgba(var(--primary-500), .35); background: linear-gradient(135deg, rgba(var(--primary-500), .14), transparent 70%)">
        <div class="sk-col sk-grow" style="--g: 5px">
            <span class="sk" style="width: 40px; height: 8px; border-radius: 999px; background: rgba(var(--primary-500), .4)"></span>
            <div class="sk-row" style="--g: 4px">
                <span class="sk sk-title" style="--w: 38%; height: 11px"></span>
                <span class="sk" style="width: 30%; height: 11px; border-radius: 3px; background: rgba(var(--primary-500), .55)"></span>
            </div>
            <span class="sk sk-line" style="--w: 80%"></span>
            <div class="sk-row" style="--g: 4px; margin-top: 3px">
                @foreach ([1, 2, 3, 4] as $i)
                    <span class="sk" style="width: 18px; height: 11px; border-radius: 3px; border: 1px solid var(--stp-sk-strong); background: transparent"></span>
                @endforeach
            </div>
        </div>
        <span class="sk sk-btn is-accent" style="--w: 50px; height: 16px"></span>
    </div>
</div>
