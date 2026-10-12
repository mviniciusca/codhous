{{-- FAQ: título e acordeão, com o primeiro item aberto --}}
<div class="stp-pv" style="justify-content: center; gap: 5px">
    <div class="sk-col sk-center" style="--g: 4px; margin-bottom: 3px">
        <span class="sk sk-title" style="--w: 44%"></span>
    </div>
    @foreach ([1, 2, 3, 4] as $i)
        <div class="sk-box" style="padding: 5px 7px; {{ $i === 1 ? 'border-color: rgba(var(--primary-500), .5)' : '' }}">
            <div class="sk-row" style="--g: 6px">
                <span class="sk sk-line sk-grow" style="--w: auto; max-width: {{ [62, 48, 56, 40][$i - 1] }}%; height: 5px; background: var(--stp-sk-strong)"></span>
                <span class="sk-grow"></span>
                <svg viewBox="0 0 20 20" fill="currentColor" style="width: 9px; height: 9px; color: var(--stp-sk-strong); {{ $i === 1 ? 'transform: rotate(180deg)' : '' }}"><path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
            </div>
            @if ($i === 1)
                <div class="sk-col" style="--g: 3px; margin-top: 5px">
                    <span class="sk sk-line" style="--w: 92%; height: 4px"></span>
                    <span class="sk sk-line" style="--w: 64%; height: 4px"></span>
                </div>
            @endif
        </div>
    @endforeach
</div>
