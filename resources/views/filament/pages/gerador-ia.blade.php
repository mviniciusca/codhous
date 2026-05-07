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
        .preset-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .preset-btn {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }
        .dark .preset-btn { background: #1e293b; border-color: rgba(255,255,255,0.05); }
        .preset-btn.active { border-color: #fbbf24; background: #fffcf0; }
        .dark .preset-btn.active { background: #2d2613; border-color: #fbbf24; }
        .preset-icon-box {
            height: 30px;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0,0,0,0.03);
            border-radius: 4px;
        }
        .preset-btn span { font-size: 10px; font-weight: bold; color: #64748b; }
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

        /* Preset 1: TOP_LEFT (Playlist) */
        .preset-top_left .inner-image { margin: 40px; border: 15px solid white; height: calc(100% - 80px); }
        .preset-top_left .deco-block { position: absolute; top: 20px; right: 20px; width: 60px; height: 60px; background: var(--highlight); border-radius: 50%; }

        /* Preset 2: TOP_CENTER (Chill Split) */
        .preset-top_center .split-bg { position: absolute; top: 0; left: 0; width: 50%; height: 100%; background: var(--highlight); }
        .preset-top_center .stripes { position: absolute; top: 10px; right: 10px; width: 80px; height: 40px; background: repeating-linear-gradient(45deg, #000, #000 5px, transparent 5px, transparent 10px); opacity: 0.3; }

        /* Preset 5: BOTTOM_CENTER (Horizontal Split) */
        .preset-bottom_center .split-bg-h { position: absolute; bottom: 0; left: 0; width: 100%; height: 50%; background: var(--highlight); }

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
    </style>

    <div class="studio-container rounded-b-2xl" style="--highlight: {{ $overlayColor }};">
        
        {{-- Sidebar Esquerda: Ajustes --}}
        <aside class="studio-sidebar-left">
            <div class="flex items-center gap-2 mb-2 text-gray-400 dark:text-white/40 text-[10px] uppercase tracking-widest font-black">
                <x-heroicon-o-adjustments-horizontal class="w-4 h-4" />
                <span>Propriedades</span>
            </div>

            <div class="space-y-5">
                {{-- Presets --}}
                <div class="space-y-2">
                    <label class="text-[10px] text-gray-500 uppercase font-bold">Layout / Presets</label>
                    <div class="preset-grid">
                        @foreach($this->presetOptions as $val => $lbl)
                            <div wire:click="selectPreset('{{ $val }}')" 
                                 class="preset-btn {{ $preset === $val ? 'active' : '' }}">
                                <div class="preset-icon-box">
                                    @if($val === 'top_left') <x-heroicon-o-square-3-stack-3d class="w-4 h-4" /> @endif
                                    @if($val === 'top_center') <x-heroicon-o-view-columns class="w-4 h-4" /> @endif
                                    @if($val === 'top_right') <x-heroicon-o-swatch class="w-4 h-4" /> @endif
                                    @if($val === 'bottom_left') <x-heroicon-o-stop class="w-4 h-4" /> @endif
                                    @if($val === 'bottom_center') <x-heroicon-o-rectangle-stack class="w-4 h-4" /> @endif
                                    @if($val === 'bottom_right') <x-heroicon-o-photo class="w-4 h-4" /> @endif
                                </div>
                                <span>{{ $lbl }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] text-gray-500 uppercase font-bold">Título do Post</label>
                    <input type="text" wire:model.live.debounce.300ms="postTitle" class="studio-input">
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
                        <label class="text-[10px] text-gray-500 uppercase font-bold">Destaque</label>
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
                    <label class="text-[10px] text-gray-400 uppercase tracking-widest font-black">Histórico</label>
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
            
            <div class="preview-wrapper preset-{{ $preset }}"
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

                {{-- Preset Elements --}}
                @if($preset === 'top_center') <div class="split-bg"></div> <div class="stripes"></div> @endif
                @if($preset === 'top_left') <div class="deco-block"></div> @endif
                @if($preset === 'bottom_center') <div class="split-bg-h"></div> @endif

                {{-- Global Overlay --}}
                <div class="absolute inset-0 transition-all duration-300" style="background: {{ $this->overlayCss }};"></div>

                {{-- Text Content --}}
                <div class="absolute inset-0 flex items-center justify-center p-12 text-center">
                    <div style="font-family: '{{ str_replace('+', ' ', $fontFamily) }}', sans-serif; color: {{ $textColor }}; z-index: 10;">
                        @if(trim($quote))
                            <span class="block text-4xl font-black leading-none opacity-40 mb-4">&ldquo;</span>
                            <p class="font-bold leading-tight" style="font-size: 26px; text-shadow: 0 4px 30px rgba(0,0,0,0.6);">{{ $quote }}</p>
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
