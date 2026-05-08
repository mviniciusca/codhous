<x-filament-panels::page>
    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
    {{-- Pré-carregar fontes para o preview --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&family=Poppins:wght@400;700;900&family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400;1,700;1,900&family=Inter:wght@400;700;900&display=swap" rel="stylesheet">

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
            z-index: 9999; /* Fica acima de tudo no Filament */
        }

        .dark .studio-container { background: #09090b; }

        /* Sidebar Esquerda */
        .studio-sidebar-left {
            background: #f8fafc;
            border-right: 1px solid rgba(0,0,0,0.05);
            padding: 24px 20px;
            display: flex;
            flex-direction: column;
            gap: 24px;
            overflow-y: auto;
            z-index: 10;
        }

        .dark .studio-sidebar-left { background: #111114; border-right: 1px solid rgba(255,255,255,0.05); }

        /* Canvas Central */
        .studio-canvas {
            flex: 1;
            display: flex;
            flex-direction: column;
            position: relative;
            background-color: #f1f5f9;
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 30px 30px;
            overflow: hidden; /* O container do canvas não rola, quem rola é a área interna */
        }

        .studio-canvas-scroll {
            flex: 1;
            overflow: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .dark .studio-canvas { background-color: #0f1115; background-image: radial-gradient(rgba(255,255,255,0.05) 1px, transparent 1px); }

        /* Sidebar Direita */
        .studio-sidebar-right {
            background: #f8fafc;
            border-left: 1px solid rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 16px 8px;
            gap: 12px;
            overflow-y: auto;
        }

        .dark .studio-sidebar-right { background: #111114; border-left: 1px solid rgba(255,255,255,0.05); }

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

        .studio-input-wrap { width: 100%; position: relative; }

        .studio-input {
            width: 100%;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 13px;
            color: #1e293b;
            padding: 10px 14px;
            outline: none !important;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            transition: all 0.2s;
        }

        .dark .studio-input { background: #1e293b; border-color: rgba(255,255,255,0.05); color: #f1f5f9; }

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

        .dark .preset-btn { background: #1e293b; border-color: rgba(255,255,255,0.05); }

        .preset-btn:hover { border-color: #fbbf24; transform: translateY(-1px); }
        .preset-btn.active { border-color: #fbbf24; background: #fffcf0; box-shadow: 0 4px 12px rgba(251, 191, 36, 0.15); }
        .dark .preset-btn.active { background: #2d2613; border-color: #fbbf24; }

        .preset-btn span {
            display: block;
            font-size: 10px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
        }

        .preset-btn.active span { color: #b45309; }
        .dark .preset-btn.active span { color: #fbbf24; }

        /* Drag & Drop Canvas */
        .preview-wrapper {
            box-shadow: 0 40px 80px -20px rgba(0,0,0,0.3);
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

        .draggable-text:hover { border-color: rgba(251, 191, 36, 0.3); }
        .draggable-text.is-dragging { border-color: #fbbf24; opacity: 0.9; scale: 1.05; z-index: 1000; }

        .style-max .quote-text { text-transform: uppercase; line-height: 0.9; }
        .style-max .title-text { font-size: 14px; text-transform: uppercase; letter-spacing: 0.3em; margin-bottom: 8px; opacity: 0.7; }

        .style-flux .quote-text { letter-spacing: -0.02em; }
        .style-flux .title-text { font-size: 12px; letter-spacing: 0.5em; margin-bottom: 24px; opacity: 0.6; }

        .style-canva_side .side-block { position: absolute; left: 0; top: 0; width: 45%; height: 100%; background: var(--highlight); z-index: 5; }

        /* Toolbar / Prompt Bar */
        /* Barra de Prompt Fixa no Fundo */
        .studio-prompt-bar {
            background: white;
            border-top: 1px solid rgba(0,0,0,0.05);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 30;
            box-shadow: 0 -4px 15px rgba(0,0,0,0.03);
            width: 100%;
        }

        .dark .studio-prompt-bar { background: #111114; border-top: 1px solid rgba(255,255,255,0.05); }

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

        .dark .studio-prompt-input { color: #f1f5f9; }

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

        .btn-generate:hover { background: #f59e0b; transform: scale(1.02); }

        /* Formatação Toolbar */
        .format-group {
            display: flex;
            align-items: center;
            background: rgba(0,0,0,0.03);
            padding: 4px;
            border-radius: 100px;
            gap: 2px;
        }
        .dark .format-group { background: rgba(255,255,255,0.05); }

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
        .dark .format-btn { color: #94a3b8; }
        .format-btn:hover { background: rgba(0,0,0,0.05); color: #1e293b; }
        .dark .format-btn:hover { background: rgba(255,255,255,0.1); color: white; }
        .format-btn.active { background: #fbbf24; color: #000; }

        /* Galeria */
        .gallery-item {
            width: 68px; height: 68px; border-radius: 14px; overflow: hidden; cursor: pointer; border: 3px solid transparent; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); background: #e2e8f0;
        }
        .gallery-item.active { border-color: #fbbf24; box-shadow: 0 8px 16px -4px rgba(251, 191, 36, 0.3); }
        .gallery-item img { width: 100%; height: 100%; object-fit: cover; }

        /* FORÇA BRUTA LIXEIRA */
        .trash-btn {
            position: absolute !important;
            top: -5px !important;
            right: -5px !important;
            width: 24px !important;
            height: 24px !important;
            background-color: #ef4444 !important; /* Vermelho Vivo */
            color: white !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            z-index: 999 !important;
            border: 2px solid white !important;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3) !important;
            cursor: pointer !important;
            opacity: 0 !important; /* Escondido por padrão */
            transition: opacity 0.2s ease !important;
        }

        .gallery-item-wrapper:hover .trash-btn {
            opacity: 1 !important; /* Aparece no hover */
        }
    </style>

    <div class="studio-container" 
         x-data="{}"
         style="--highlight: {{ $overlayColor }};">
        
        {{-- Sidebar Esquerda --}}
        <aside class="studio-sidebar-left">
             <div class="space-y-6">
                {{-- Botão Voltar --}}
                <div class="mb-4">
                    <a href="{{ filament()->getUrl() }}" class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-amber-500 transition-colors group">
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
                    <input type="text" wire:model.live.debounce.300ms="postTitle" class="studio-input" placeholder="Ex: NOVIDADE">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="studio-input-wrap">
                        <label class="studio-label">Formato</label>
                        <select wire:model.live="platform" class="studio-input">
                            @foreach ($this->platformOptions as $v => $o) <option value="{{ $v }}">{{ $o['label'] }}</option> @endforeach
                        </select>
                    </div>
                    <div class="studio-input-wrap">
                        <label class="studio-label">Fonte</label>
                        <select wire:model.live="fontFamily" class="studio-input">
                            @foreach ($this->fontOptions as $v => $l) <option value="{{ $v }}">{{ $l }}</option> @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="studio-input-wrap">
                        <label class="studio-label">Cor do Texto</label>
                        <input type="color" wire:model.live="textColor" class="w-full h-10 p-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-white/10 rounded-lg cursor-pointer">
                    </div>
                    <div class="studio-input-wrap">
                        <label class="studio-label">Highlight / Filtro</label>
                        <input type="color" wire:model.live="overlayColor" class="w-full h-10 p-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-white/10 rounded-lg cursor-pointer">
                    </div>
                </div>

                <div class="studio-input-wrap">
                    <div class="flex justify-between items-center mb-1">
                        <label class="studio-label !mb-0">Tamanho da Fonte</label>
                        <span class="text-[11px] text-amber-500 font-black">{{ $fontSize }}px</span>
                    </div>
                    <input type="range" wire:model.live="fontSize" min="12" max="150" class="w-full accent-amber-500 h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700">
                </div>

                <div class="studio-input-wrap">
                    <div class="flex justify-between items-center mb-1">
                        <label class="studio-label !mb-0">Opacidade Filtro</label>
                        <span class="text-[11px] text-amber-500 font-black">{{ $overlayOpacity }}%</span>
                    </div>
                    <input type="range" wire:model.live="overlayOpacity" min="0" max="100" class="w-full accent-amber-500 h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700">
                </div>

                <hr class="border-gray-200 dark:border-white/5">

                <div class="space-y-3" wire:poll.5s>
                    <label class="studio-label">Artes Recentes</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($this->recentPosts as $p)
                            <div class="relative aspect-square rounded-xl overflow-hidden bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 group shadow-sm">
                                @if($p->isGenerated()) 
                                    <img src="{{ $p->output_url }}" class="w-full h-full object-cover">
                                    
                                    {{-- Barra de Ações (Sempre Visível no fundo da miniatura) --}}
                                    <div class="absolute bottom-0 inset-x-0 bg-black/40 backdrop-blur-md p-1.5 flex items-center justify-center gap-2">
                                        <a href="{{ $p->output_url }}" target="_blank" title="Visualizar" class="p-1 hover:text-amber-500 transition-colors text-white">
                                            <x-heroicon-m-eye class="w-3.5 h-3.5" />
                                        </a>
                                        <div class="w-px h-3 bg-white/20"></div>
                                        <a href="{{ $p->output_url }}" download="arte-{{ $p->id }}.png" title="Baixar" class="p-1 hover:text-amber-500 transition-colors text-white">
                                            <x-heroicon-m-arrow-down-tray class="w-3.5 h-3.5" />
                                        </a>
                                        <div class="w-px h-3 bg-white/20"></div>
                                        <button wire:click="deletePost({{ $p->id }})" 
                                                wire:confirm="Tem certeza que deseja apagar esta arte?"
                                                title="Deletar" 
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
                <div class="preview-wrapper style-{{ $preset }}"
                     style="width: 540px; aspect-ratio: {{ $platform === 'story' ? '9/16' : ($platform === 'facebook' || $platform === 'linkedin' ? '1.91/1' : '1/1') }}; max-height: 85%;">
                    
                    {{-- Background --}}
                    <div class="absolute inset-0 bg-[#000]">
                         @if($this->selectedBackground?->getPreviewUrl())
                            <div class="inner-image absolute inset-0 bg-cover bg-center"
                                 style="background-image: url('{{ $this->selectedBackground->getPreviewUrl() }}');">
                            </div>
                        @endif
                    </div>

                    <div class="absolute inset-0" style="background: {{ $this->overlayCss }};"></div>
                    @if($preset === 'canva_side') <div class="side-block"></div> @endif

                    {{-- DRAGGABLE TEXT BLOCK (ESTÁVEL) --}}
                    <div class="draggable-text"
                         x-data="{ 
                            textX: {{ $textX }}, 
                            textY: {{ $textY }},
                            isDragging: false,
                            init() {
                                interact($el).draggable({
                                    listeners: {
                                        start: () => { this.isDragging = true },
                                        move: (event) => {
                                            const wrapper = document.querySelector('.preview-wrapper');
                                            const rect = wrapper.getBoundingClientRect();
                                            this.textX += (event.dx / rect.width) * 100;
                                            this.textY += (event.dy / rect.height) * 100;
                                        },
                                        end: () => {
                                            this.isDragging = false;
                                            @this.updateCoordinates(this.textX, this.textY);
                                        }
                                    }
                                });
                            }
                         }"
                         :class="{ 'is-dragging': isDragging }"
                         :style="`left: ${textX}%; top: ${textY}%; transform: translate(${ @this.textAlign === 'left' ? '0' : (@this.textAlign === 'right' ? '-100' : '-50') }%, -50%); font-family: '${@this.fontFamily.replace('+', ' ')}', sans-serif; text-align: {{ $textAlign }};`"
                         style="position: absolute; cursor: move; z-index: 100; width: auto; max-width: 90%;">
                        

                        @if(trim($quote)) 
                            <p class="quote-text leading-tight" style="color: {{ $textColor }}; font-weight: {{ $isBold ? '700' : '400' }}; font-style: {{ $isItalic ? 'italic' : 'normal' }}; font-size: {{ $fontSize }}px; text-shadow: 0 2px 10px rgba(0,0,0,0.2); font-family: inherit; text-align: inherit;">
                                {{ $quote }}
                            </p> 
                        @else 
                            <p class="text-[12px] uppercase font-black opacity-20 tracking-widest italic" style="color: {{ $textColor }}; font-family: inherit; text-align: inherit;">Arraste para posicionar</p> 
                        @endif
                    </div>
                </div>
            </div>

            {{-- Prompt Bar & Toolbar (Fixa no rodapé) --}}
            <div class="studio-prompt-bar">
                
                {{-- Text Style Toolbar --}}
                <div class="format-group">
                    <div wire:click="$toggle('isBold')" class="format-btn {{ $isBold ? 'active' : '' }}"><span class="font-black text-xs">B</span></div>
                    <div wire:click="$toggle('isItalic')" class="format-btn {{ $isItalic ? 'active' : '' }}"><span class="italic font-serif text-xs">I</span></div>
                </div>

                <div class="w-px h-8 bg-gray-200 dark:bg-white/10 mx-1"></div>

                <input type="text" wire:model.live.debounce.300ms="quote" placeholder="Sua frase de impacto..." class="studio-prompt-input">
                
                <button wire:click="dispatchGeneration" wire:loading.attr="disabled" wire:target="dispatchGeneration" class="btn-generate">
                    <span wire:loading.remove wire:target="dispatchGeneration">GERAR ARTE</span>
                    <x-heroicon-o-arrow-path wire:loading wire:target="dispatchGeneration" class="w-4 h-4 animate-spin" />
                </button>
            </div>
        </main>

        {{-- Sidebar Direita: Galeria Vertical --}}
        <aside class="studio-sidebar-right">
             <div class="flex flex-col gap-4 pb-8">
                {{-- Botão de Upload Customizado --}}
                <button wire:click="mountAction('uploadBackground')" 
                        type="button"
                        class="gallery-item flex items-center justify-center bg-amber-500 border-none hover:bg-amber-600 transition-colors shadow-lg group">
                    <x-heroicon-o-plus class="w-8 h-8 text-black group-hover:scale-110 transition-transform" />
                </button>

                @foreach ($this->backgrounds as $bg)
                    <div class="gallery-item-wrapper relative" style="width: 68px; height: 68px; margin-bottom: 12px;">
                        <div class="gallery-item {{ $backgroundImageId === $bg->id ? 'active' : '' }}" 
                             style="width: 100%; height: 100%; border-radius: 14px; overflow: hidden; position: relative;">
                            <img src="{{ $bg->getThumbnailUrl() }}" 
                                 wire:click="selectBackground({{ $bg->id }})"
                                 class="w-full h-full object-cover cursor-pointer">
                        </div>

                        {{-- Botão de Lixeira (SVG DIRETO E CSS IMPORTANTE) --}}
                        <button wire:click="deleteBackground({{ $bg->id }})"
                                wire:confirm="Tem certeza que deseja apagar esta imagem?"
                                type="button"
                                class="trash-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width: 14px; height: 14px;">
                                <path fill-rule="evenodd" d="M8.75 3A2.75 2.75 0 0 0 6 5.75v.562c-.34.059-.68.124-1.022.196a.75.75 0 1 0 .312 1.468h.011l.83 7.06c.066.566.547 1.001 1.116 1.001h5.49c.568 0 1.05-.435 1.115-1.001l.83-7.06h.013a.75.75 0 1 0 .312-1.468 48.707 48.707 0 0 0-1.022-.195V5.75A2.75 2.75 0 0 0 11.25 3h-2.5ZM10 7.5a.75.75 0 0 1 .75.75v5.25a.75.75 0 0 1-1.5 0V8.25A.75.75 0 0 1 10 7.5ZM7.25 8.25a.75.75 0 0 0-1.5 0v5.25a.75.75 0 0 0 1.5 0V8.25Zm6 0a.75.75 0 0 0-1.5 0v5.25a.75.75 0 0 0 1.5 0V8.25Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
        </aside>
    </div>

    <x-filament-actions::modals />

    <script>
        document.addEventListener('livewire:updated', function () {
            const font  = @this.fontFamily ?? 'Inter';
            const link  = document.getElementById('gf-link');
            const url   = `https://fonts.googleapis.com/css2?family=${encodeURIComponent(font)}:wght@400;700;900&display=swap`;
            if (link) link.href = url;
        });
    </script>
</x-filament-panels::page>
