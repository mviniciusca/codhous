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
            height: calc(100vh - 64px); 
            margin: -40px -24px -40px -24px; 
            background: #ffffff;
            overflow: hidden;
            position: relative;
            transition: background 0.3s ease;
        }

        .dark .studio-container { background: #09090b; }

        .studio-sidebar-left {
            background: #f8fafc;
            border-right: 1px solid rgba(0,0,0,0.05);
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            overflow-y: auto;
        }

        .dark .studio-sidebar-left { background: #111114; border-right: 1px solid rgba(255,255,255,0.05); }

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

        .dark .studio-canvas { background-color: #0f1115; background-image: radial-gradient(rgba(255,255,255,0.05) 1px, transparent 1px); }

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

        .dark .studio-sidebar-right { background: #111114; border-left: 1px solid rgba(255,255,255,0.05); }

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

        .dark .studio-prompt-bar { background: rgba(24, 26, 32, 0.85); border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 20px 40px rgba(0,0,0,0.4); }

        .studio-prompt-input {
            flex: 1;
            background: transparent;
            border: none;
            color: #1f2937;
            font-size: 14px;
            outline: none !important;
            padding: 8px 0;
        }

        .dark .studio-prompt-input { color: white; }

        /* Presets Selectors */
        .preset-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .preset-btn { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 8px; text-align: center; cursor: pointer; transition: all 0.2s; position: relative; }
        .dark .preset-btn { background: #1e293b; border-color: rgba(255,255,255,0.05); }
        .preset-btn.active { border-color: #fbbf24; background: #fffcf0; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(251, 191, 36, 0.1); }
        .dark .preset-btn.active { background: #2d2613; border-color: #fbbf24; }
        .preset-btn span { font-size: 9px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
        .dark .preset-btn span { color: #94a3b8; }
        .preset-btn.active span { color: #b45309; }
        .dark .preset-btn.active span { color: #fbbf24; }

        /* Preview Canvas Logic */
        .preview-wrapper {
            box-shadow: 0 40px 80px -20px rgba(0,0,0,0.2);
            border-radius: 12px;
            overflow: hidden;
            background: #000;
            position: relative;
        }

        /* Preset Styles */
        .style-max { text-align: left; justify-content: flex-start; padding: 80px; }
        .style-max .quote-text { text-transform: uppercase; font-weight: 900; font-size: 42px; line-height: 0.95; }
        .style-max .title-text { font-size: 14px; text-transform: uppercase; letter-spacing: 0.3em; margin-bottom: 15px; opacity: 0.8; }

        .style-flux { text-align: center; justify-content: center; padding: 60px; }
        .style-flux .quote-text { font-weight: 400; font-style: italic; font-size: 32px; }
        .style-flux .title-text { font-size: 12px; letter-spacing: 0.5em; margin-bottom: 40px; }

        .style-canva_side { display: flex; align-items: stretch; justify-content: flex-start; text-align: left; }
        .style-canva_side .side-block { width: 45%; height: 100%; background: var(--highlight); padding: 40px; display: flex; flex-direction: column; justify-content: center; z-index: 20; position: relative; }
        .style-canva_side .inner-image { width: 55%; left: auto; right: 0; }
        .style-canva_side .text-container { position: relative; padding: 0; }

        .style-minimal { align-items: flex-end; justify-content: flex-start; text-align: left; padding: 40px; }
        .style-minimal .quote-text { font-size: 20px; font-weight: 300; }

        /* Color Pill Fixes */
        .studio-input { width: 100%; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; color: #1f2937; padding: 8px 12px; appearance: none; }
        .dark .studio-input { background: #1e293b; border: 1px solid rgba(255,255,255,0.05); color: white; }
        
        select.studio-input {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5' /%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 10px center; background-size: 14px; padding-right: 32px;
        }

        .color-pill { display: flex; align-items: center; gap: 8px; background: #ffffff; padding: 6px 10px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 11px; font-family: monospace; }
        .dark .color-pill { background: #1e293b; border: 1px solid rgba(255,255,255,0.05); color: #94a3b8; }
        .color-circle { width: 20px; height: 20px; border-radius: 6px; cursor: pointer; border: 1px solid rgba(0,0,0,0.1); }

        /* Galeria de Miniaturas */
        .gallery-item {
            width: 64px;
            height: 64px;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s ease;
            background: #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .dark .gallery-item { background: #1e293b; }
        .gallery-item:hover { transform: scale(1.05); border-color: rgba(251, 191, 36, 0.5); }
        .gallery-item.active { border-color: #fbbf24; box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.2); }
        .gallery-item img { width: 100%; height: 100%; object-fit: cover; }
    </style>

    <div class="studio-container rounded-b-2xl" style="--highlight: {{ $overlayColor }};">
        
        {{-- Sidebar Esquerda: Ajustes --}}
        <aside class="studio-sidebar-left">
            <div class="flex items-center gap-2 mb-2 text-gray-400 dark:text-white/40 text-[10px] uppercase tracking-widest font-black">
                <x-heroicon-o-adjustments-horizontal class="w-4 h-4" />
                <span>Branding / Estilos</span>
            </div>

            <div class="space-y-5">
                {{-- Presets --}}
                <div class="space-y-2">
                    <label class="text-[10px] text-gray-500 uppercase font-bold">Design Presets</label>
                    <div class="preset-grid">
                        @foreach($this->presetOptions as $val => $lbl)
                            <div wire:click="selectPreset('{{ $val }}')" 
                                 class="preset-btn {{ $preset === $val ? 'active' : '' }}">
                                <span>{{ str_replace('Estilo ', '', $lbl) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] text-gray-500 uppercase font-bold">Título / Label</label>
                    <input type="text" wire:model.live.debounce.300ms="postTitle" class="studio-input" placeholder="Ex: NOVIDADE">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-2">
                        <label class="text-[10px] text-gray-500 uppercase font-bold">Formato</label>
                        <select wire:model.live="platform" class="studio-input">
                            @foreach ($this->platformOptions as $v => $o) <option value="{{ $v }}">{{ $o['label'] }}</option> @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] text-gray-500 uppercase font-bold">Fonte</label>
                        <select wire:model.live="fontFamily" class="studio-input">
                            @foreach ($this->fontOptions as $v => $l) <option value="{{ $v }}">{{ $l }}</option> @endforeach
                        </select>
                    </div>
                </div>

                {{-- Cores --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-2">
                        <label class="text-[10px] text-gray-500 uppercase font-bold">Texto</label>
                        <div class="color-pill shadow-sm">
                            <input type="color" wire:model.live="textColor" class="color-circle">
                            <span>{{ $textColor }}</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] text-gray-500 uppercase font-bold">Highlight</label>
                        <div class="color-pill shadow-sm">
                            <input type="color" wire:model.live="overlayColor" class="color-circle">
                            <span>{{ $overlayColor }}</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <label class="text-[10px] text-gray-500 uppercase font-bold">Opacidade Filtro</label>
                        <span class="text-[10px] text-amber-500 font-bold">{{ $overlayOpacity }}%</span>
                    </div>
                    <input type="range" wire:model.live="overlayOpacity" min="0" max="100" class="w-full accent-amber-500">
                </div>

                <hr class="border-gray-200 dark:border-white/5 my-2">

                {{-- Recent Status --}}
                <div class="space-y-3" wire:poll.5s>
                    <label class="text-[10px] text-gray-400 uppercase tracking-widest font-black">Fila de Geração</label>
                    <div class="space-y-2 max-h-40 overflow-y-auto pr-2">
                        @foreach($this->recentPosts as $p)
                            <div class="flex items-center gap-2 p-2 rounded-lg bg-white dark:bg-white/5 border border-gray-100 dark:border-white/5 group">
                                <div class="w-8 h-8 rounded bg-gray-100 dark:bg-gray-800 shrink-0 overflow-hidden">
                                    @if($p->isGenerated()) <img src="{{ $p->output_url }}" class="w-full h-full object-cover"> @else <x-heroicon-o-arrow-path class="w-4 h-4 text-gray-400 animate-spin m-2" /> @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] text-gray-900 dark:text-white truncate font-bold">{{ $p->title }}</p>
                                    <p class="text-[8px] text-gray-500 uppercase">{{ $p->status }}</p>
                                </div>
                                @if($p->isGenerated())
                                    <a href="{{ $p->output_url }}" download class="opacity-0 group-hover:opacity-100 transition-opacity"><x-heroicon-m-arrow-down-tray class="w-3 h-3 text-amber-500" /></a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </aside>

        {{-- Canvas Central --}}
        <main class="studio-canvas">
            
            <div class="preview-wrapper style-{{ $preset }}"
                 style="width: 500px; aspect-ratio: {{ $platform === 'story' ? '9/16' : ($platform === 'facebook' || $platform === 'linkedin' ? '1.91/1' : '1/1') }}; max-height: 85%;">
                
                {{-- Background Base --}}
                <div class="absolute inset-0 bg-[#000]">
                     @if($this->selectedBackground?->getPreviewUrl())
                        <div class="inner-image absolute inset-0 bg-cover bg-center transition-all duration-700"
                             style="background-image: url('{{ $this->selectedBackground->getPreviewUrl() }}');">
                        </div>
                    @else
                        <div class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900">
                             <x-heroicon-o-photo class="w-12 h-12 text-white/5" />
                        </div>
                    @endif
                </div>

                {{-- Global Overlay --}}
                <div class="absolute inset-0 transition-all duration-300" style="background: {{ $this->overlayCss }};"></div>

                {{-- Style Specific Blocks --}}
                @if($preset === 'canva_side') <div class="side-block"></div> @endif

                {{-- Text Content --}}
                <div class="absolute inset-0 flex flex-col text-container" 
                     style="font-family: '{{ str_replace('+', ' ', $fontFamily) }}', sans-serif; color: {{ $textColor }}; z-index: 30;">
                    
                    @php
                        $presetEnum = \App\Enums\CardPreset::tryFrom($preset);
                        $style = $presetEnum ? $presetEnum->getStyle() : [];
                    @endphp

                    <div class="w-full">
                        @if($postTitle)
                            <span class="title-text block font-black">{{ $postTitle }}</span>
                        @endif
                        
                        @if(trim($quote))
                            <p class="quote-text font-bold leading-tight" style="text-shadow: 0 4px 30px rgba(0,0,0,0.6);">{{ $quote }}</p>
                        @else
                            <p class="text-[10px] uppercase tracking-[0.2em] text-gray-400 dark:text-white/30 font-bold italic">Digite o seu quote abaixo</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Floating Prompt Bar --}}
            <div class="studio-prompt-bar">
                <x-heroicon-o-chat-bubble-bottom-center-text class="w-5 h-5 text-amber-500 shrink-0" />
                <input type="text" wire:model.live.debounce.300ms="quote" placeholder="Sua frase de impacto..." class="studio-prompt-input" maxlength="600">
                
                <div class="flex items-center gap-3">
                    <span class="text-[10px] text-gray-400 font-mono">{{ strlen($quote ?? '') }}/600</span>
                    <button wire:click="dispatchGeneration" wire:loading.attr="disabled" class="bg-amber-500 hover:bg-amber-400 text-black px-8 py-2.5 rounded-full text-xs font-black transition-all flex items-center gap-2 shadow-lg shadow-amber-500/20">
                        <span wire:loading.remove wire:target="dispatchGeneration">GERAR</span>
                        <x-heroicon-o-arrow-path wire:loading wire:target="dispatchGeneration" class="w-3 h-3 animate-spin" />
                    </button>
                </div>
            </div>
        </main>

        {{-- Sidebar Direita: Galeria Vertical --}}
        <aside class="studio-sidebar-right">
            <a href="{{ route('filament.admin.resources.background-images.create') }}" class="w-14 h-14 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-500 hover:bg-amber-500 hover:text-white transition-all mb-4"><x-heroicon-o-plus class="w-7 h-7" /></a>
            <div class="flex flex-col gap-3 pb-8">
                @foreach ($this->backgrounds as $bg)
                    <div wire:click="selectBackground({{ $bg->id }})" class="gallery-item {{ $backgroundImageId === $bg->id ? 'active' : '' }}"><img src="{{ $bg->getThumbnailUrl() ?: $bg->getFirstMediaUrl('image') }}" class="w-full h-full object-cover"></div>
                @endforeach
            </div>
        </aside>
    </div>

    <script>
        document.addEventListener('livewire:updated', function () {
            const font  = @this.fontFamily ?? 'Inter';
            const link  = document.getElementById('gf-link');
            const url   = `https://fonts.googleapis.com/css2?family=${encodeURIComponent(font)}:wght@400;700;900&display=swap`;
            if (link && link.href !== url) link.href = url;
        });
    </script>
</x-filament-panels::page>
