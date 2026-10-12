{{-- Onde atuamos: texto com cidades e mapa com área de atendimento --}}
<div class="stp-pv sk-row" style="--g: 10px; flex-direction: row; align-items: stretch">
    <div class="sk-col" style="--g: 5px; width: 40%; justify-content: center">
        <span class="sk sk-title" style="--w: 80%"></span>
        <span class="sk sk-line"></span>
        <span class="sk sk-line" style="--w: 70%"></span>
        <div style="display: flex; flex-wrap: wrap; gap: 3px; margin-top: 5px">
            @foreach ([26, 20, 30, 18, 24] as $w)
                <span class="sk" style="width: {{ $w }}px; height: 9px; border-radius: 999px"></span>
            @endforeach
        </div>
    </div>
    <div class="sk-box sk-grow" style="position: relative; overflow: hidden">
        <svg viewBox="0 0 100 80" preserveAspectRatio="none" style="position: absolute; inset: 0; width: 100%; height: 100%; color: var(--stp-sk-strong)" fill="none" stroke="currentColor" stroke-width=".8">
            <path d="M0 22 Q30 18 50 30 T100 26"/><path d="M0 56 Q25 60 45 48 T100 58"/><path d="M28 0 Q34 40 22 80"/><path d="M70 0 Q62 36 76 80"/>
        </svg>
        <span style="position: absolute; left: 50%; top: 50%; width: 46px; height: 46px; transform: translate(-50%, -50%); border-radius: 999px; border: 1px dashed rgba(var(--primary-500), .7); background: rgba(var(--primary-500), .12)"></span>
        <svg viewBox="0 0 24 24" fill="currentColor" style="position: absolute; left: 50%; top: 50%; width: 14px; height: 14px; transform: translate(-50%, -80%); color: rgb(var(--primary-500))"><path d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5Z"/></svg>
    </div>
</div>
