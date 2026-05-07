<x-filament-panels::page>
    {{-- Google Fonts for preview --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link id="gf-link" rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family={{ urlencode($this->fontFamily) }}:wght@400;700;900&display=swap">

    <style>
        /* Estúdio Container Totalmente Imersivo */
        .studio-container {
            display: grid;
            grid-template-columns: 280px 1fr 100px;
            gap: 0;
            height: calc(100vh - 64px); /* Desconta apenas o TopBar do Filament */
            margin: -40px -24px -40px -24px; /* Estica para todas as bordas */
            background: #ffffff;
            overflow: hidden;
            position: relative;
            transition: background 0.3s ease;
        }

        .dark .studio-container {
            background: #09090b; /* Zinc 950 */
        }

        /* Painéis Laterais */
        .studio-sidebar-left {
            background: #f8fafc;
            border-right: 1px solid rgba(0,0,0,0.05);
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            overflow-y: auto;
        }

        .dark .studio-sidebar-left {
            background: #111114;
            border-right: 1px solid rgba(255,255,255,0.05);
        }

        /* Canvas Central */
        .studio-canvas {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
            background-color: #f1f5f9;
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 30px 30px;
        }

        .dark .studio-canvas {
            background-color: #0f1115;
            background-image: radial-gradient(rgba(255,255,255,0.05) 1px, transparent 1px);
        }

        /* Galeria Lateral Direita */
        .studio-sidebar-right {
            background: #f8fafc;
            border-left: 1px solid rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 12px 8px;
            gap: 12px;
            overflow-y: auto;
        }

        .dark .studio-sidebar-right {
            background: #111114;
            border-left: 1px solid rgba(255,255,255,0.05);
        }

        /* Floating Prompt Bar */
        .studio-prompt-bar {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            max-width: 90%;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 100px;
            padding: 8px 12px 8px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            z-index: 50;
        }

        .dark .studio-prompt-bar {
            background: rgba(24, 26, 32, 0.85);
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }

        .studio-prompt-input {
            flex: 1;
            background: transparent;
            border: none;
            color: #1f2937;
            font-size: 14px;
            outline: none !important;
            padding: 8px 0;
        }

        .dark .studio-prompt-input {
            color: white;
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
            opacity: 0.8;
            background: #e2e8f0;
        }
        .dark .gallery-item { background: #1e293b; opacity: 0.6; }
        
        .gallery-item:hover { opacity: 1; transform: scale(1.05); }
        .gallery-item.active {
            border-color: #fbbf24;
            opacity: 1;
            transform: scale(1.1);
            box-shadow: 0 0 15px rgba(251, 191, 36, 0.3);
        }

        /* Preview Wrapper */
        .preview-wrapper {
            box-shadow: 0 40px 80px -20px rgba(0,0,0,0.2);
            border-radius: 12px;
            overflow: hidden;
            background: #000;
            transition: all 0.3s ease;
        }
        .dark .preview-wrapper {
            box-shadow: 0 50px 100px -20px rgba(0,0,0,0.7);
        }

        /* Inputs Custom */
        .studio-input {
            width: 100%;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 13px;
            color: #1f2937;
            padding: 8px 12px;
            outline: none !important;
            transition: all 0.2s;
            appearance: none;
        }
        
        select.studio-input {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5' /%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 14px;
            padding-right: 32px;
        }

        .dark .studio-input {
            background: #1e293b;
            border: 1px solid rgba(255,255,255,0.05);
            color: white;
        }

        .dark select.studio-input {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5' /%3E%3C/svg%3E");
        }

        .studio-input:focus {
            border-color: #fbbf24;
            box-shadow: 0 0 0 2px rgba(251, 191, 36, 0.1);
        }

        /* Color Input Styling */
        .color-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            padding: 6px 10px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-size: 11px;
            font-family: monospace;
            color: #64748b;
        }
        .dark .color-pill {
            background: #1e293b;
            border: 1px solid rgba(255,255,255,0.05);
            color: #94a3b8;
        }
        .color-circle {
            width: 20px;
            height: 20px;
            border-radius: 6px;
            border: 1px solid rgba(0,0,0,0.05);
            cursor: pointer;
            padding: 0;
            background: none;
        }
        .color-circle::-webkit-color-swatch-wrapper { padding: 0; }
        .color-circle::-webkit-color-swatch { border: none; border-radius: 5px; }
    </style>

    <div class="studio-container rounded-b-2xl">
        
        {{-- Sidebar Esquerda: Ajustes --}}
        <aside class="studio-sidebar-left">
            <div class="flex items-center gap-2 mb-2 text-gray-400 dark:text-white/40 text-[10px] uppercase tracking-widest font-black">
                <x-heroicon-o-adjustments-horizontal class="w-4 h-4" />
                <span>Propriedades</span>
            </div>

            <div class="space-y-6">
                {{-- Título --}}
                <div class="space-y-2">
                    <label class="text-[10px] text-gray-500 uppercase font-bold">Título do Post</label>
                    <input type="text" wire:model.live.debounce.300ms="postTitle"
                           placeholder="Ex: Quote do Dia"
                           class="studio-input focus:ring-2 focus:ring-amber-500">
                </div>

                {{-- Plataforma --}}
                <div class="space-y-2">
                    <label class="text-[10px] text-gray-500 uppercase font-bold">Formato</label>
                    <select wire:model.live="platform" class="studio-input focus:ring-2 focus:ring-amber-500">
                        @foreach ($this->platformOptions as $value => $opt)
                            <option value="{{ $value }}">{{ $opt['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tipografia --}}
                <div class="space-y-2">
                    <label class="text-[10px] text-gray-500 uppercase font-bold">Fonte</label>
                    <select wire:model.live="fontFamily" class="studio-input focus:ring-2 focus:ring-amber-500">
                        @foreach ($this->fontOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Cores --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] text-gray-500 uppercase font-bold">Texto</label>
                        <div class="color-pill shadow-sm">
                            <input type="color" wire:model.live="textColor" class="color-circle">
                            <span class="uppercase tracking-tighter">{{ $textColor }}</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] text-gray-500 uppercase font-bold">Filtro</label>
                        <div class="color-pill shadow-sm">
                            <input type="color" wire:model.live="overlayColor" class="color-circle">
                            <span class="uppercase tracking-tighter">{{ $overlayColor }}</span>
                        </div>
                    </div>
                </div>

                {{-- Opacidade --}}
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <label class="text-[10px] text-gray-500 uppercase font-bold">Opacidade Filtro</label>
                        <span class="text-[10px] text-amber-500 font-bold">{{ $overlayOpacity }}%</span>
                    </div>
                    <input type="range" wire:model.live="overlayOpacity" min="0" max="100"
                           class="w-full accent-amber-500 bg-gray-200 dark:bg-gray-700 h-1.5 rounded-lg appearance-none cursor-pointer">
                </div>

                <hr class="border-gray-200 dark:border-white/5 my-4">

                {{-- Recent Status --}}
                <div class="space-y-3" wire:poll.5s>
                    <label class="text-[10px] text-gray-400 uppercase tracking-widest font-black">Fila de Geração</label>
                    <div class="space-y-2 max-h-48 overflow-y-auto pr-2">
                        @foreach($this->recentPosts as $p)
                            <div class="flex items-center gap-2 p-2 rounded-lg bg-white dark:bg-white/5 border border-gray-100 dark:border-white/5 group relative">
                                <div class="w-10 h-10 rounded-md bg-gray-100 dark:bg-gray-800 shrink-0 overflow-hidden shadow-inner">
                                    @if($p->isGenerated())
                                        <img src="{{ $p->output_url }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <x-heroicon-o-arrow-path class="w-4 h-4 text-gray-400 animate-spin" />
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[11px] text-gray-900 dark:text-white truncate font-bold">{{ $p->title }}</p>
                                    <p class="text-[9px] text-gray-500 uppercase">{{ $p->status }} • {{ $p->created_at->diffForHumans() }}</p>
                                </div>
                                @if($p->isGenerated())
                                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ $p->output_url }}" download title="Download" class="p-1 hover:bg-amber-500/10 rounded">
                                            <x-heroicon-m-arrow-down-tray class="w-4 h-4 text-amber-500" />
                                        </a>
                                        <button wire:click="regeneratePost({{ $p->id }})" title="Regerar" class="p-1 hover:bg-blue-500/10 rounded">
                                            <x-heroicon-m-arrow-path class="w-3 h-3 text-blue-500" />
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </aside>

        {{-- Canvas Central --}}
        <main class="studio-canvas">
            
            <div class="preview-wrapper shadow-2xl"
                 style="width: 500px; aspect-ratio: {{ $platform === 'story' ? '9/16' : ($platform === 'facebook' || $platform === 'linkedin' ? '1.91/1' : '1/1') }}; max-height: 85%;">
                
                <div class="relative w-full h-full">
                    {{-- BG --}}
                    @if($this->selectedBackground?->getPreviewUrl())
                        <div class="absolute inset-0 bg-cover bg-center transition-all duration-700"
                             style="background-image: url('{{ $this->selectedBackground->getPreviewUrl() }}');">
                        </div>
                    @else
                        <div class="absolute inset-0 bg-[#e2e8f0] dark:bg-[#181a20] flex flex-col items-center justify-center">
                            <x-heroicon-o-photo class="w-16 h-16 text-gray-300 dark:text-white/5 mb-2" />
                            <span class="text-xs text-gray-400 dark:text-white/20">Selecione um fundo à direita</span>
                        </div>
                    @endif

                    {{-- Overlay --}}
                    <div class="absolute inset-0 transition-all duration-300"
                         style="background: {{ $this->overlayCss }};"></div>

                    {{-- Text --}}
                    <div class="absolute inset-0 flex items-center justify-center p-12 text-center">
                        <div style="font-family: '{{ str_replace('+', ' ', $fontFamily) }}', sans-serif; color: {{ $textColor }};">
                            @if(trim($quote))
                                <span class="block text-5xl font-black leading-none opacity-40 mb-4">&ldquo;</span>
                                <p class="font-bold leading-tight tracking-tight" style="font-size: 26px; text-shadow: 0 4px 30px rgba(0,0,0,0.6);">
                                    {{ $quote }}
                                </p>
                            @else
                                <p class="text-[10px] uppercase tracking-[0.2em] text-gray-400 dark:text-white/30 font-bold italic">Digite o seu quote abaixo</p>
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
                       placeholder="Sua frase de impacto aqui..."
                       class="studio-prompt-input"
                       maxlength="600">
                
                <div class="flex items-center gap-3">
                    <span class="text-[10px] text-gray-400 font-mono">{{ strlen($quote ?? '') }}/600</span>
                    <button wire:click="dispatchGeneration"
                            wire:loading.attr="disabled"
                            class="bg-amber-500 hover:bg-amber-400 text-black px-8 py-2.5 rounded-full text-xs font-black transition-all flex items-center gap-2 shadow-lg shadow-amber-500/20 active:scale-95 border-none">
                        <span wire:loading.remove wire:target="dispatchGeneration">GERAR</span>
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
               title="Adicionar Novo Fundo"
               class="w-14 h-14 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-500 hover:bg-amber-500 hover:text-white transition-all mb-4 group">
                <x-heroicon-o-plus class="w-7 h-7 group-hover:rotate-90 transition-transform" />
            </a>

            <div class="flex flex-col gap-3 pb-8">
                @foreach ($this->backgrounds as $bg)
                    <div wire:click="selectBackground({{ $bg->id }})"
                         title="{{ $bg->name }}"
                         class="gallery-item {{ $backgroundImageId === $bg->id ? 'active' : '' }} shadow-sm">
                        <img src="{{ $bg->getThumbnailUrl() ?: $bg->getFirstMediaUrl('image') }}" 
                             alt="{{ $bg->name }}"
                             class="w-full h-full object-cover">
                    </div>
                @endforeach
            </div>

            <div class="mt-auto pt-4 border-t border-gray-200 dark:border-white/5 opacity-30 hover:opacity-100 transition-opacity">
                <a href="{{ route('filament.admin.resources.background-images.index') }}" title="Biblioteca Completa">
                    <x-heroicon-o-rectangle-group class="w-6 h-6 text-gray-500 dark:text-white" />
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
