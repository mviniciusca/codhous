<x-filament-panels::page>
    {{-- Forçar carregamento das fontes do Google --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=Montserrat:wght@400;700;900&family=Oswald:wght@400;700&family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=Poppins:wght@400;700;900&family=Roboto:wght@400;700;900&family=Lato:wght@400;700;900&family=Raleway:wght@400;700;900&family=Ubuntu:wght@400;700&display=swap" rel="stylesheet">

    <style>
        .studio-layout {
            position: fixed;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            display: grid;
            grid-template-columns: 320px 1fr 100px;
            background: #f8fafc;
            z-index: 40;
        }
        .dark .studio-layout { background: #09090b; }

        /* Sidebar Controles */
        .sidebar-controls {
            padding: 30px;
            border-right: 1px solid rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            gap: 25px;
            overflow-y: auto;
            background: #fff;
        }
        .dark .sidebar-controls { background: #111114; border-color: rgba(255,255,255,0.05); }

        /* Área de Preview */
        .preview-area {
            background-color: #f9f9f9;
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 30px 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            overflow: auto;
        }
        .dark .preview-area { 
            background-color: #0f1115;
            background-image: radial-gradient(rgba(255,255,255,0.05) 1px, transparent 1px);
        }

        .card-preview {
            width: 540px;
            height: 540px;
            background-color: #000;
            background-size: cover;
            background-position: center;
            position: relative;
            border-radius: 20px;
            box-shadow: 0 40px 100px -20px rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 50px;
            overflow: hidden;
        }

        .card-overlay {
            position: absolute;
            inset: 0;
            background: var(--overlay-color, #000);
            opacity: var(--overlay-opacity, 0.4);
            z-index: 1;
        }

        .card-text {
            position: relative;
            z-index: 2;
            color: #fff;
            font-size: 42px;
            font-weight: 900;
            line-height: 1.1;
            text-transform: uppercase;
            word-wrap: break-word;
            max-width: 100%;
        }

        /* Sidebar Galeria */
        .sidebar-gallery {
            padding: 15px 10px;
            border-left: 1px solid rgba(0,0,0,0.05);
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            overflow-y: auto;
        }
        .dark .sidebar-gallery { background: #111114; border-color: rgba(255,255,255,0.05); }

        .thumb {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            border: 3px solid transparent;
            transition: 0.2s;
        }
        .thumb.active { border-color: #fbbf24; scale: 1.05; }
        .thumb img { width: 100%; height: 100%; object-fit: cover; }

        .btn-add {
            width: 70px;
            height: 70px;
            background: #fbbf24;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(251, 191, 36, 0.3);
        }

        /* Barra Inferior */
        .bottom-bar {
            position: absolute;
            bottom: 0;
            left: 320px;
            right: 100px;
            background: #fff;
            padding: 15px 30px;
            border-top: 1px solid rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 20px;
            z-index: 100;
        }
        .dark .bottom-bar { background: #111114; border-color: rgba(255,255,255,0.05); }

        .prompt-input {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            font-size: 16px;
            font-weight: 500;
            color: #1e293b;
        }
        .dark .prompt-input { color: #fff; }

        .btn-main {
            background: #fbbf24;
            color: #000;
            padding: 12px 30px;
            border-radius: 100px;
            font-weight: 900;
            border: none;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-main:hover { background: #f59e0b; scale: 1.05; }
    </style>

    <div class="studio-layout">
        {{-- Controles --}}
        <aside class="sidebar-controls">
            <a href="{{ filament()->getUrl() }}" class="flex items-center gap-2 text-[10px] font-black uppercase text-gray-400 hover:text-amber-500">
                <x-heroicon-m-arrow-left class="w-4 h-4" /> Voltar
            </a>

            <div>
                <label class="text-[10px] font-black uppercase text-gray-400 block mb-2">Estilo da Fonte</label>
                <select wire:model.live="fontFamily" class="w-full bg-gray-50 dark:bg-white/5 border-none rounded-xl p-3 text-sm focus:ring-amber-500">
                    @foreach($this->fontOptions as $val => $lbl)
                        <option value="{{ $val }}">{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <div class="flex justify-between items-center mb-2">
                    <label class="text-[10px] font-black uppercase text-gray-400">Tamanho da Fonte</label>
                    <span class="text-[10px] font-black text-amber-500">{{ $fontSize }}px</span>
                </div>
                <input type="range" wire:model.live="fontSize" min="12" max="150" class="w-full accent-amber-500 h-1.5 bg-gray-100 rounded-lg appearance-none cursor-pointer">
            </div>

            <div>
                <label class="text-[10px] font-black uppercase text-gray-400 block mb-2">Filtro de Fundo</label>
                <div class="flex items-center gap-4">
                    <input type="color" wire:model.live="overlayColor" class="w-12 h-12 p-1 bg-white dark:bg-gray-800 rounded-lg cursor-pointer">
                    <input type="range" wire:model.live="overlayOpacity" min="0" max="100" class="flex-1 accent-amber-500">
                </div>
            </div>

            <hr class="border-gray-100 dark:border-white/5">

            <div class="space-y-3" wire:poll.5s>
                <label class="text-[10px] font-black uppercase text-gray-400 block">Artes Recentes</label>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($this->recentPosts as $p)
                        <div class="relative aspect-square rounded-xl overflow-hidden bg-gray-100 dark:bg-white/5 border border-gray-100 dark:border-white/10 group">
                            @if($p->isGenerated())
                                <img src="{{ $p->output_url }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                    <a href="{{ $p->output_url }}" target="_blank" class="p-2 bg-white/20 hover:bg-white/40 rounded-full text-white transition">
                                        <x-heroicon-m-eye class="w-4 h-4" />
                                    </a>
                                    <a href="{{ $p->output_url }}" download class="p-2 bg-amber-500 hover:bg-amber-600 rounded-full text-black transition">
                                        <x-heroicon-m-arrow-down-tray class="w-4 h-4" />
                                    </a>
                                </div>
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center gap-1">
                                    <x-heroicon-o-arrow-path class="w-5 h-5 text-amber-500 animate-spin" />
                                    <span class="text-[8px] font-black uppercase text-gray-400">Gerando</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-auto">
                <button wire:click="dispatchGeneration" class="w-full btn-main flex items-center justify-center gap-2">
                    <x-heroicon-m-sparkles class="w-4 h-4" />
                    GERAR ARTE FINAL
                </button>
            </div>
        </aside>

        {{-- Preview Central --}}
        <main class="preview-area">
            <div class="card-preview" 
                 style="background-image: url('{{ $this->selectedBackgroundUrl }}'); 
                        --overlay-color: {{ $overlayColor }}; 
                        --overlay-opacity: {{ $overlayOpacity / 100 }};
                        align-items: {{ $preset === 'top' ? 'start' : ($preset === 'bottom' ? 'end' : 'center') }};">
                <div class="card-overlay"></div>
                <div class="card-text" style="font-family: '{{ str_replace('+', ' ', $fontFamily) }}' !important; color: {{ $textColor }}; font-size: {{ $fontSize }}px;">
                    {!! nl2br(e($quote ?: 'Escreva algo...')) !!}
                </div>
            </div>

            <div class="bottom-bar">
                <div class="flex bg-gray-100 dark:bg-white/5 p-1 rounded-full gap-1">
                    <button wire:click="selectPreset('top')" title="Topo" class="w-8 h-8 rounded-full flex items-center justify-center {{ $preset === 'top' ? 'bg-amber-500 text-black' : 'text-gray-400' }}">
                        <x-heroicon-m-bars-2 class="w-4 h-4 rotate-180" />
                    </button>
                    <button wire:click="selectPreset('max')" title="Centro" class="w-8 h-8 rounded-full flex items-center justify-center {{ $preset === 'max' ? 'bg-amber-500 text-black' : 'text-gray-400' }}">
                        <x-heroicon-m-pause class="w-4 h-4 rotate-90" />
                    </button>
                    <button wire:click="selectPreset('bottom')" title="Base" class="w-8 h-8 rounded-full flex items-center justify-center {{ $preset === 'bottom' ? 'bg-amber-500 text-black' : 'text-gray-400' }}">
                        <x-heroicon-m-bars-2 class="w-4 h-4" />
                    </button>
                </div>

                <div class="w-px h-8 bg-gray-200 dark:bg-white/10"></div>

                <input type="text" wire:model.live.debounce.300ms="quote" class="prompt-input" placeholder="O que você quer dizer hoje?">
                
                <div class="flex gap-2">
                    <button wire:click="$toggle('isBold')" class="w-8 h-8 rounded-full border {{ $isBold ? 'bg-amber-500 border-amber-500 text-black' : 'border-gray-200 text-gray-400' }}">B</button>
                </div>
            </div>
        </main>

        {{-- Galeria --}}
        <aside class="sidebar-gallery">
            <button wire:click="mountAction('uploadBackground')" type="button" class="btn-add">
                <x-heroicon-o-plus class="w-8 h-8" />
            </button>

            @foreach($this->backgrounds as $bg)
                <div class="thumb {{ $backgroundImageId === $bg->id ? 'active' : '' }}" wire:click="selectBackground({{ $bg->id }})">
                    <img src="{{ $bg->getThumbnailUrl() }}">
                </div>
            @endforeach
        </aside>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>