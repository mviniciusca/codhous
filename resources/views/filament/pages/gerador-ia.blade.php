<x-filament-panels::page>

    {{-- Google Fonts for preview --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link id="gf-link" rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family={{ urlencode($this->fontFamily) }}:wght@400;700;900&display=swap">

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 items-start">

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- COLUNA ESQUERDA — Criador de Posts                         --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div class="flex flex-col gap-4">

            {{-- ① Configuração Básica --}}
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="fi-section-header flex items-center gap-x-3 px-6 py-4 border-b border-gray-100 dark:border-white/5">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-primary-500 text-white text-xs font-bold shrink-0">1</span>
                    <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">Configuração Básica</h3>
                </div>
                <div class="fi-section-content p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div class="sm:col-span-2">
                        <x-filament::input.wrapper>
                            <x-slot name="label"><label class="text-sm font-medium text-gray-700 dark:text-gray-200">Título do Post</label></x-slot>
                            <x-filament::input
                                type="text"
                                wire:model.live.debounce.300ms="postTitle"
                                placeholder="Ex: Inspiração da Semana"
                                id="post-title"
                            />
                        </x-filament::input.wrapper>
                    </div>

                    {{-- Platform --}}
                    <div>
                        <label class="fi-fo-field-wrp-label block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Plataforma</label>
                        <select wire:model.live="platform" id="platform"
                                class="fi-input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                            @foreach ($this->platformOptions as $value => $opt)
                                <option value="{{ $value }}">{{ $opt['label'] }} — {{ $opt['size'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date (display only) --}}
                    <div>
                        <label class="fi-fo-field-wrp-label block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Data</label>
                        <x-filament::input
                            type="text"
                            value="{{ now()->format('d/m/Y') }}"
                            id="post-date"
                            disabled
                        />
                    </div>

                </div>
            </div>

            {{-- ② Conteúdo & Design --}}
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="fi-section-header flex items-center gap-x-3 px-6 py-4 border-b border-gray-100 dark:border-white/5">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-primary-500 text-white text-xs font-bold shrink-0">2</span>
                    <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">Conteúdo & Design</h3>
                </div>
                <div class="fi-section-content p-6 flex flex-col gap-4">

                    {{-- Quote --}}
                    <div>
                        <label class="fi-fo-field-wrp-label block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Texto / Quote</label>
                        <textarea wire:model.live.debounce.300ms="quote"
                                  id="quote"
                                  rows="4"
                                  maxlength="600"
                                  placeholder="Digite sua mensagem, frase ou quote aqui..."
                                  class="fi-input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
                        <p class="text-xs text-gray-400 mt-1 text-right">{{ strlen($quote ?? '') }}/600</p>
                    </div>

                    {{-- Font + Colors --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="fi-fo-field-wrp-label block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Fonte</label>
                            <select wire:model.live="fontFamily" id="font-family"
                                    class="fi-input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                                @foreach ($this->fontOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="fi-fo-field-wrp-label block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Cores</label>
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col items-center gap-1">
                                    <input type="color" wire:model.live="textColor" id="text-color"
                                           class="w-10 h-10 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer">
                                    <span class="text-xs text-gray-500">Texto</span>
                                </div>
                                <div class="flex flex-col items-center gap-1">
                                    <input type="color" wire:model.live="overlayColor" id="overlay-color"
                                           class="w-10 h-10 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer">
                                    <span class="text-xs text-gray-500">Overlay</span>
                                </div>
                                <div class="flex-1 flex flex-col gap-1">
                                    <input type="range" wire:model.live="overlayOpacity" id="overlay-opacity"
                                           min="0" max="100"
                                           class="w-full accent-primary-500">
                                    <span class="text-xs text-gray-500">Opacidade: {{ $overlayOpacity }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Background Gallery --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="fi-fo-field-wrp-label text-sm font-medium text-gray-700 dark:text-gray-200">Galeria de Fundos</label>
                            @if($backgroundImageId)
                                <button wire:click="selectBackground(0)"
                                        class="text-xs text-danger-500 hover:underline">Limpar seleção</button>
                            @endif
                        </div>

                        @if($this->backgrounds->isEmpty())
                            <div class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700 p-8 text-center">
                                <x-heroicon-o-photo class="w-10 h-10 text-gray-300 mb-2" />
                                <p class="text-sm text-gray-400">Nenhum fundo cadastrado.</p>
                                <a href="{{ route('filament.admin.resources.background-images.create') }}"
                                   class="mt-2 text-xs text-primary-500 hover:underline">
                                    Adicionar fundos na galeria →
                                </a>
                            </div>
                        @else
                            {{-- Flex-wrap of fixed 64×64 square thumbs --}}
                            <div class="flex flex-wrap gap-1.5 max-h-52 overflow-y-auto rounded-xl p-1.5 ring-1 ring-gray-100 dark:ring-white/5">
                                @foreach ($this->backgrounds as $bg)
                                    @php
                                        $thumbSrc = $bg->getThumbnailUrl()
                                            ?: $bg->getFirstMediaUrl('image');
                                    @endphp
                                    <button
                                        wire:key="bg-{{ $bg->id }}"
                                        wire:click="selectBackground({{ $bg->id }})"
                                        id="bg-{{ $bg->id }}"
                                        title="{{ $bg->name }}"
                                        style="width: 64px; height: 64px; flex-shrink: 0;"
                                        class="relative rounded-md overflow-hidden ring-2 transition-all duration-150
                                               {{ $backgroundImageId === $bg->id
                                                    ? 'ring-primary-500 shadow-md'
                                                    : 'ring-transparent hover:ring-primary-300' }}">
                                        @if($thumbSrc)
                                            <img src="{{ $thumbSrc }}"
                                                 alt="{{ $bg->name }}"
                                                 loading="lazy"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                                <x-heroicon-o-photo class="w-4 h-4 text-gray-400" />
                                            </div>
                                        @endif

                                        @if($backgroundImageId === $bg->id)
                                            <div class="absolute inset-0 bg-primary-500/25 flex items-center justify-center">
                                                <x-heroicon-s-check-circle class="w-5 h-5 text-white drop-shadow" />
                                            </div>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        @endif

                    </div>

                </div>
            </div>

        </div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- COLUNA DIREITA — Prévia em Tempo Real + Ações              --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div class="flex flex-col gap-4 sticky top-6">

            {{-- ③ Previsão & Ações --}}
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="fi-section-header flex items-center justify-between gap-x-3 px-6 py-4 border-b border-gray-100 dark:border-white/5">
                    <div class="flex items-center gap-3">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-primary-500 text-white text-xs font-bold shrink-0">3</span>
                        <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">Prévia em Tempo Real</h3>
                    </div>
                    <span class="text-xs text-gray-400">{{ $this->platformOptions[$platform]['size'] ?? '' }}</span>
                </div>

                <div class="p-6">
                    {{-- Preview Canvas --}}
                    <div class="relative w-full rounded-xl overflow-hidden shadow-xl"
                         style="padding-bottom: {{ $platform === 'story' ? '177.78%' : ($platform === 'facebook' || $platform === 'linkedin' ? '52.5%' : '100%') }}; background: #111;">

                        {{-- Background Image --}}
                        @if($this->selectedBackground?->getPreviewUrl())
                            <div class="absolute inset-0 bg-cover bg-center"
                                 style="background-image: url('{{ $this->selectedBackground->getPreviewUrl() }}');">
                            </div>
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center">
                                <x-heroicon-o-photo class="w-16 h-16 text-gray-500" />
                            </div>
                        @endif

                        {{-- Color Overlay --}}
                        <div class="absolute inset-0"
                             style="background: {{ $this->overlayCss }};"></div>

                        {{-- Quote Text --}}
                        <div class="absolute inset-0 flex items-center justify-center p-8 sm:p-12">
                            <div class="text-center" style="font-family: '{{ str_replace('+', ' ', $fontFamily) }}', sans-serif; color: {{ $textColor }};">
                                @if(trim($quote))
                                    <span class="block text-4xl sm:text-6xl font-black leading-none opacity-60 mb-2" style="font-size: clamp(2rem, 8vw, 5rem)">&ldquo;</span>
                                    <p class="font-bold leading-snug drop-shadow-lg"
                                       style="font-size: clamp(0.8rem, 3vw, 1.75rem); text-shadow: 0 2px 20px rgba(0,0,0,0.5);">
                                        {{ $quote }}
                                    </p>
                                @else
                                    <p class="text-sm opacity-40">Seu texto aparecerá aqui...</p>
                                @endif
                            </div>
                        </div>

                        {{-- Platform Badge --}}
                        <div class="absolute top-3 right-3 rounded-full bg-black/40 backdrop-blur px-2 py-1 text-xs text-white/80 font-medium">
                            {{ $this->platformOptions[$platform]['label'] ?? '' }}
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-4 flex flex-col sm:flex-row gap-3">
                        <x-filament::button
                            wire:click="dispatchGeneration"
                            wire:loading.attr="disabled"
                            wire:target="dispatchGeneration"
                            color="primary"
                            class="flex-1"
                            id="btn-generate"
                            icon="heroicon-o-queue-list">
                            <span wire:loading.remove wire:target="dispatchGeneration">Enviar para a Fila</span>
                            <span wire:loading wire:target="dispatchGeneration">Processando...</span>
                        </x-filament::button>

                        <x-filament::button
                            wire:click="resetForm"
                            color="gray"
                            id="btn-reset"
                            icon="heroicon-o-arrow-path">
                            Limpar
                        </x-filament::button>
                    </div>

                    {{-- Info --}}
                    <p class="mt-3 text-xs text-gray-400 text-center leading-relaxed">
                        A imagem será gerada em segundo plano via fila.
                        Você pode continuar criando outros posts enquanto aguarda.
                    </p>
                </div>
            </div>

            {{-- ④ Últimos Posts Gerados --}}
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
                 wire:poll.5s>
                <div class="fi-section-header flex items-center gap-x-3 px-6 py-4 border-b border-gray-100 dark:border-white/5">
                    <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">Últimos Gerados</h3>
                </div>
                <div class="fi-section-content p-4">
                    @if($this->recentPosts->isEmpty())
                        <p class="text-xs text-gray-400 text-center py-4">Nenhum post gerado ainda.</p>
                    @else
                        <div class="flex flex-col gap-2">
                            @foreach($this->recentPosts as $p)
                                <div class="flex items-center gap-3 p-2 rounded-lg bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-white/5">
                                    <div class="w-12 h-12 rounded-md overflow-hidden bg-gray-200 dark:bg-gray-700 shrink-0">
                                        @if($p->isGenerated())
                                            <img src="{{ $p->output_url }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <x-heroicon-o-arrow-path class="w-5 h-5 text-gray-400 animate-spin" />
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $p->title }}</p>
                                        <p class="text-[10px] text-gray-500">{{ $p->created_at->diffForHumans() }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if($p->isGenerated())
                                            <x-filament::link
                                                href="{{ $p->output_url }}"
                                                target="_blank"
                                                icon="heroicon-m-arrow-down-tray"
                                                color="primary"
                                                size="sm"
                                                download>
                                                Download
                                            </x-filament::link>
                                        @else
                                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                                {{ ucfirst($p->status) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick link to gallery --}}
            <a href="{{ route('filament.admin.resources.background-images.index') }}"
               class="group flex items-center gap-3 rounded-xl border border-dashed border-gray-200 dark:border-gray-700 px-4 py-3 text-sm text-gray-500 hover:border-primary-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                <x-heroicon-o-arrow-up-tray class="w-5 h-5 shrink-0 group-hover:text-primary-500 transition-colors" />
                <span>Gerenciar Galeria de Fundos</span>
                <x-heroicon-o-arrow-right class="w-4 h-4 ml-auto shrink-0 group-hover:translate-x-1 transition-transform" />
            </a>

        </div>
    </div>

    {{-- Live font reloader --}}
    <script>
        document.addEventListener('livewire:updated', function () {
            const font  = @this.fontFamily ?? 'Inter';
            const link  = document.getElementById('gf-link');
            const url   = `https://fonts.googleapis.com/css2?family=${encodeURIComponent(font)}:wght@400;700;900&display=swap`;
            if (link && link.href !== url) link.href = url;
        });
    </script>

</x-filament-panels::page>
