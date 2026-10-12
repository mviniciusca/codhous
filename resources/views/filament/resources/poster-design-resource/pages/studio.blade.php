@php
    $i = fn (string $name, string $class = 'ps-i') => svg('heroicon-o-'.$name, $class, ['aria-hidden' => 'true'])->toHtml();
    $config = $this->studioConfig();
@endphp

<div>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="{{ \App\Services\PosterStudio\Fonts::googleUrl() }}">
    @vite('resources/js/poster-studio/index.js')

    <div class="ps" wire:ignore x-data="posterStudio(@js($config))" x-cloak>

        {{-- ============================================================ topo --}}
        <header class="ps-top">
            <a href="{{ $config['urls']['index'] }}" class="ps-icon-btn" title="Voltar para a galeria" aria-label="Voltar para a galeria">{!! $i('arrow-left') !!}</a>

            <div class="ps-brand">
                <span class="ps-brand-mark ps-hide-sm">{!! $i('photo') !!}</span>
                <label class="ps-sr" for="ps-name">Nome da arte</label>
                <input id="ps-name" class="ps-name" x-model="name" @change="renameProject()" @keydown.enter="$event.target.blur()" maxlength="120">
            </div>

            <span class="ps-status ps-hide-sm" :data-status="status" role="status" aria-live="polite" :title="statusMessage">
                <span class="ps-status-dot"></span>
                <span x-text="statusLabel"></span>
            </span>
            <template x-if="config.design.isVariation">
                <span class="ps-chip linked ps-hide-md" x-text="'Variação · ' + (config.design.variantLabel ?? '')"></span>
            </template>

            <div class="ps-spacer"></div>

            <div class="ps-group">
                <button type="button" class="ps-icon-btn" @click="undo()" :disabled="!canUndo" title="Desfazer (Ctrl+Z)" aria-label="Desfazer">{!! $i('arrow-uturn-left') !!}</button>
                <button type="button" class="ps-icon-btn" @click="redo()" :disabled="!canRedo" title="Refazer (Ctrl+Shift+Z)" aria-label="Refazer">{!! $i('arrow-uturn-right') !!}</button>
            </div>

            <button type="button" class="ps-btn ps-hide-sm" @click="save()" :disabled="status === 'saving'" title="Salvar (Ctrl+S)">
                {!! $i('cloud-arrow-up') !!} <span>Salvar</span>
            </button>
            <button type="button" class="ps-btn ps-btn-primary" @click="rightTab = 'export'; rightOpen = true" title="Baixar imagem">
                {!! $i('arrow-down-tray') !!} <span class="ps-hide-sm">Baixar</span>
            </button>
            <button type="button" class="ps-icon-btn ps-right-toggle" style="display: none" @click="rightOpen = !rightOpen" :aria-pressed="rightOpen" title="Painel de propriedades" aria-label="Painel de propriedades">{!! $i('adjustments-horizontal') !!}</button>

            <div class="ps-menu" @click.outside="menuOpen = false">
                <button type="button" class="ps-icon-btn" @click="menuOpen = !menuOpen" :aria-expanded="menuOpen" aria-label="Mais ações">{!! $i('ellipsis-horizontal') !!}</button>
                <div class="ps-dropdown" x-show="menuOpen" x-transition.opacity>
                    <button type="button" @click="menuOpen = false; await save(); $wire.duplicate()">{!! $i('document-duplicate') !!} Duplicar arte</button>
                    <button type="button" @click="menuOpen = false; rightTab = 'variations'; rightOpen = true">{!! $i('swatch') !!} Variações e versões</button>
                    <button type="button" @click="menuOpen = false; refreshBrand()">{!! $i('arrow-path') !!} Atualizar dados da empresa</button>
                    <hr>
                    <button type="button" @click="menuOpen = false; showShortcuts = true">{!! $i('information-circle') !!} Atalhos de teclado</button>
                    <a href="{{ $config['urls']['index'] }}">{!! $i('squares-2x2') !!} Todas as artes</a>
                </div>
            </div>
        </header>

        {{-- ============================================================ avisos --}}
        <template x-if="status === 'conflict'">
            <div class="ps-banner danger" role="alert">
                {!! $i('exclamation-triangle') !!}
                <span x-text="statusMessage"></span>
                <span class="ps-spacer"></span>
                <button type="button" class="ps-btn ps-btn-sm" @click="window.location.reload()">Carregar versão salva</button>
                <button type="button" class="ps-btn ps-btn-sm ps-btn-danger" @click="save(true)">Sobrescrever com a minha</button>
            </div>
        </template>
        <template x-if="status === 'error'">
            <div class="ps-banner danger" role="alert">
                {!! $i('exclamation-triangle') !!}
                <span x-text="statusMessage || 'Não foi possível salvar.'"></span>
                <span class="ps-spacer"></span>
                <button type="button" class="ps-btn ps-btn-sm" @click="save()">Tentar de novo</button>
            </div>
        </template>
        <template x-if="draftRecovery">
            <div class="ps-banner" role="alert">
                {!! $i('information-circle') !!}
                <span x-text="'Encontramos alterações não salvas de ' + draftRecovery.at + '.'"></span>
                <span class="ps-spacer"></span>
                <button type="button" class="ps-btn ps-btn-sm" @click="discardDraft()">Descartar</button>
                <button type="button" class="ps-btn ps-btn-sm ps-btn-primary" @click="restoreDraft()">Restaurar</button>
            </div>
        </template>

        <div class="ps-body">
            {{-- ======================================================== trilho de ferramentas --}}
            <nav class="ps-rail" aria-label="Ferramentas">
                @foreach ([
                    'templates' => ['squares-2x2', 'Templates'],
                    'text' => ['language', 'Texto'],
                    'images' => ['photo', 'Imagens'],
                    'elements' => ['square-2-stack', 'Elementos'],
                    'data' => ['building-storefront', 'Dados'],
                    'colors' => ['swatch', 'Cores'],
                    'layers' => ['square-3-stack-3d', 'Camadas'],
                ] as $key => [$icon, $label])
                    <button type="button" class="ps-tool" :aria-current="tool === '{{ $key }}' && leftOpen"
                            @click="tool === '{{ $key }}' ? (leftOpen = !leftOpen) : (tool = '{{ $key }}', leftOpen = true)">
                        {!! $i($icon) !!}<span>{{ $label }}</span>
                    </button>
                @endforeach
            </nav>

            {{-- ======================================================== painel esquerdo --}}
            <aside class="ps-left" :class="{ open: leftOpen }" x-show="leftOpen || window.innerWidth < 1024" aria-label="Painel da ferramenta">
                @include('filament.resources.poster-design-resource.partials.left-panels')
            </aside>

            {{-- ======================================================== centro --}}
            <main class="ps-center">
                <div class="ps-ctxbar">
                    <template x-if="selection.length === 0">
                        <div style="display: contents">
                            <span class="ps-ctxbar-label" x-text="formatLabel(doc.format) + ' · ' + doc.width + '×' + doc.height + ' px'"></span>
                            <div class="ps-menu" x-data="{ open: false }" @click.outside="open = false">
                                <button type="button" class="ps-btn ps-btn-sm" @click="open = !open">{!! $i('arrows-pointing-out', 'ps-i-sm') !!} Mudar formato</button>
                                <div class="ps-dropdown left" x-show="open" x-transition.opacity style="min-width: 250px">
                                    <template x-for="(format, key) in formats" :key="key">
                                        <button type="button" @click="open = false; changeFormat(key)">
                                            <span style="flex: 1" x-text="format.label"></span>
                                            <small style="color: var(--ps-muted)" x-text="format.width + '×' + format.height"></small>
                                        </button>
                                    </template>
                                    <hr>
                                    <button type="button" @click="open = false; rightTab = 'design'; rightOpen = true; $nextTick(() => document.getElementById('ps-custom-width')?.focus())">Personalizado…</button>
                                </div>
                            </div>
                            <span class="ps-spacer"></span>
                            <span class="ps-hint ps-hide-md" style="margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; min-width: 0">Duplo clique edita textos · <kbd>Alt</kbd> ao arrastar desliga o alinhamento automático</span>
                        </div>
                    </template>

                    <template x-if="selection.length > 0">
                        <div style="display: contents">
                            <span class="ps-ctxbar-label" x-text="sel ? (sel?.name || typeLabels[sel?.type]) : selection.length + ' elementos'"></span>
                            <div class="ps-group">
                                <template x-if="sel && sel?.type === 'image'">
                                    <button type="button" class="ps-icon-btn" style="padding: 0 10px; gap: 6px; display: inline-flex" @click="openImagePicker(sel?.id)" :disabled="sel?.locked">{!! $i('photo', 'ps-i-sm') !!} Trocar</button>
                                </template>
                                <button type="button" class="ps-icon-btn" @click="duplicate()" title="Duplicar (Ctrl+D)" aria-label="Duplicar">{!! $i('document-duplicate') !!}</button>
                                <button type="button" class="ps-icon-btn" @click="toggleLock()" :aria-pressed="selectionLocked" :title="selectionLocked ? 'Desbloquear' : 'Bloquear'" :aria-label="selectionLocked ? 'Desbloquear' : 'Bloquear'">
                                    <span x-show="selectionLocked">{!! $i('lock-closed') !!}</span><span x-show="!selectionLocked">{!! $i('lock-open') !!}</span>
                                </button>
                                <button type="button" class="ps-icon-btn" @click="bringForward()" title="Trazer para frente" aria-label="Trazer para frente">{!! $i('bars-arrow-up') !!}</button>
                                <button type="button" class="ps-icon-btn" @click="sendBackward()" title="Enviar para trás" aria-label="Enviar para trás">{!! $i('bars-arrow-down') !!}</button>
                            </div>
                            <div class="ps-group ps-hide-sm">
                                @include('filament.resources.poster-design-resource.partials.align-buttons')
                            </div>
                            <span class="ps-spacer"></span>
                            <button type="button" class="ps-icon-btn danger" @click="remove()" :disabled="selectionLocked" title="Excluir (Delete)" aria-label="Excluir">{!! $i('trash') !!}</button>
                        </div>
                    </template>
                </div>

                <div class="ps-workspace" x-ref="workspace" @mousedown.self="clearSelection()">
                    <div class="ps-stage" :style="{ width: Math.round(doc.width * zoom) + 'px', height: Math.round(doc.height * zoom) + 'px' }">
                        <canvas x-ref="canvas"></canvas>
                        <div class="ps-overlay">
                            <template x-for="(area, index) in safeAreaBoxes" :key="index">
                                <div class="ps-safe" :style="{ left: area.x * zoom + 'px', top: area.y * zoom + 'px', width: area.width * zoom + 'px', height: area.height * zoom + 'px' }">
                                    <span>Área coberta pela interface do app</span>
                                </div>
                            </template>
                            <template x-for="(guide, index) in guides" :key="index">
                                <div class="ps-guide" :class="guide.type" :style="guide.type === 'v' ? { left: guide.pos * zoom + 'px' } : { top: guide.pos * zoom + 'px' }"></div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="ps-loading" x-show="!ready">
                    <div style="text-align: center">
                        <template x-if="!fatal"><div><div class="ps-spinner"></div>Carregando editor…</div></template>
                        <template x-if="fatal"><div style="color: var(--ps-danger)" x-text="fatal"></div></template>
                    </div>
                </div>

                <footer class="ps-bottombar">
                    <span><strong x-text="doc.width + ' × ' + doc.height"></strong> px</span>
                    <span x-text="layers.length + ' elementos'"></span>
                    <template x-if="transform">
                        <span x-text="'X ' + transform.x + '  Y ' + transform.y + '  ·  ' + transform.width + ' × ' + transform.height + (transform.angle ? '  ·  ' + transform.angle + '°' : '')"></span>
                    </template>
                    <span class="ps-spacer"></span>
                    <template x-if="safeAreaBoxes.length || showSafeArea === false">
                        <label class="ps-switch" style="padding: 0; gap: 6px">
                            <input type="checkbox" x-model="showSafeArea"> Áreas seguras
                        </label>
                    </template>
                    <div class="ps-zoom ps-menu" @click.outside="zoomMenu = false">
                        <button type="button" class="ps-icon-btn" @click="zoomBy(1 / 1.2)" title="Diminuir zoom (Ctrl −)" aria-label="Diminuir zoom">{!! $i('minus', 'ps-i-sm') !!}</button>
                        <button type="button" class="ps-icon-btn ps-zoom-value" @click="zoomMenu = !zoomMenu" x-text="zoomPercent + '%'" aria-label="Escolher zoom"></button>
                        <button type="button" class="ps-icon-btn" @click="zoomBy(1.2)" title="Aumentar zoom (Ctrl +)" aria-label="Aumentar zoom">{!! $i('plus', 'ps-i-sm') !!}</button>
                        <div class="ps-dropdown" x-show="zoomMenu" x-transition.opacity style="top: auto; bottom: calc(100% + 6px); min-width: 140px">
                            <button type="button" @click="setZoom('fit')">Ajustar à tela</button>
                            <template x-for="value in [0.25, 0.5, 0.75, 1, 1.5, 2]" :key="value">
                                <button type="button" @click="setZoom(value)" x-text="Math.round(value * 100) + '%'"></button>
                            </template>
                        </div>
                    </div>
                </footer>
            </main>

            {{-- ======================================================== painel direito --}}
            <aside class="ps-right" :class="{ open: rightOpen }" aria-label="Propriedades e exportação">
                @include('filament.resources.poster-design-resource.partials.right-panel')
            </aside>
        </div>

        {{-- ============================================================ diálogos --}}
        <template x-if="confirmDialog">
            <div class="ps-backdrop" @click.self="confirmDialog = null" @keydown.escape.window="confirmDialog = null">
                <div class="ps-dialog" role="alertdialog" aria-modal="true" :aria-label="confirmDialog.title">
                    <h3 x-text="confirmDialog.title"></h3>
                    <p x-text="confirmDialog.message"></p>
                    <footer>
                        <button type="button" class="ps-btn" @click="confirmDialog = null">Cancelar</button>
                        <button type="button" class="ps-btn ps-btn-primary" x-init="$nextTick(() => $el.focus())" @click="confirmYes()">Confirmar</button>
                    </footer>
                </div>
            </div>
        </template>

        <template x-if="showShortcuts">
            <div class="ps-backdrop" @click.self="showShortcuts = false" @keydown.escape.window="showShortcuts = false">
                <div class="ps-dialog" role="dialog" aria-modal="true" aria-label="Atalhos de teclado">
                    <h3>Atalhos de teclado</h3>
                    <table style="width: 100%; border-collapse: collapse; margin: 10px 0 16px; font-size: 13px">
                        @foreach ([
                            'Ctrl + Z / Ctrl + Shift + Z' => 'Desfazer / refazer',
                            'Ctrl + S' => 'Salvar agora',
                            'Ctrl + D' => 'Duplicar',
                            'Ctrl + C / Ctrl + V' => 'Copiar / colar elementos',
                            'Delete' => 'Excluir seleção',
                            'Setas (+ Shift)' => 'Mover 1 px (10 px)',
                            'Shift + clique' => 'Selecionar vários',
                            'Alt ao arrastar' => 'Sem alinhamento automático',
                            'Ctrl + / Ctrl − / Ctrl 0' => 'Zoom / ajustar à tela',
                            'Esc' => 'Limpar seleção',
                        ] as $keys => $action)
                            <tr style="border-top: 1px solid var(--ps-border)"><td style="padding: 6px 0"><kbd>{{ $keys }}</kbd></td><td style="padding: 6px 0; color: var(--ps-muted)">{{ $action }}</td></tr>
                        @endforeach
                    </table>
                    <footer><button type="button" class="ps-btn" @click="showShortcuts = false">Fechar</button></footer>
                </div>
            </div>
        </template>

        <template x-if="toast">
            <div class="ps-toast" :data-type="toast.type" role="status" aria-live="polite" x-text="toast.message"></div>
        </template>
    </div>
</div>
