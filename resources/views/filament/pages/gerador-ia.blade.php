<x-filament-panels::page>
    {{-- Konva.js for Professional Design Engine --}}
    <script src="https://unpkg.com/konva@9/konva.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
    {{-- Pré-carregar fontes para o preview --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&family=Poppins:wght@400;700;900&family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400;1,700;1,900&family=Inter:wght@400;700;900&display=swap"
        rel="stylesheet">

    <style>
        /* Container Principal */
        .studio-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            display: grid;
            grid-template-columns: 260px 1fr 100px;
            gap: 0;
            background: #ffffff;
            overflow: hidden;
            z-index: 9999;
            /* Fica acima de tudo no Filament */
        }

        .dark .studio-container {
            background: #09090b;
        }

        /* Sidebar Esquerda */
        .studio-sidebar-left {
            background: #f8fafc;
            border-right: 1px solid rgba(0, 0, 0, 0.05);
            padding: 24px 20px;
            display: flex;
            flex-direction: column;
            gap: 24px;
            overflow-y: auto;
            z-index: 10;
        }

        .dark .studio-sidebar-left {
            background: #111114;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Canvas Central */
        .studio-canvas {
            flex: 1;
            display: flex;
            flex-direction: column;
            position: relative;
            background-color: #f1f5f9;
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 30px 30px;
            overflow: hidden;
            /* O container do canvas não rola, quem rola é a área interna */
        }

        .studio-canvas-scroll {
            flex: 1;
            overflow: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .dark .studio-canvas {
            background-color: #0f1115;
            background-image: radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
        }

        /* Sidebar Direita */
        .studio-sidebar-right {
            background: #f8fafc;
            border-left: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 16px 8px;
            gap: 12px;
            overflow-y: auto;
        }

        .dark .studio-sidebar-right {
            background: #111114;
            border-left: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Inputs e Selects Customizados */
        .studio-label {
            display: block;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 800;
            color: #64748b;
            margin-bottom: 6px;
            letter-spacing: 0.05em;
        }

        .studio-input-wrap {
            width: 100%;
            position: relative;
        }

        .studio-input {
            width: 100%;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 13px;
            color: #1e293b;
            padding: 10px 14px;
            outline: none !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            transition: all 0.2s;
        }

        .dark .studio-input {
            background: #1e293b;
            border-color: rgba(255, 255, 255, 0.05);
            color: #f1f5f9;
        }

        select.studio-input {
            appearance: none !important;
            padding-right: 32px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5' /%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 14px;
        }

        /* Preset Grid */
        .preset-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .preset-btn {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dark .preset-btn {
            background: #1e293b;
            border-color: rgba(255, 255, 255, 0.05);
        }

        .preset-btn:hover {
            border-color: #fbbf24;
            transform: translateY(-1px);
        }

        .preset-btn.active {
            border-color: #fbbf24;
            background: #fffcf0;
            box-shadow: 0 4px 12px rgba(251, 191, 36, 0.15);
        }

        .dark .preset-btn.active {
            background: #2d2613;
            border-color: #fbbf24;
        }

        .preset-btn span {
            display: block;
            font-size: 10px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
        }

        .preset-btn.active span {
            color: #b45309;
        }

        .dark .preset-btn.active span {
            color: #fbbf24;
        }

        /* Drag & Drop Canvas */
        .preview-wrapper {
            box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.3);
            border-radius: 16px;
            overflow: hidden;
            background: #000;
            position: relative;
            user-select: none;
        }

        .draggable-text {
            position: absolute;
            cursor: move;
            user-select: none;
            touch-action: none;
            z-index: 100;
            padding: 16px;
            border: 2px dashed transparent;
            transition: border-color 0.2s, transform 0.1s;
            max-width: 100%;
            width: fit-content;
            will-change: transform, left, top;
        }

        .draggable-text:hover {
            border-color: rgba(251, 191, 36, 0.3);
        }

        .draggable-text.is-dragging {
            border-color: #fbbf24;
            opacity: 0.9;
            scale: 1.05;
            z-index: 1000;
        }

        .style-max .quote-text {
            text-transform: uppercase;
            line-height: 0.9;
        }

        .style-max .title-text {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.3em;
            margin-bottom: 8px;
            opacity: 0.7;
        }

        .style-flux .quote-text {
            letter-spacing: -0.02em;
        }

        .style-flux .title-text {
            font-size: 12px;
            letter-spacing: 0.5em;
            margin-bottom: 24px;
            opacity: 0.6;
        }

        .style-canva_side .side-block {
            position: absolute;
            left: 0;
            top: 0;
            width: 45%;
            height: 100%;
            background: var(--highlight);
            z-index: 5;
        }

        /* Toolbar / Prompt Bar */
        /* Barra de Prompt Fixa no Fundo */
        .studio-prompt-bar {
            background: white;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 30;
            box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.03);
            width: 100%;
        }

        .dark .studio-prompt-bar {
            background: #111114;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .studio-prompt-input {
            flex: 1;
            background: transparent;
            border: none !important;
            box-shadow: none !important;
            color: #1e293b;
            font-size: 15px;
            font-weight: 500;
            padding: 8px 0;
            outline: none !important;
        }

        .dark .studio-prompt-input {
            color: #f1f5f9;
        }

        .btn-generate {
            background: #fbbf24;
            color: #000000;
            padding: 12px 32px;
            border-radius: 100px;
            font-size: 13px;
            font-weight: 900;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3);
            border: none;
            cursor: pointer;
        }

        .btn-generate:hover {
            background: #f59e0b;
            transform: scale(1.02);
        }

        /* Formatação Toolbar */
        .format-group {
            display: flex;
            align-items: center;
            background: rgba(0, 0, 0, 0.03);
            padding: 4px;
            border-radius: 100px;
            gap: 2px;
        }

        .dark .format-group {
            background: rgba(255, 255, 255, 0.05);
        }

        .format-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: #64748b;
            transition: all 0.2s;
            cursor: pointer;
        }

        .dark .format-btn {
            color: #94a3b8;
        }

        .format-btn:hover {
            background: rgba(0, 0, 0, 0.05);
            color: #1e293b;
        }

        .dark .format-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .format-btn.active {
            background: #fbbf24;
            color: #000;
        }

        /* Galeria */
        .gallery-item {
            width: 68px;
            height: 68px;
            border-radius: 14px;
            overflow: hidden;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            background: #e2e8f0;
        }

        .gallery-item.active {
            border-color: #fbbf24;
            box-shadow: 0 8px 16px -4px rgba(251, 191, 36, 0.3);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* FORÇA BRUTA LIXEIRA */
        .trash-btn {
            position: absolute !important;
            top: -5px !important;
            right: -5px !important;
            width: 24px !important;
            height: 24px !important;
            background-color: #ef4444 !important;
            /* Vermelho Vivo */
            color: white !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            z-index: 999 !important;
            border: 2px solid white !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3) !important;
            cursor: pointer !important;
            opacity: 0 !important;
            /* Escondido por padrão */
            transition: opacity 0.2s ease !important;
        }

        .gallery-item-wrapper:hover .trash-btn {
            opacity: 1 !important;
            /* Aparece no hover */
        }

        /* Patterns CSS */
        .pattern-dots {
            background-image: radial-gradient(currentColor 1px, transparent 1px);
            background-size: var(--pattern-size, 10px) var(--pattern-size, 10px);
        }

        .pattern-lines {
            background-image: repeating-linear-gradient(45deg, currentColor 0, currentColor 1px, transparent 0, transparent 50%);
            background-size: var(--pattern-size, 10px) var(--pattern-size, 10px);
        }

        .pattern-grid {
            background-image: linear-gradient(currentColor 1px, transparent 1px), linear-gradient(90deg, currentColor 1px, transparent 1px);
            background-size: var(--pattern-size, 20px) var(--pattern-size, 20px);
        }

        .pattern-noise {
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
            opacity: 0.15 !important;
        }
    </style>

    <div class="studio-container" x-data="studio" style="--highlight: {{ $overlayColor }};">

        {{-- Sidebar Esquerda --}}
        <aside class="studio-sidebar-left">
            <div class="space-y-6">
                {{-- Botão Voltar --}}
                <div class="mb-4">
                    <a href="{{ filament()->getUrl() }}"
                        class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-amber-500 transition-colors group">
                        <x-heroicon-m-arrow-left class="w-4 h-4 group-hover:-translate-x-1 transition-transform" />
                        Voltar ao Painel
                    </a>
                </div>

                <div>
                    <label class="studio-label">Design Style</label>
                    <div class="preset-grid">
                        @foreach($this->presetOptions as $val => $lbl)
                            <div wire:click="selectPreset('{{ $val }}')"
                                class="preset-btn {{ $preset === $val ? 'active' : '' }}">
                                <span>{{ str_replace('Estilo ', '', $lbl) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="studio-input-wrap">
                    <label class="studio-label">Título / Label</label>
                    <input type="text" wire:model.live.debounce.300ms="postTitle" class="studio-input"
                        placeholder="Ex: NOVIDADE">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="studio-input-wrap">
                        <label class="studio-label">Formato</label>
                        <select wire:model.live="platform" class="studio-input">
                            @foreach ($this->platformOptions as $v => $o) <option value="{{ $v }}">{{ $o['label'] }}
                            </option> @endforeach
                        </select>
                    </div>
                    <div class="studio-input-wrap">
                        <label class="studio-label">Fonte</label>
                        <select wire:model.live="fontFamily" class="studio-input">
                            @foreach ($this->fontOptions as $v => $l) <option value="{{ $v }}">{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="studio-input-wrap">
                    <label class="studio-label">Padrão Visual</label>
                    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 6px; margin-bottom: 8px;">
                        <button wire:click="$set('pattern', null)"
                            class="aspect-square rounded border flex items-center justify-center transition-all {{ is_null($pattern) ? 'border-amber-500 bg-amber-50 dark:bg-amber-500/10' : 'border-gray-200 dark:border-white/10' }}">
                            <x-heroicon-m-x-mark class="w-4 h-4 text-gray-400" />
                        </button>
                        @foreach($this->patternOptions as $key => $label)
                            <button wire:click="$set('pattern', '{{ $key }}')" title="{{ $label }}"
                                class="aspect-square rounded border transition-all flex items-center justify-center overflow-hidden {{ $pattern === $key ? 'border-amber-500 ring-1 ring-amber-500' : 'border-gray-200 dark:border-white/10' }}">
                                <div class="w-full h-full pattern-{{ $key }} opacity-40 scale-150"
                                    style="color: {{ $patternColor }}; --pattern-size: 8px;"></div>
                            </button>
                        @endforeach
                    </div>

                    @if($pattern)
                        <div class="grid grid-cols-2 gap-3 animate-in fade-in slide-in-from-top-1 duration-200">
                            <div class="space-y-1">
                                <label class="text-[9px] uppercase font-black text-gray-400">Cor</label>
                                <input type="color" wire:model.live="patternColor"
                                    class="w-full h-7 p-0.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-white/10 rounded cursor-pointer">
                            </div>
                            <div class="space-y-1">
                                <div class="flex justify-between items-center">
                                    <label class="text-[9px] uppercase font-black text-gray-400">Escala</label>
                                    <span class="text-[9px] font-black text-amber-500">{{ $patternSize }}</span>
                                </div>
                                <input type="range" wire:model.live="patternSize" min="4" max="80" step="1"
                                    class="w-full h-1 bg-gray-200 dark:bg-white/10 rounded-lg appearance-none cursor-pointer accent-amber-500">
                            </div>
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="studio-input-wrap">
                        <label class="studio-label">Cor do Texto</label>
                        <input type="color" wire:model.live="textColor"
                            class="w-full h-10 p-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-white/10 rounded-lg cursor-pointer">
                    </div>
                    <div class="studio-input-wrap">
                        <label class="studio-label">Highlight / Filtro</label>
                        <input type="color" wire:model.live="overlayColor"
                            class="w-full h-10 p-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-white/10 rounded-lg cursor-pointer">
                    </div>
                </div>

                <div class="studio-input-wrap">
                    <div class="flex justify-between items-center mb-1">
                        <label class="studio-label !mb-0">Tamanho da Fonte</label>
                        <span class="text-[11px] text-amber-500 font-black">{{ $fontSize }}px</span>
                    </div>
                    <input type="range" wire:model.live="fontSize" min="12" max="150"
                        class="w-full accent-amber-500 h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700">
                </div>

                <div class="studio-input-wrap">
                    <div class="flex justify-between items-center mb-1">
                        <label class="studio-label !mb-0">Opacidade Filtro</label>
                        <span class="text-[11px] text-amber-500 font-black">{{ $overlayOpacity }}%</span>
                    </div>
                    <input type="range" wire:model.live="overlayOpacity" min="0" max="100"
                        class="w-full accent-amber-500 h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700">
                </div>

                <hr class="border-gray-200 dark:border-white/5">

                <div class="space-y-3" wire:poll.5s>
                    <label class="studio-label">Artes Recentes</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($this->recentPosts as $p)
                            <div
                                class="relative aspect-square rounded-xl overflow-hidden bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 group shadow-sm">
                                @if($p->isGenerated())
                                    <img src="{{ $p->output_url }}" class="w-full h-full object-cover">

                                    {{-- Barra de Ações (Sempre Visível no fundo da miniatura) --}}
                                    <div
                                        class="absolute bottom-0 inset-x-0 bg-black/40 backdrop-blur-md p-1.5 flex items-center justify-center gap-2">
                                        <a href="{{ $p->output_url }}" target="_blank" title="Visualizar"
                                            class="p-1 hover:text-amber-500 transition-colors text-white">
                                            <x-heroicon-m-eye class="w-3.5 h-3.5" />
                                        </a>
                                        <div class="w-px h-3 bg-white/20"></div>
                                        <a href="{{ $p->output_url }}" download="arte-{{ $p->id }}.png" title="Baixar"
                                            class="p-1 hover:text-amber-500 transition-colors text-white">
                                            <x-heroicon-m-arrow-down-tray class="w-3.5 h-3.5" />
                                        </a>
                                        <div class="w-px h-3 bg-white/20"></div>
                                        <button wire:click="deletePost({{ $p->id }})"
                                            wire:confirm="Tem certeza que deseja apagar esta arte?" title="Deletar"
                                            class="p-1 hover:text-red-500 transition-colors text-white">
                                            <x-heroicon-m-trash class="w-3.5 h-3.5" />
                                        </button>
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
            </div>
        </aside>

        {{-- Coluna Central --}}
        <main class="studio-canvas">
            <div class="studio-canvas-scroll">
                <div class="preview-wrapper style-{{ $preset }}" id="stage-parent"
                    wire:ignore
                    style="width: 540px; aspect-ratio: {{ $platform === 'story' ? '9/16' : ($platform === 'facebook' || $platform === 'linkedin' ? '1.91/1' : '1/1') }}; max-height: 85%; overflow: hidden; position: relative;">
                    <div id="konva-stage"></div>
                </div>
            </div>

            {{-- Prompt Bar & Toolbar (Fixa no rodapé) --}}
            <div class="studio-prompt-bar">

                {{-- Text Style Toolbar --}}
                <div class="format-group">
                    <div wire:click="$toggle('isBold')" class="format-btn {{ $isBold ? 'active' : '' }}"><span
                            class="font-black text-xs">B</span></div>
                    <div wire:click="$toggle('isItalic')" class="format-btn {{ $isItalic ? 'active' : '' }}"><span
                            class="italic font-serif text-xs">I</span></div>
                </div>

                <div class="w-px h-8 bg-gray-200 dark:bg-white/10 mx-1"></div>

                <input type="text" wire:model.live.debounce.300ms="quote" placeholder="Sua frase de impacto..."
                    class="studio-prompt-input">

                <button wire:click="dispatchGeneration" wire:loading.attr="disabled" wire:target="dispatchGeneration"
                    class="btn-generate">
                    <span wire:loading.remove wire:target="dispatchGeneration">GERAR ARTE</span>
                    <x-heroicon-o-arrow-path wire:loading wire:target="dispatchGeneration"
                        class="w-4 h-4 animate-spin" />
                </button>
            </div>
        </main>

        {{-- Sidebar Direita: Galeria Vertical --}}
        <aside class="studio-sidebar-right">
            <div class="flex flex-col gap-4 pb-8">
                {{-- Botão de Upload Customizado --}}
                <button wire:click="mountAction('uploadBackground')" type="button"
                    class="gallery-item flex items-center justify-center bg-amber-500 border-none hover:bg-amber-600 transition-colors shadow-lg group">
                    <x-heroicon-o-plus class="w-8 h-8 text-black group-hover:scale-110 transition-transform" />
                </button>

                @foreach ($this->backgrounds as $bg)
                    <div class="gallery-item-wrapper relative" style="width: 68px; height: 68px; margin-bottom: 12px;">
                        <div class="gallery-item {{ $backgroundImageId === $bg->id ? 'active' : '' }}"
                            style="width: 100%; height: 100%; border-radius: 14px; overflow: hidden; position: relative;">
                            <img src="{{ $bg->getThumbnailUrl() }}" wire:click="selectBackground({{ $bg->id }})"
                                class="w-full h-full object-cover cursor-pointer">
                        </div>

                        {{-- Botão de Lixeira (SVG DIRETO E CSS IMPORTANTE) --}}
                        <button wire:click="deleteBackground({{ $bg->id }})"
                            wire:confirm="Tem certeza que deseja apagar esta imagem?" type="button" class="trash-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                style="width: 14px; height: 14px;">
                                <path fill-rule="evenodd"
                                    d="M8.75 3A2.75 2.75 0 0 0 6 5.75v.562c-.34.059-.68.124-1.022.196a.75.75 0 1 0 .312 1.468h.011l.83 7.06c.066.566.547 1.001 1.116 1.001h5.49c.568 0 1.05-.435 1.115-1.001l.83-7.06h.013a.75.75 0 1 0 .312-1.468 48.707 48.707 0 0 0-1.022-.195V5.75A2.75 2.75 0 0 0 11.25 3h-2.5ZM10 7.5a.75.75 0 0 1 .75.75v5.25a.75.75 0 0 1-1.5 0V8.25A.75.75 0 0 1 10 7.5ZM7.25 8.25a.75.75 0 0 0-1.5 0v5.25a.75.75 0 0 0 1.5 0V8.25Zm6 0a.75.75 0 0 0-1.5 0v5.25a.75.75 0 0 0 1.5 0V8.25Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
        </aside>
    </div>

    <x-filament-actions::modals />

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('studio', () => ({
                stage: null,
                layer: null,
                transformer: null,
                bgImg: null,
                overlay: null,
                textNode: null,

                init() {
                    console.log('Iniciando Konva Engine...');
                    const container = document.getElementById('stage-parent');
                    if (!container) {
                        console.error('Container stage-parent não encontrado!');
                        return;
                    }

                    // Forçar dimensões
                    const width = 540;
                    const height = container.offsetHeight || 540;

                    this.stage = new Konva.Stage({
                        container: 'konva-stage',
                        width: width,
                        height: height
                    });

                    this.layer = new Konva.Layer();
                    this.stage.add(this.layer);

                    // 1. Fundo
                    this.bgImg = new Konva.Image({ x: 0, y: 0, width: width, height: height });
                    this.layer.add(this.bgImg);

                    // 2. Filtro de Sobreposição (Usando Rect com Opacidade)
                    this.overlay = new Konva.Rect({
                        x: 0, y: 0, width: width, height: height,
                        fill: '#000', opacity: 0.4,
                        listening: false // Não atrapalha o clique no texto
                    });
                    this.layer.add(this.overlay);

                    // 3. Texto Profissional
                    this.textNode = new Konva.Text({
                        text: 'Digite sua frase...',
                        x: width / 2,
                        y: height / 2,
                        fontSize: 42,
                        fontFamily: 'Inter',
                        fill: '#ffffff',
                        align: 'center',
                        width: width * 0.8,
                        draggable: true,
                        name: 'target-text'
                    });
                    this.layer.add(this.textNode);

                    // 4. Transformer (O segredo do Canva: alças de redimensionamento)
                    this.transformer = new Konva.Transformer({
                        nodes: [this.textNode],
                        enabledAnchors: ['top-left', 'top-right', 'bottom-left', 'bottom-right'],
                        boundBoxFunc: (oldBox, newBox) => {
                            if (Math.abs(newBox.width) < 50 || Math.abs(newBox.height) < 20) return oldBox;
                            return newBox;
                        }
                    });
                    this.layer.add(this.transformer);

                    // Sincronizar com o Backend ao soltar o mouse
                    this.textNode.on('dragend transformend', () => {
                        const x = (this.textNode.x() / width) * 100;
                        const y = (this.textNode.y() / height) * 100;
                        @this.updateCoordinates(x, y);
                    });

                    // Eventos de clique para selecionar o texto
                    this.stage.on('click tap', (e) => {
                        if (e.target === this.stage || e.target === this.bgImg) {
                            this.transformer.nodes([]);
                        } else {
                            this.transformer.nodes([this.textNode]);
                        }
                        this.layer.draw();
                    });

                    this.updateAll();
                    
                    Livewire.on('updated', () => {
                        console.log('Livewire atualizado, redesenhando...');
                        this.updateAll();
                    });
                },

                updateAll() {
                    if (!this.stage) return;

                    const quote = @this.get('quote') || 'Escreva algo...';
                    const textColor = @this.get('textColor');
                    const fontSize = @this.get('fontSize');
                    const fontFamily = @this.get('fontFamily');
                    const textAlign = @this.get('textAlign');
                    const isBold = @this.get('isBold');

                    this.textNode.setAttrs({
                        text: quote,
                        fill: textColor,
                        fontSize: parseInt(fontSize),
                        fontFamily: fontFamily,
                        fontStyle: isBold ? 'bold' : 'normal',
                        align: textAlign
                    });

                    // Centralização inteligente
                    if (textAlign === 'left') this.textNode.offsetX(0);
                    else if (textAlign === 'right') this.textNode.offsetX(this.textNode.width());
                    else this.textNode.offsetX(this.textNode.width() / 2);
                    this.textNode.offsetY(this.textNode.height() / 2);

                    // Atualizar Filtro
                    this.overlay.fill(@this.get('overlayColor'));
                    this.overlay.opacity(@this.get('overlayOpacity') / 100);

                    // Carregar Imagem de Fundo (Melhorado)
                    const bgUrl = @this.get('selectedBackgroundUrl');
                    console.log('Tentando carregar fundo:', bgUrl);
                    
                    if (bgUrl) {
                        const img = new Image();
                        img.crossOrigin = 'anonymous';
                        img.onload = () => {
                            console.log('Imagem carregada com sucesso!');
                            this.bgImg.image(img);
                            
                            const stageRatio = this.stage.width() / this.stage.height();
                            const imgRatio = img.width / img.height;
                            
                            if (imgRatio > stageRatio) {
                                this.bgImg.height(this.stage.height());
                                this.bgImg.width(this.stage.height() * imgRatio);
                            } else {
                                this.bgImg.width(this.stage.width());
                                this.bgImg.height(this.stage.width() / imgRatio);
                            }
                            
                            this.bgImg.x((this.stage.width() - this.bgImg.width()) / 2);
                            this.bgImg.y((this.stage.height() - this.bgImg.height()) / 2);
                            
                            this.layer.batchDraw();
                        };
                        img.onerror = () => console.error('Erro ao carregar a imagem de fundo em:', bgUrl);
                        img.src = bgUrl;
                    } else {
                        console.warn('Nenhuma URL de fundo selecionada.');
                        this.bgImg.image(null);
                        this.layer.batchDraw();
                    }

                    this.layer.batchDraw();
                }
            }));
        });
    </script>
</x-filament-panels::page>