{{-- Parceiros comerciais: texto à esquerda e cards de parceiro com logo e descrição --}}
<div class="stp-pv sk-row" style="--g: 10px; flex-direction: row; align-items: stretch">
    <div class="sk-col" style="--g: 5px; width: 38%; justify-content: center">
        <span class="sk sk-line" style="--w: 40%; height: 4px; background: rgba(var(--primary-500), .5)"></span>
        <span class="sk sk-title" style="--w: 90%"></span>
        <span class="sk sk-title" style="--w: 60%"></span>
        <span class="sk sk-line" style="margin-top: 3px"></span>
        <span class="sk sk-line" style="--w: 80%"></span>
        <span class="sk sk-btn" style="margin-top: 6px; --w: 48px"></span>
    </div>
    <div class="sk-grow" style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px">
        @foreach ([1, 2, 3, 4] as $i)
            <div class="sk-box sk-col" style="--g: 4px; padding: 7px">
                <div class="sk-img" style="height: 20px; width: 34px">@include('filament.forms.components.section-type-picker.previews._img')</div>
                <span class="sk sk-line" style="--w: 70%; height: 6px; background: var(--stp-sk-strong)"></span>
                <span class="sk sk-line" style="--w: 90%"></span>
            </div>
        @endforeach
    </div>
</div>
