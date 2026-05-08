<x-filament-panels::page>
    {{-- Forçar carregamento das fontes do Google --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=Montserrat:wght@400;700;900&family=Oswald:wght@400;700&family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=Poppins:wght@400;700;900&family=Roboto:wght@400;700;900&family=Lato:wght@400;700;900&family=Raleway:wght@400;700;900&family=Ubuntu:wght@400;700&display=swap" rel="stylesheet">

    <style>        .studio-layout {
            position: fixed;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            display: grid;
            grid-template-columns: 280px 1fr 85px;
            background: #f8fafc;
            z-index: 40;
            font-family: 'Inter', sans-serif;
        }
        .dark .studio-layout { background: #09090b; }

        /* Sidebar Controles */
        .sidebar-controls {
            padding: 15px;
            border-right: 1px solid rgba(0,0,0,0.08);
            display: flex;
            flex-direction: column;
            gap: 15px;
            overflow-y: auto;
            background: #ffffff;
            scrollbar-width: thin;
        }
        .dark .sidebar-controls { background: #111114; border-color: rgba(255,255,255,0.08); }

        .section-title {
            text-[9px] font-black uppercase text-gray-400 tracking-widest mb-2 block;
        }

        .control-group {
            background: #fdfdfd;
            border: 1px solid #f1f5f9;
            padding: 12px;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .dark .control-group { background: #18181b; border-color: rgba(255,255,255,0.05); }

        /* Inputs Modernos - FIX SELECT */
        .studio-select {
            width: 100%;
            background: #f1f5f9;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 8px 30px 8px 12px !important;
            font-size: 12px !important;
            font-weight: 700;
            color: #1e293b;
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='m19.5 8.25-7.5 7.5-7.5-7.5' /%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 10px center !important;
            background-size: 14px !important;
        }
        .dark .studio-select { background-color: #27272a; border-color: #3f3f46 !important; color: #fff; }

        .prompt-input {
            width: 100%;
            background: #f1f5f9;
            border: none !important;
            border-radius: 10px !important;
            padding: 8px 12px !important;
            font-size: 12px !important;
            font-weight: 600;
            color: #1e293b;
        }
        .dark .prompt-input { background: #27272a; color: #fff; }

        /* Patterns Grid */
        .pattern-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
        }
        .pattern-btn {
            aspect-ratio: 1;
            border-radius: 8px;
            border: 2px solid #f1f5f9;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.2s;
            overflow: hidden;
        }
        .pattern-btn.active { border-color: #fbbf24; background: #fffbeb; }
        .dark .pattern-btn { background: #27272a; border-color: #3f3f46; }
        .dark .pattern-btn.active { border-color: #fbbf24; background: #451a03; }

        /* Área de Preview */
        .preview-area {
            position: relative;
            background-color: #f1f5f9;
            background-image: 
                radial-gradient(#cbd5e1 1.5px, transparent 1.5px),
                radial-gradient(#cbd5e1 1.5px, transparent 1.5px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
            overflow: auto;
        }
        .dark .preview-area { 
            background-color: #09090b;
            background-image: radial-gradient(rgba(255,255,255,0.1) 1px, transparent 1px);
        }

        .card-preview {
            width: 500px;
            height: 500px;
            background-color: #1e293b;
            background-size: cover;
            background-position: center;
            position: relative;
            border-radius: 0;
            box-shadow: 0 40px 80px -20px rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .card-overlay {
            position: absolute;
            inset: 0;
            background: var(--overlay-color, #000);
            opacity: var(--overlay-opacity, 0.4);
            z-index: 1;
        }

        .card-pattern {
            position: absolute;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            mask-repeat: repeat;
            -webkit-mask-repeat: repeat;
        }

        .card-text {
            position: relative;
            z-index: 2;
            line-height: 1.1;
            text-transform: uppercase;
            word-wrap: break-word;
            max-width: 100%;
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Sidebar Galeria */
        .sidebar-gallery {
            padding: 15px 10px;
            border-left: 1px solid rgba(0,0,0,0.08);
            background: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            overflow-y: auto;
        }
        .dark .sidebar-gallery { background: #111114; border-color: rgba(255,255,255,0.08); }

        .thumb {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid transparent;
            transition: 0.3s;
            box-shadow: 0 2px 4px -1px rgba(0,0,0,0.1);
        }
        .thumb.active { border-color: #fbbf24; transform: scale(1.05); box-shadow: 0 8px 12px -3px rgba(251, 191, 36, 0.3); }
        .thumb img { width: 100%; height: 100%; object-fit: cover; }

        .btn-add {
            width: 60px;
            height: 60px;
            background: #fbbf24;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(251, 191, 36, 0.3);
            transition: 0.2s;
        }
        .btn-add:hover { transform: translateY(-2px); background: #f59e0b; }

        /* Barra Inferior Fixa no Centro */
        .bottom-bar {
            position: absolute;
            bottom: 25px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            padding: 8px 15px;
            border-radius: 100px;
            border: 1px solid rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 100;
            box-shadow: 0 15px 35px -10px rgba(0,0,0,0.1);
            width: auto;
            min-width: 600px;
        }
        .dark .bottom-bar { background: rgba(24, 24, 27, 0.9); border-color: rgba(255,255,255,0.1); }

        .btn-main {
            background: #1e293b;
            color: #fff;
            padding: 14px 40px;
            border-radius: 100px;
            font-weight: 800;
            font-size: 13px;
            border: none;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 10px 20px -5px rgba(0,0,0,0.2);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-main:hover { background: #0f172a; transform: translateY(-2px); box-shadow: 0 15px 30px -5px rgba(0,0,0,0.3); }
        .btn-main:active { transform: translateY(0); }

        .action-icon-bar {
            position: absolute;
            bottom: 0;
            inset-x: 0;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(8px);
            padding: 8px;
            display: flex;
            justify-content: center;
            gap: 15px;
            opacity: 1;
        }
    </style>

    <div class="studio-layout">
        {{-- Sidebar Esquerda: Controles --}}
        <aside class="sidebar-controls">
            <a href="{{ filament()->getUrl() }}" class="flex items-center gap-2 text-[10px] font-black uppercase text-gray-400 hover:text-amber-500 transition">
                <x-heroicon-m-arrow-left class="w-4 h-4" /> Voltar ao Painel
            </a>

            <div class="space-y-6">
                {{-- Tipografia --}}
                <div class="control-group">
                    <label class="section-title">Tipografia</label>
                    <select wire:model.live="fontFamily" class="studio-select">
                        @foreach($this->fontOptions as $val => $lbl)
                            <option value="{{ $val }}">{{ $lbl }}</option>
                        @endforeach
                    </select>
                    
                    <div>
                        <div class="flex justify-between text-[10px] font-bold text-gray-500 mb-1">
                            <span>Tamanho</span>
                            <span>{{ $fontSize }}px</span>
                        </div>
                        <input type="range" wire:model.live="fontSize" min="12" max="150" class="w-full accent-amber-500 h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="flex-1">
                            <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 block">Cor do Texto</label>
                            <input type="color" wire:model.live="textColor" class="w-full h-8 p-1 bg-white border border-gray-100 rounded-lg cursor-pointer">
                        </div>
                        <button wire:click="$toggle('isBold')" class="w-8 h-8 mt-4 rounded-lg border {{ $isBold ? 'bg-amber-500 border-amber-500 text-black' : 'border-gray-200 text-gray-400' }} font-bold">B</button>
                    </div>
                </div>

                {{-- Camadas --}}
                <div class="control-group">
                    <label class="section-title">Filtro de Fundo</label>
                    <div class="flex items-center gap-3">
                        <input type="color" wire:model.live="overlayColor" class="w-10 h-10 p-1 bg-white border border-gray-100 rounded-lg cursor-pointer">
                        <div class="flex-1">
                            <input type="range" wire:model.live="overlayOpacity" min="0" max="100" class="w-full accent-amber-500 h-1">
                            <div class="flex justify-between text-[8px] font-bold text-gray-400 mt-1">
                                <span>Opacidade</span>
                                <span>{{ $overlayOpacity }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Texturas --}}
                <div class="control-group">
                    <label class="section-title">Textura (Pattern)</label>
                    <div class="flex flex-wrap gap-2">
                        <button wire:click="$set('pattern', null)" class="w-10 h-10 flex-shrink-0 rounded-lg border-2 {{ is_null($pattern) ? 'border-amber-500 bg-amber-50' : 'border-gray-100 bg-white' }} flex items-center justify-center transition" title="Nenhum">
                            <span class="text-[9px] font-black {{ is_null($pattern) ? 'text-amber-600' : 'text-gray-300' }}">OFF</span>
                        </button>
                        @foreach(['dots', 'lines', 'grid'] as $p)
                            <button wire:click="$set('pattern', '{{ $p }}')" class="w-10 h-10 flex-shrink-0 rounded-lg border-2 {{ $pattern === $p ? 'border-amber-500 bg-amber-50' : 'border-gray-100 bg-white' }} flex items-center justify-center transition" title="{{ ucfirst($p) }}">
                                <img src="/assets/patterns/{{ $p }}.png" class="w-5 h-5 opacity-40 invert dark:invert-0">
                            </button>
                        @endforeach
                    </div>
                    
                    @if($pattern)
                        <div class="pt-1 flex items-center gap-2">
                            <input type="color" wire:model.live="patternColor" class="w-6 h-6 p-1 bg-white border border-gray-100 rounded-lg cursor-pointer">
                            <input type="range" wire:model.live="patternSize" min="5" max="100" class="flex-1 accent-amber-500 h-1">
                        </div>
                    @endif
                </div>

                {{-- Galeria Recent --}}
                <div class="space-y-2 pt-1" wire:poll.5s>
                    <label class="section-title">Artes Recentes</label>
                    <div style="display: grid !important; grid-template-columns: 1fr 1fr !important; gap: 8px !important;">
                        @foreach($this->recentPosts->where('status', '!=', 'failed') as $p)
                            <div class="relative aspect-square rounded-lg overflow-hidden bg-gray-100 dark:bg-white/5 border border-gray-100 dark:border-white/10 group shadow-sm transition hover:shadow-md" style="width: 100% !important; aspect-ratio: 1/1 !important;">
                                @if($p->isGenerated())
                                    <img src="{{ $p->output_url }}" class="w-full h-full object-cover" style="display: block !important;">
                                    <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px] flex items-center justify-center gap-3 opacity-0 group-hover:opacity-100 transition duration-200">
                                        <a href="{{ $p->output_url }}" target="_blank" class="text-white hover:text-amber-500 transform hover:scale-125 transition">
                                            <x-heroicon-m-eye class="w-4 h-4" />
                                        </a>
                                        <a href="{{ $p->output_url }}" download class="text-white hover:text-amber-500 transform hover:scale-125 transition">
                                            <x-heroicon-m-arrow-down-tray class="w-4 h-4" />
                                        </a>
                                        <button wire:click="deletePost({{ $p->id }})" class="text-white hover:text-red-500 transform hover:scale-125 transition">
                                            <x-heroicon-m-trash class="w-4 h-4" />
                                        </button>
                                    </div>
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <x-heroicon-o-arrow-path class="w-4 h-4 text-amber-500 animate-spin" />
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Botão Removido daqui --}}
            </div>
        </aside>

        {{-- Área de Preview Central --}}
        <main class="preview-area">
            <div class="card-preview" id="card-container"
                 style="background-image: url('{{ $this->selectedBackgroundUrl }}'); 
                        --overlay-color: {{ $overlayColor }}; 
                        --overlay-opacity: {{ $overlayOpacity / 100 }};
                        align-items: {{ $preset === 'top' ? 'start' : ($preset === 'bottom' ? 'end' : 'center') }};">
                <div class="card-overlay"></div>
                
                @if($pattern)
                    <div class="card-pattern" style="
                        mask-image: url('/assets/patterns/{{ $pattern }}.png');
                        -webkit-mask-image: url('/assets/patterns/{{ $pattern }}.png');
                        mask-size: {{ $patternSize }}px;
                        -webkit-mask-size: {{ $patternSize }}px;
                        background-color: {{ $patternColor }};
                        opacity: 0.2;
                    "></div>
                @endif

                <div class="card-text" style="font-family: '{{ str_replace('+', ' ', $fontFamily) }}' !important; color: {{ $textColor }}; font-size: {{ $fontSize }}px; font-weight: {{ $isBold ? '900' : '400' }};">
                    {!! nl2br(e($quote ?: 'Escreva algo...')) !!}
                </div>
            </div>

            <div class="bottom-bar">
                <div class="flex bg-gray-100 dark:bg-white/10 p-1.5 rounded-full gap-1 border border-gray-200 dark:border-white/5">
                    <button wire:click="selectPreset('top')" title="Topo" class="w-9 h-9 rounded-full flex items-center justify-center transition {{ $preset === 'top' ? 'bg-amber-500 text-black shadow-lg shadow-amber-500/30' : 'text-gray-400 hover:bg-gray-200' }}">
                        <x-heroicon-m-bars-2 class="w-4 h-4 rotate-180" />
                    </button>
                    <button wire:click="selectPreset('max')" title="Centro" class="w-9 h-9 rounded-full flex items-center justify-center transition {{ $preset === 'max' ? 'bg-amber-500 text-black shadow-lg shadow-amber-500/30' : 'text-gray-400 hover:bg-gray-200' }}">
                        <x-heroicon-m-pause class="w-4 h-4 rotate-90" />
                    </button>
                    <button wire:click="selectPreset('bottom')" title="Base" class="w-9 h-9 rounded-full flex items-center justify-center transition {{ $preset === 'bottom' ? 'bg-amber-500 text-black shadow-lg shadow-amber-500/30' : 'text-gray-400 hover:bg-gray-200' }}">
                        <x-heroicon-m-bars-2 class="w-4 h-4" />
                    </button>
                </div>

                <input type="text" wire:model.live.debounce.300ms="quote" class="prompt-input !bg-transparent !py-1" placeholder="O que você quer dizer hoje? Digite aqui...">
                
                <button onclick="takeSnapshot()" class="btn-generate flex items-center gap-2 px-5 py-2.5 rounded-full bg-amber-500 text-black font-black text-[10px] uppercase tracking-tighter hover:bg-amber-400 transition shadow-lg shadow-amber-500/20 group whitespace-nowrap">
                    <x-heroicon-m-sparkles class="w-3.5 h-3.5 group-hover:animate-pulse" />
                    <span>Gerar Arte</span>
                </button>
            </div>
        </main>

        {{-- Sidebar Direita: Galeria de Fundos --}}
        <aside class="sidebar-gallery">
            <button wire:click="mountAction('uploadBackground')" type="button" class="btn-add" title="Upload de Imagem">
                <x-heroicon-o-plus class="w-10 h-10" />
            </button>

            @foreach($this->backgrounds as $bg)
                <div class="thumb {{ $backgroundImageId === $bg->id ? 'active' : '' }}" wire:click="selectBackground({{ $bg->id }})">
                    <img src="{{ $bg->getThumbnailUrl() }}">
                </div>
            @endforeach
        </aside>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script>
        function takeSnapshot() {
            const el = document.getElementById('card-container');
            const btn = event.currentTarget;
            const originalContent = btn.innerHTML;
            
            btn.innerHTML = 'CAPTURANDO...';
            btn.disabled = true;

            html2canvas(el, {
                scale: 2, 
                useCORS: true,
                allowTaint: true,
                backgroundColor: null,
                logging: false
            }).then(canvas => {
                const dataUrl = canvas.toDataURL('image/png');
                @this.saveSnapshot(dataUrl).then(() => {
                    btn.innerHTML = originalContent;
                    btn.disabled = false;
                });
            }).catch(err => {
                console.error('Erro na captura:', err);
                btn.innerHTML = originalContent;
                btn.disabled = false;
            });
        }
    </script>

    <x-filament-actions::modals />
</x-filament-panels::page>