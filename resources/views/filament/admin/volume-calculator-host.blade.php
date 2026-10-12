{{-- Host do modal “Calculadora de volume”: abre com open-modal id=volume-calculator (topbar e Visão geral). O componente só carrega quando o modal abre --}}
<x-filament::modal
    id="volume-calculator"
    width="6xl"
    icon="heroicon-o-cube"
    heading="Calculadora de volume"
    description="Volume de concreto com margem de segurança e memória de cálculo, pelas fórmulas da empresa."
    :close-by-clicking-away="false"
>
    @livewire(\App\Livewire\VolumeCalculatorTool::class, ['variant' => 'modal', 'lazy' => true], key('volume-calculator-modal'))

    <x-slot name="footer">
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end; gap: 16px;">
            <x-filament::link :href="\App\Filament\Resources\VolumeCalculationResource::getUrl()" icon="heroicon-m-clock" size="sm" color="gray">
                Histórico de cálculos
            </x-filament::link>
            <x-filament::link :href="\App\Filament\Clusters\ToolsCluster\Pages\VolumeCalculatorPage::getUrl()" icon="heroicon-m-arrow-top-right-on-square" size="sm">
                Abrir página completa
            </x-filament::link>
        </div>
    </x-slot>
</x-filament::modal>
