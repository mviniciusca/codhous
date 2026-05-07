<x-filament-panels::page>
    {{-- Google Fonts for preview --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link id="gf-link" rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family={{ urlencode($this->fontFamily) }}:wght@400;700;900&display=swap">

    <style>
        /* Estúdio Container */
        .studio-container {
            display: grid;
            grid-template-columns: 280px 1fr 100px;
            gap: 0;
            height: calc(100vh - 160px);
            margin: -24px;
            background: #0f1115;
            overflow: hidden;
            position: relative;
        }

        /* Painel Lateral Esquerdo (Ajustes) */
        .studio-sidebar-left {
            background: #181a20;
            border-right: 1px solid rgba(255,255,255,0.05);
            padding: 20px;
            display: flex;
            flex-col: column;
            gap: 20px;
            overflow-y: auto;
        }

        /* Canvas Central */
        .studio-canvas {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
            background-image: radial-gradient(circle, #23262d 1px, transparent 1px);
            background-size: 30px 30px;
        }

        /* Galeria Lateral Direita */
        .studio-sidebar-right {
            background: #181a20;
            border-left: 1px solid rgba(255,255,255,0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 12px 8px;
            gap: 12px;
            overflow-y: auto;
        }

        /* Floating Prompt Bar */
        .studio-prompt-bar {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            max-width: 90%;
            background: rgba(24, 26, 32, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 100px;
            padding: 8px 12px 8px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            z-index: 50;
        }

        .studio-prompt-input {
            flex: 1;
            background: transparent;
            border: none;
            color: white;
            font-size: 14px;
            outline: none !important;
            padding: 8px 0;
        }

        /* Thumbnail Estilo Print */
        .gallery-item {
            width: 64px;
            height: 64px;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
            opacity: 0.6;
        }
        .gallery-item:hover { opacity: 1; transform: scale(1.05); }
        .gallery-item.active {
            border-color: #fbbf24; /* Primary color */
            opacity: 1;
            transform: scale(1.1);
            box-shadow: 0 0 15px rgba(251, 191, 36, 0.3);
        }

        /* Canvas Preview Responsive */
        .preview-wrapper {
            box-shadow: 0 50px 100px -20px rgba(0,0,0,0.7);
            border-radius: 8px;
            overflow: hidden;
            background: #000;
            transition: all 0.3s ease;
        }

        /* Custom scrollbar para o estúdio */
        .studio-sidebar-left::-webkit-scrollbar,
        .studio-sidebar-right::-webkit-scrollbar {
            width: 4px;
        }
        .studio-sidebar-left::-webkit-scrollbar-thumb,
        .studio-sidebar-right::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }
    </style>

    <div class="studio-container rounded-2xl shadow-2xl ring-1 ring-white/10">
        
        {{-- Sidebar Esquerda: Ajustes --}}
        <aside class="studio-sidebar-left">
            <div class="flex items-center gap-2 mb-4 text-white/50 text-xs uppercase tracking-widest font-bold">
                <x-heroicon-o-adjustments-horizontal class="w-4 h-4" />
                <span>Propriedades</span>
            </div>

            <div class="space-y-6">
                {{-- Título --}}
                <div class="space-y-2">
                    <label class="text-xs text-gray-400 font-medium">Título do Projeto</label>
                    <input type="text" wire:model.live.debounce.300ms="postTitle"
                           placeholder="Dê um nome..."
                           class="w-full bg-[#23262d] border-none rounded-lg text-sm text-white placeholder-gray-600 focus:ring-1 focus:ring-amber-500">
                </div>

                {{-- Plataforma --}}
                <div class="space-y-2">
                    <label class="text-xs text-gray-400 font-medium">Formato / Plataforma</label>
                    <select wire:model.live="platform"
                            class="w-full bg-[#23262d] border-none rounded-lg text-sm text-white focus:ring-1 focus:ring-amber-500">
                        @foreach ($this->platformOptions as $value => $opt)
                            <option value="{{ $value }}">{{ $opt['label'] }} ({{ $opt['size'] }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tipografia --}}
                <div class="space-y-2">
                    <label class="text-xs text-gray-400 font-medium">Fonte</label>
                    <select wire:model.live="fontFamily"
                            class="w-full bg-[#23262d] border-none rounded-lg text-sm text-white focus:ring-1 focus:ring-amber-500">
                        @foreach ($this->fontOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Cores --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400 font-medium">Cor Texto</label>
                        <input type="color" wire:model.live="textColor" class="w-full h-10 bg-transparent border-none p-0 cursor-pointer rounded overflow-hidden">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs text-gray-400 font-medium">Filtro Cor</label>
                        <input type="color" wire:model.live="overlayColor" class="w-full h-10 bg-transparent border-none p-0 cursor-pointer rounded overflow-hidden">
                    </div>
                </div>

                {{-- Opacidade --}}
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <label class="text-xs text-gray-400 font-medium">Intensidade Filtro</label>
                        <span class="text-[10px] text-amber-500 font-bold">{{ $overlayOpacity }}%</span>
                    </div>
                    <input type="range" wire:model.live="overlayOpacity" min="0" max="100"
                           class="w-full accent-amber-500 bg-gray-700 h-1 rounded-lg appearance-none cursor-pointer">
                </div>

                <hr class="border-white/5 my-4">

                {{-- Recent Status --}}
                <div class="space-y-3" wire:poll.5s>
                    <label class="text-[10px] text-gray-500 uppercase tracking-widest font-black">Histórico Recente</label>
                    <div class="space-y-2 max-h-40 overflow-y-auto pr-2">
                        @foreach($this->recentPosts as $p)
                            <div class="flex items-center gap-2 p-2 rounded bg-white/5 border border-white/5 group relative">
                                <div class="w-8 h-8 rounded bg-gray-800 shrink-0 overflow-hidden">
                                    @if($p->isGenerated())
                                        <img src="{{ $p->output_url }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <x-heroicon-o-arrow-path class="w-3 h-3 text-white/20 animate-spin" />
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] text-white truncate font-medium">{{ $p->title }}</p>
                                    <p class="text-[8px] text-gray-500 uppercase">{{ $p->status }}</p>
                                </div>
                                @if($p->isGenerated())
                                    <a href="{{ $p->output_url }}" download class="opacity-0 group-hover:opacity-100 transition-opacity">
                                        <x-heroicon-m-arrow-down-tray class="w-3 h-3 text-amber-500" />
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </aside>

        {{-- Canvas Central --}}
        <main class="studio-canvas">
            
            <div class="preview-wrapper"
                 style="width: 500px; aspect-ratio: {{ $platform === 'story' ? '9/16' : ($platform === 'facebook' || $platform === 'linkedin' ? '1.91/1' : '1/1') }}; max-height: 80%;">
                
                <div class="relative w-full h-full">
                    {{-- BG --}}
                    @if($this->selectedBackground?->getPreviewUrl())
                        <div class="absolute inset-0 bg-cover bg-center transition-all duration-500"
                             style="background-image: url('{{ $this->selectedBackground->getPreviewUrl() }}');">
                        </div>
                    @else
                        <div class="absolute inset-0 bg-[#181a20] flex flex-col items-center justify-center">
                            <x-heroicon-o-sparkles class="w-12 h-12 text-white/5 mb-2" />
                            <span class="text-xs text-white/20">Selecione um fundo à direita</span>
                        </div>
                    @endif

                    {{-- Overlay --}}
                    <div class="absolute inset-0 transition-all duration-300"
                         style="background: {{ $this->overlayCss }};"></div>

                    {{-- Text --}}
                    <div class="absolute inset-0 flex items-center justify-center p-10 text-center">
                        <div style="font-family: '{{ str_replace('+', ' ', $fontFamily) }}', sans-serif; color: {{ $textColor }};">
                            @if(trim($quote))
                                <span class="block text-4xl font-black leading-none opacity-40 mb-4">&ldquo;</span>
                                <p class="font-bold leading-tight" style="font-size: 24px; text-shadow: 0 4px 30px rgba(0,0,0,0.6);">
                                    {{ $quote }}
                                </p>
                            @else
                                <p class="text-xs uppercase tracking-widest text-white/30 italic">Escreva algo na barra inferior...</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Floating Prompt Bar --}}
            <div class="studio-prompt-bar">
                <x-heroicon-o-chat-bubble-bottom-center-text class="w-5 h-5 text-amber-500 shrink-0" />
                <input type="text" 
                       wire:model.live.debounce.300ms="quote"
                       placeholder="O que você quer dizer neste post?"
                       class="studio-prompt-input"
                       maxlength="600">
                
                <div class="flex items-center gap-2">
                    <span class="text-[10px] text-gray-500 mr-2">{{ strlen($quote ?? '') }}/600</span>
                    <button wire:click="dispatchGeneration"
                            wire:loading.attr="disabled"
                            class="bg-amber-500 hover:bg-amber-400 text-black px-6 py-2 rounded-full text-xs font-bold transition-all flex items-center gap-2 shadow-lg shadow-amber-500/20">
                        <span wire:loading.remove wire:target="dispatchGeneration">Gerar Post</span>
                        <x-heroicon-o-arrow-path wire:loading wire:target="dispatchGeneration" class="w-3 h-3 animate-spin" />
                        <x-heroicon-o-sparkles wire:loading.remove wire:target="dispatchGeneration" class="w-3 h-3" />
                    </button>
                </div>
            </div>
        </main>

        {{-- Sidebar Direita: Galeria Vertical --}}
        <aside class="studio-sidebar-right">
            {{-- Upload Shortcut --}}
            <a href="{{ route('filament.admin.resources.background-images.create') }}" 
               title="Adicionar Fundo"
               class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-500 hover:bg-amber-500 hover:text-black transition-all mb-4">
                <x-heroicon-o-plus class="w-6 h-6" />
            </a>

            @foreach ($this->backgrounds as $bg)
                <div wire:click="selectBackground({{ $bg->id }})"
                     class="gallery-item {{ $backgroundImageId === $bg->id ? 'active' : '' }}">
                    <img src="{{ $bg->getThumbnailUrl() ?: $bg->getFirstMediaUrl('image') }}" 
                         alt="{{ $bg->name }}"
                         class="w-full h-full object-cover">
                </div>
            @endforeach

            <div class="mt-auto pt-4 border-t border-white/5 opacity-20 hover:opacity-100 transition-opacity">
                <a href="{{ route('filament.admin.resources.background-images.index') }}" title="Gerenciar Biblioteca">
                    <x-heroicon-o-rectangle-group class="w-6 h-6 text-white" />
                </a>
            </div>
        </aside>

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
