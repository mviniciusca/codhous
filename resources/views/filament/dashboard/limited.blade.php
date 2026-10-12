{{-- Visão geral de quem não tem a área Comercial: atalhos só para o que o perfil libera --}}
@php $shortcuts = $this->getShortcuts(); @endphp

<div class="cx cx-ui">
    <x-dashboard.panel title="Seus atalhos" description="Áreas liberadas para o seu perfil de acesso" icon="heroicon-o-squares-2x2">
        @if (count($shortcuts))
            <ul class="cx-shortcuts" role="list">
                @foreach ($shortcuts as $item)
                    <li>
                        <a href="{{ $item['url'] }}" class="cx-shortcut">
                            <span class="cx-panel-icon" aria-hidden="true"><x-filament::icon :icon="$item['icon']" class="cx-icon" /></span>
                            <span class="cx-shortcut-text">
                                <strong>{{ $item['label'] }}</strong>
                                <span>{{ $item['area'] }}</span>
                            </span>
                            <x-filament::icon icon="heroicon-m-chevron-right" class="cx-row-go" aria-hidden="true" />
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <x-dashboard.empty icon="heroicon-o-lock-closed" title="Nenhuma área liberada" description="Peça a um administrador para ajustar o seu perfil de acesso." />
        @endif
    </x-dashboard.panel>
</div>
