{{-- Cabeçalho do inspetor: componente escolhido (ícone, nome, descrição e identificador) --}}
@php
    /** @var array<string, mixed>|null $meta */
@endphp

@if ($meta)
    <div class="sb-summary">
        <span class="sb-summary-icon"><x-filament::icon :icon="$meta['icon']" /></span>
        <div class="sb-summary-text">
            <p class="sb-summary-title">{{ $meta['label'] }}</p>
            <p class="sb-summary-desc">{{ $meta['description'] }}</p>
            <div class="sb-summary-tags">
                <code class="sb-tag" title="Identificador do tipo">{{ $type }}</code>
                @foreach ($categories as $category)
                    <span class="sb-tag sb-tag--soft">{{ $category }}</span>
                @endforeach
                @if ($locked)
                    <span class="sb-tag sb-tag--soft" title="O tipo define os campos e não pode ser alterado depois de criado">
                        <x-filament::icon icon="heroicon-m-lock-closed" /> fixo
                    </span>
                @endif
            </div>
        </div>
    </div>
@else
    <div class="sb-summary sb-summary--empty">
        <span class="sb-summary-icon"><x-filament::icon icon="heroicon-o-cursor-arrow-rays" /></span>
        <div class="sb-summary-text">
            <p class="sb-summary-title">Nenhum componente selecionado</p>
            <p class="sb-summary-desc">Escolha um tipo de seção na biblioteca. Os campos de conteúdo aparecem de acordo com o componente.</p>
        </div>
    </div>
@endif
