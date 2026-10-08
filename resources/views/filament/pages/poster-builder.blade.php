<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
        <!-- Coluna da Esquerda: Formulário -->
        <div>
            <form wire:submit="generate">
                {{ $this->form }}

                <div class="mt-6">
                    <x-filament::button type="submit" class="w-full" size="lg">
                        Gerar Poster
                    </x-filament::button>
                </div>
            </form>
        </div>
        
        <!-- Coluna da Direita: Resultado -->
        <div class="sticky top-6">
            @if($generatedImageUrl)
                <div class="bg-white p-6 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <h2 class="text-lg font-medium mb-4">Resultado</h2>
                    <div class="flex flex-col items-center">
                        <img src="{{ $generatedImageUrl }}" class="max-w-full h-auto rounded-lg shadow-lg border border-gray-200 dark:border-gray-700" style="max-height: 600px;" />
                        
                        <div class="mt-6 w-full">
                            <x-filament::button tag="a" href="{{ $generatedImageUrl }}" download="poster_gerado.jpg" icon="heroicon-o-arrow-down-tray" class="w-full">
                                Baixar Imagem
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-gray-50 dark:bg-gray-900 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-xl p-12 flex flex-col items-center justify-center text-gray-500 dark:text-gray-400 min-h-[400px]">
                    <x-heroicon-o-photo class="w-16 h-16 mb-4 text-gray-400 dark:text-gray-500" />
                    <p class="text-sm text-center font-medium">O resultado aparecerá aqui</p>
                    <p class="text-xs text-center mt-2 text-gray-400 dark:text-gray-500">Selecione um template e clique em Gerar Poster.</p>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
