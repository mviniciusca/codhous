<x-filament-panels::page>
    {{-- Google Fonts for UI and Design --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Montserrat:wght@400;700;900&family=Oswald:wght@400;700&family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=Poppins:wght@400;700;900&family=Roboto:wght@400;700;900&family=Lato:wght@400;700;900&family=Raleway:wght@400;700;900&family=Ubuntu:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 320px;
            --gallery-width: 90px;
            --accent-color: #fbbf24;
            --bg-base: #09090b;
            --bg-surface: #121217;
            --border-subtle: rgba(255, 255, 255, 0.08);
            --text-muted: #a1a1aa;
        }

        .studio-layout {
            position: fixed;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            display: grid;
            grid-template-columns: var(--sidebar-width) 1fr var(--gallery-width);
            background: var(--bg-base);
            color: #fff;
            z-index: 40;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
        }

        /* Sidebar Controles */
        .sidebar-controls {
            padding: 24px;
            border-right: 1px solid var(--border-subtle);
            display: flex;
            flex-direction: column;
            gap: 24px;
            overflow-y: auto;
            background: var(--bg-surface);
            scrollbar-width: none;
        }
        .sidebar-controls::-webkit-scrollbar { display: none; }

        .sidebar-header {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .back-link {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            transition: 0.2s;
        }
        .back-link:hover { color: var(--accent-color); }

        .section-title {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--text-muted);
            letter-spacing: 0.1em;
            margin-bottom: 12px;
            display: block;
        }

        .control-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-subtle);
            border-radius: 16px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            transition: 0.2s;
        }
        .control-card:hover { border-color: rgba(251, 191, 36, 0.2); }

        /* Custom Inputs */
        .studio-select {
            width: 100%;
            background-color: rgba(0, 0, 0, 0.2);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='rgba(255,255,255,0.5)' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            border: 1px solid var(--border-subtle) !important;
            border-radius: 10px !important;
            padding: 10px 36px 10px 12px !important;
            font-size: 13px !important;
            font-weight: 600;
            color: #fff;
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            cursor: pointer;
            transition: 0.2s;
        }
        .studio-select:focus { border-color: var(--accent-color) !important; outline: none; }

        .range-container {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .range-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .range-label { font-size: 11px; font-weight: 600; color: #fff; }
        .range-value { font-size: 11px; font-weight: 700; color: var(--accent-color); }

        .custom-range {
            -webkit-appearance: none;
            width: 100%;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            outline: none;
        }
        .custom-range::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 14px;
            height: 14px;
            background: var(--accent-color);
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 0 10px rgba(251, 191, 36, 0.5);
            transition: 0.2s;
        }
        .custom-range::-webkit-slider-thumb:hover { transform: scale(1.2); }

        /* Patterns & Buttons */
        .pattern-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }
        .btn-toggle {
            aspect-ratio: 1;
            border-radius: 10px;
            border: 1px solid var(--border-subtle);
            background: rgba(255, 255, 255, 0.02);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.3s;
            overflow: hidden;
        }
        .btn-toggle.active { border-color: var(--accent-color); background: rgba(251, 191, 36, 0.1); }
        .btn-toggle:hover:not(.active) { background: rgba(255, 255, 255, 0.05); }

        /* Canvas Area */
        .preview-main {
            position: relative;
            background-color: var(--bg-base);
            background-image: 
                radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 32px 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            overflow: auto;
            scrollbar-width: thin;
        }

        .canvas-container {
            position: relative;
            box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 0.7);
            border-radius: 4px;
            overflow: hidden;
            transition: 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .canvas-box {
            width: 500px;
            height: 500px;
            background-color: #18181b;
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            padding: 48px;
            transition: all 0.4s ease;
        }

        .canvas-overlay {
            position: absolute;
            inset: 0;
            z-index: 1;
        }

        .canvas-pattern {
            position: absolute;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            mask-repeat: repeat;
            -webkit-mask-repeat: repeat;
        }

        .canvas-text {
            position: relative;
            z-index: 2;
            width: 100%;
            word-wrap: break-word;
            transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        /* Bottom Command Bar */
        .command-bar-wrapper {
            position: absolute;
            bottom: 32px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            z-index: 100;
        }

        .preset-strip {
            display: flex;
            gap: 8px;
            background: rgba(18, 18, 23, 0.6);
            backdrop-filter: blur(16px);
            padding: 4px;
            border-radius: 12px;
            border: 1px solid var(--border-subtle);
            opacity: 0;
            transform: translateY(10px);
            transition: 0.3s;
            pointer-events: none;
        }
        .command-bar-wrapper:hover .preset-strip { opacity: 1; transform: translateY(0); pointer-events: auto; }

        .btn-preset-mini {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            background: transparent;
            border: 1px solid transparent;
            transition: 0.2s;
            cursor: pointer;
        }
        .btn-preset-mini.active { background: var(--accent-color); color: #000; }
        .btn-preset-mini:hover:not(.active) { color: #fff; background: rgba(255, 255, 255, 0.05); }

        .command-bar {
            width: 720px;
            height: 64px;
            background: rgba(18, 18, 23, 0.85);
            backdrop-filter: blur(24px);
            border: 1px solid var(--border-subtle);
            border-radius: 32px;
            display: flex;
            align-items: center;
            padding: 0 8px 0 24px;
            gap: 16px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        }

        .prompt-input {
            flex: 1;
            background: transparent !important;
            border: none !important;
            color: #fff !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            outline: none !important;
            padding: 0 !important;
        }
        .prompt-input::placeholder { color: var(--text-muted); }

        .generate-btn {
            background: var(--accent-color);
            color: #000;
            height: 48px;
            padding: 0 24px;
            border-radius: 24px;
            font-weight: 800;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
            border: none;
            box-shadow: 0 8px 20px rgba(251, 191, 36, 0.2);
        }
        .generate-btn:hover { transform: translateY(-2px) scale(1.02); background: #fcd34d; box-shadow: 0 12px 24px rgba(251, 191, 36, 0.3); }
        .generate-btn:active { transform: translateY(0) scale(0.98); }

        /* Recent Arts Section */
        .recent-arts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .art-card {
            position: relative;
            aspect-ratio: 1;
            border-radius: 12px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-subtle);
            transition: 0.3s;
        }
        .art-card:hover { transform: translateY(-4px); border-color: rgba(255, 255, 255, 0.2); }
        
        .art-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            opacity: 0;
            transition: 0.3s;
        }
        .art-card:hover .art-overlay { opacity: 1; }

        /* Right Gallery */
        .sidebar-gallery {
            padding: 24px 12px;
            border-left: 1px solid var(--border-subtle);
            background: var(--bg-surface);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            overflow-y: auto;
            scrollbar-width: none;
        }

        .asset-thumb {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid transparent;
            transition: 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            background: rgba(255, 255, 255, 0.03);
        }
        .asset-thumb img { width: 100%; height: 100%; object-fit: cover; opacity: 0.8; transition: 0.3s; }
        .asset-thumb:hover img { opacity: 1; }
        .asset-thumb.active { border-color: var(--accent-color); transform: scale(1.1); box-shadow: 0 0 20px rgba(251, 191, 36, 0.3); }
        .asset-thumb.active img { opacity: 1; }

        .btn-upload {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px dashed var(--border-subtle);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-upload:hover { background: rgba(255, 255, 255, 0.08); color: #fff; border-color: var(--accent-color); }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.4s ease forwards; }
    </style>

    <div class="studio-layout">
        {{-- Left Sidebar: Tools --}}
        <aside class="sidebar-controls">
            <header class="sidebar-header">
                <a href="{{ filament()->getUrl() }}" class="back-link">
                    <x-heroicon-m-arrow-left class="w-4 h-4" /> Voltar ao Painel
                </a>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center">
                        <x-heroicon-o-sparkles class="w-5 h-5 text-black" />
                    </div>
                    <h2 class="text-lg font-black tracking-tight">Studio Editor</h2>
                </div>
            </header>

            <div class="space-y-8">
                {{-- Typography Card --}}
                <div class="space-y-4">
                    <label class="section-title">Tipografia & Estilo</label>
                    <div class="control-card">
                        <div class="space-y-3">
                            <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Fonte</label>
                            <select wire:model.live="fontFamily" class="studio-select">
                                @foreach($this->fontOptions as $val => $lbl)
                                    <option value="{{ $val }}">{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="range-container">
                            <div class="range-header">
                                <span class="range-label">Tamanho do Texto</span>
                                <span class="range-value">{{ $fontSize }}px</span>
                            </div>
                            <input type="range" wire:model.live="fontSize" min="12" max="150" class="custom-range">
                        </div>

                        <div class="flex items-end gap-3">
                            <div class="flex-1 space-y-2">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Cor</label>
                                <div class="relative group">
                                    <input type="color" wire:model.live="textColor" class="w-full h-10 p-1 bg-zinc-900 border border-zinc-800 rounded-lg cursor-pointer">
                                </div>
                            </div>
                            <button wire:click="$toggle('isBold')" 
                                    class="w-10 h-10 rounded-lg border flex items-center justify-center transition-all duration-300 {{ $isBold ? 'bg-amber-500 border-amber-500 text-black shadow-lg shadow-amber-500/20' : 'border-zinc-800 text-zinc-500 hover:border-zinc-600' }}">
                                <span class="text-sm font-black">B</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Layers & Filters Card --}}
                <div class="space-y-4">
                    <label class="section-title">Filtros & Camadas</label>
                    <div class="control-card">
                        <div class="range-container">
                            <div class="range-header">
                                <span class="range-label">Opacidade do Fundo</span>
                                <span class="range-value">{{ $overlayOpacity }}%</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="color" wire:model.live="overlayColor" class="w-10 h-10 p-1 bg-zinc-900 border border-zinc-800 rounded-lg cursor-pointer flex-shrink-0">
                                <input type="range" wire:model.live="overlayOpacity" min="0" max="100" class="custom-range">
                            </div>
                        </div>

                        <div class="space-y-3 pt-2">
                            <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Textura</label>
                            <div class="pattern-grid">
                                <button wire:click="$set('pattern', null)" class="btn-toggle {{ is_null($pattern) ? 'active' : '' }}" title="Nenhum">
                                    <span class="text-[10px] font-black {{ is_null($pattern) ? 'text-amber-500' : 'text-zinc-600' }}">OFF</span>
                                </button>
                                @foreach(['dots', 'lines', 'grid'] as $p)
                                    <button wire:click="$set('pattern', '{{ $p }}')" class="btn-toggle {{ $pattern === $p ? 'active' : '' }}" title="{{ ucfirst($p) }}">
                                        <img src="/assets/patterns/{{ $p }}.png" class="w-5 h-5 opacity-50 invert">
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        @if($pattern)
                            <div class="range-container animate-fade-in" style="--delay: 0.1s">
                                <div class="range-header">
                                    <span class="range-label">Escala da Textura</span>
                                    <span class="range-value">{{ $patternSize }}px</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <input type="color" wire:model.live="patternColor" class="w-8 h-8 p-1 bg-zinc-900 border border-zinc-800 rounded-lg cursor-pointer">
                                    <input type="range" wire:model.live="patternSize" min="5" max="100" class="custom-range">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Recent Arts --}}
                <div class="space-y-4 pt-2" wire:poll.10s>
                    <div class="flex justify-between items-center">
                        <label class="section-title mb-0">Artes Recentes</label>
                        <span class="text-[9px] font-bold text-amber-500/50 uppercase">Live Update</span>
                    </div>
                    <div class="recent-arts-grid">
                        @foreach($this->recentPosts->where('status', '!=', 'failed') as $p)
                            <div class="art-card">
                                @if($p->isGenerated())
                                    <img src="{{ $p->output_url }}" class="w-full h-full object-cover">
                                    <div class="art-overlay">
                                        <a href="{{ $p->output_url }}" target="_blank" class="p-2 bg-white/10 hover:bg-amber-500 hover:text-black rounded-full transition-all">
                                            <x-heroicon-m-eye class="w-4 h-4" />
                                        </a>
                                        <button wire:click="deletePost({{ $p->id }})" class="p-2 bg-white/10 hover:bg-red-500 text-white rounded-full transition-all">
                                            <x-heroicon-m-trash class="w-4 h-4" />
                                        </button>
                                    </div>
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center gap-2">
                                        <x-heroicon-o-arrow-path class="w-5 h-5 text-amber-500 animate-spin" />
                                        <span class="text-[8px] font-bold text-zinc-600 uppercase">Processando</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main Canvas Area --}}
        <main class="preview-main">
            {{-- Platform Badge removido a pedido --}}

            <div class="canvas-container" id="canvas-wrapper">
                <div class="canvas-box" id="card-container"
                     style="background-image: url('{{ $this->selectedBackgroundUrl }}'); 
                            align-items: {{ $preset === 'top' ? 'start' : ($preset === 'bottom' ? 'end' : 'center') }};
                            justify-content: center;
                            text-align: center;">
                    
                    <div class="canvas-overlay" style="background-color: {{ $overlayColor }}; opacity: {{ $overlayOpacity / 100 }};"></div>
                    
                    @if($pattern)
                        <div class="canvas-pattern" style="
                            mask-image: url('/assets/patterns/{{ $pattern }}.png');
                            -webkit-mask-image: url('/assets/patterns/{{ $pattern }}.png');
                            mask-size: {{ $patternSize }}px;
                            -webkit-mask-size: {{ $patternSize }}px;
                            background-color: {{ $patternColor }};
                            opacity: 0.2;
                        "></div>
                    @endif

                    <div class="canvas-text" style="font-family: '{{ str_replace('+', ' ', $fontFamily) }}', sans-serif !important; color: {{ $textColor }}; font-size: {{ $fontSize }}px; font-weight: {{ $isBold ? '900' : '400' }}; line-height: 1.1; text-transform: uppercase;">
                        {!! nl2br(e($quote ?: '')) !!}
                    </div>
                </div>
            </div>

            {{-- Floating Command Bar Wrapper --}}
            <div class="command-bar-wrapper">
                {{-- Preset Strip --}}
                <div class="preset-strip">
                    @foreach(\App\Enums\CardPreset::cases() as $case)
                        <button wire:click="selectPreset('{{ $case->value }}')" 
                                class="btn-preset-mini {{ $preset === $case->value ? 'active' : '' }}">
                            {{ $case->name }}
                        </button>
                    @endforeach
                </div>

                <div class="command-bar">
                    {{-- Quick Preset Toggles --}}
                    <div class="flex items-center gap-1.5 bg-zinc-950/50 p-1 rounded-full border border-white/5 mr-2">
                        <button wire:click="selectPreset('top')" title="Superior" class="w-8 h-8 rounded-full flex items-center justify-center transition-all {{ $preset === 'top' ? 'bg-amber-500 text-black shadow-lg shadow-amber-500/30' : 'text-zinc-500 hover:text-white' }}">
                            <x-heroicon-m-bars-2 class="w-4 h-4 rotate-180" />
                        </button>
                        <button wire:click="selectPreset('max')" title="Centro" class="w-8 h-8 rounded-full flex items-center justify-center transition-all {{ $preset === 'max' ? 'bg-amber-500 text-black shadow-lg shadow-amber-500/30' : 'text-zinc-500 hover:text-white' }}">
                            <x-heroicon-m-pause class="w-4 h-4 rotate-90" />
                        </button>
                        <button wire:click="selectPreset('bottom')" title="Inferior" class="w-8 h-8 rounded-full flex items-center justify-center transition-all {{ $preset === 'bottom' ? 'bg-amber-500 text-black shadow-lg shadow-amber-500/30' : 'text-zinc-500 hover:text-white' }}">
                            <x-heroicon-m-bars-2 class="w-4 h-4" />
                        </button>
                    </div>

                    <input type="text" 
                           wire:model.live.debounce.300ms="quote" 
                           class="prompt-input" 
                           placeholder="O que você quer expressar hoje?">
                    
                    <button onclick="takeSnapshot()" id="generate-trigger" class="generate-btn">
                        <x-heroicon-m-sparkles class="w-4 h-4" />
                        <span>Gerar Arte</span>
                    </button>
                </div>
            </div>
        </main>

        {{-- Right Sidebar: Asset Gallery --}}
        <aside class="sidebar-gallery">
            <button wire:click="mountAction('uploadBackground')" type="button" class="btn-upload" title="Upload de Imagem">
                <x-heroicon-o-plus class="w-6 h-6" />
            </button>

            <div class="w-full h-px bg-white/5 my-2"></div>

            @foreach($this->backgrounds as $bg)
                <div class="asset-thumb {{ $backgroundImageId === $bg->id ? 'active' : '' }}" 
                     wire:click="selectBackground({{ $bg->id }})"
                     title="{{ $bg->name }}">
                    <img src="{{ $bg->getThumbnailUrl() }}" loading="lazy">
                </div>
            @endforeach
        </aside>
    </div>

    {{-- Capture Script --}}
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script>
        function takeSnapshot() {
            const el = document.getElementById('card-container');
            const btn = document.getElementById('generate-trigger');
            if(!btn) return;

            const originalContent = btn.innerHTML;
            
            // Visual feedback
            btn.classList.add('opacity-50', 'pointer-events-none');
            btn.querySelector('span').innerText = 'Capturando...';
            btn.querySelector('svg').classList.add('animate-spin');

            html2canvas(el, {
                scale: 2, 
                useCORS: true,
                allowTaint: true,
                backgroundColor: null,
                logging: false,
                onclone: (clonedDoc) => {
                    const clonedEl = clonedDoc.getElementById('card-container');
                    if(clonedEl) clonedEl.style.transform = 'none';
                }
            }).then(canvas => {
                const dataUrl = canvas.toDataURL('image/png', 1.0);
                @this.saveSnapshot(dataUrl).then(() => {
                    btn.innerHTML = originalContent;
                    btn.classList.remove('opacity-50', 'pointer-events-none');
                });
            }).catch(err => {
                console.error('Snapshot Error:', err);
                btn.innerHTML = originalContent;
                btn.classList.remove('opacity-50', 'pointer-events-none');
            });
        }

        // Shortcut support
        document.addEventListener('keydown', (e) => {
            if ((e.metaKey || e.ctrlKey) && e.key === 'Enter') {
                takeSnapshot();
            }
        });
    </script>

    <x-filament-actions::modals />
</x-filament-panels::page>