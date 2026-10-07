<x-filament-panels::page>
    <form wire:submit="generate">
        {{ $this->form }}

        <div class="mt-4">
            <x-filament::button type="submit">
                Gerar Poster
            </x-filament::button>
        </div>
    </form>
    
    @if($generatedImageUrl)
        <div class="mt-8 bg-white p-6 rounded-lg shadow dark:bg-gray-800">
            <h2 class="text-xl font-bold mb-4">Resultado:</h2>
            <div class="flex flex-col items-center">
                <img src="{{ $generatedImageUrl }}" class="max-w-full h-auto rounded-lg shadow-lg border" style="max-height: 600px;" />
                
                <div class="mt-6 flex space-x-4">
                    <x-filament::button tag="a" href="{{ $generatedImageUrl }}" download="poster_gerado.jpg" icon="heroicon-o-arrow-down-tray">
                        Baixar Imagem
                    </x-filament::button>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
