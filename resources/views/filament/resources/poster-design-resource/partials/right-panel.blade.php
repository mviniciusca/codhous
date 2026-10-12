@php
    $i = fn (string $name, string $class = 'ps-i') => svg('heroicon-o-'.$name, $class, ['aria-hidden' => 'true'])->toHtml();
    $color = 'filament.resources.poster-design-resource.partials.color-field';
@endphp

<div class="ps-panel-head" style="padding-bottom: 8px">
    <div class="ps-tabs" role="tablist" style="margin: 0; flex: 1">
        <button type="button" role="tab" :aria-selected="rightTab === 'design'" @click="rightTab = 'design'">Design</button>
        <button type="button" role="tab" :aria-selected="rightTab === 'variations'" @click="rightTab = 'variations'">Variações</button>
        <button type="button" role="tab" :aria-selected="rightTab === 'export'" @click="rightTab = 'export'">Exportar</button>
    </div>
    <button type="button" class="ps-icon-btn ps-right-toggle" style="display: none" @click="rightOpen = false" aria-label="Fechar painel">{!! $i('x-mark') !!}</button>
</div>

<div class="ps-scroll">

{{-- ==================================================================== DESIGN --}}
<template x-if="rightTab === 'design'">
<div>
    {{-- ---------------------------------------------------------- vários elementos --}}
    <template x-if="selection.length > 1">
        <div class="ps-section">
            <div class="ps-section-title"><span x-text="selection.length + ' elementos selecionados'"></span></div>
            <span class="ps-label">Alinhar entre si</span>
            <div class="ps-group" style="margin: 6px 0 12px; flex-wrap: wrap">
                @include('filament.resources.poster-design-resource.partials.align-buttons')
            </div>
            <div class="ps-row">
                <button type="button" class="ps-btn ps-btn-sm" @click="duplicate()">{!! $i('document-duplicate', 'ps-i-sm') !!} Duplicar</button>
                <button type="button" class="ps-btn ps-btn-sm ps-btn-danger" @click="remove()">{!! $i('trash', 'ps-i-sm') !!} Excluir</button>
            </div>
        </div>
    </template>

    {{-- ---------------------------------------------------------- um elemento --}}
    <template x-if="sel">
        <div>
            <div class="ps-section">
                <div class="ps-field">
                    <span>Nome da camada</span>
                    <input class="ps-input" :value="sel?.name" @change="renameLayer(sel?.id, $event.target.value)" maxlength="60">
                </div>

                <template x-if="sel?.bind">
                    <div class="ps-alert info" style="display: flex; align-items: center; gap: 8px">
                        {!! $i('link', 'ps-i-sm') !!}
                        <span style="flex: 1">Vinculado a <b x-text="bindLabel(sel?.bind)"></b>. Atualiza junto com as Configurações.</span>
                        <button type="button" class="ps-link" @click="unbind()">Desvincular</button>
                    </div>
                </template>

                <template x-if="sel?.locked">
                    <div class="ps-alert info" style="display: flex; align-items: center; gap: 8px">
                        {!! $i('lock-closed', 'ps-i-sm') !!}
                        <span style="flex: 1">Elemento bloqueado.</span>
                        <button type="button" class="ps-link" @click="toggleLock()">Desbloquear</button>
                    </div>
                </template>
            </div>

            {{-- ---------- texto --}}
            <template x-if="sel?.type === 'text'">
                <div class="ps-section">
                    <div class="ps-section-title">Texto</div>
                    <div class="ps-field">
                        <textarea class="ps-textarea" :value="sel?.text" @input="setProp('text', $event.target.value, { commit: false })" :disabled="sel?.locked" aria-label="Conteúdo do texto"></textarea>
                    </div>
                    <div class="ps-field">
                        <span>Fonte</span>
                        <select class="ps-select" :value="sel?.fontFamily" @change="setProp('fontFamily', $event.target.value); fontWeights($event.target.value).includes(sel?.fontWeight) || setProp('fontWeight', fontWeights($event.target.value).at(-1))">
                            <template x-for="font in fonts" :key="font"><option :value="font" x-text="font" :style="{ fontFamily: font }" :selected="font === sel?.fontFamily"></option></template>
                        </select>
                    </div>
                    <div class="ps-row">
                        <label class="ps-field"><span>Peso</span>
                            <select class="ps-select" @change="setProp('fontWeight', Number($event.target.value))">
                                <template x-for="weight in fontWeights(sel?.fontFamily)" :key="weight">
                                    <option :value="weight" :selected="weight === sel?.fontWeight" x-text="{ 400: 'Regular', 500: 'Médio', 600: 'Seminegrito', 700: 'Negrito', 800: 'Extra negrito', 900: 'Black' }[weight] ?? weight"></option>
                                </template>
                            </select>
                        </label>
                        <label class="ps-field"><span>Tamanho</span>
                            <input type="number" class="ps-input" min="4" max="1000" :value="Math.round(sel?.fontSize)" @change="setNumber('fontSize', $event.target.value, { min: 4, max: 1000 })">
                        </label>
                    </div>
                    <div class="ps-field">
                        <span>Alinhamento e estilo</span>
                        <div style="display: flex; gap: 8px">
                            <div class="ps-seg" style="flex: 1">
                                <template x-for="align in ['left', 'center', 'right', 'justify']" :key="align">
                                    <button type="button" :aria-pressed="sel?.textAlign === align" @click="setProp('textAlign', align)" :aria-label="{ left: 'Esquerda', center: 'Centro', right: 'Direita', justify: 'Justificado' }[align]" :title="{ left: 'Esquerda', center: 'Centro', right: 'Direita', justify: 'Justificado' }[align]">
                                        <svg class="ps-i-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                            <path d="M4 6h16"/>
                                            <path :d="{ left: 'M4 12h10', center: 'M7 12h10', right: 'M10 12h10', justify: 'M4 12h16' }[align]"/>
                                            <path d="M4 18h16"/>
                                        </svg>
                                    </button>
                                </template>
                            </div>
                            <div class="ps-seg">
                                <button type="button" :aria-pressed="sel?.fontStyle === 'italic'" @click="setProp('fontStyle', sel?.fontStyle === 'italic' ? 'normal' : 'italic')" title="Itálico" aria-label="Itálico" style="width: 34px">{!! $i('italic', 'ps-i-sm') !!}</button>
                            </div>
                        </div>
                    </div>
                    <label class="ps-field"><span>Altura da linha</span>
                        <div class="ps-range">
                            <input type="range" min="0.7" max="2.5" step="0.05" :value="sel?.lineHeight" @input="setProp('lineHeight', Number($event.target.value), { commit: false })">
                            <output x-text="Number(sel?.lineHeight).toFixed(2)"></output>
                        </div>
                    </label>
                    <label class="ps-field"><span>Espaçamento entre letras</span>
                        <div class="ps-range">
                            <input type="range" min="-100" max="800" step="10" :value="sel?.charSpacing" @input="setProp('charSpacing', Number($event.target.value), { commit: false })">
                            <output x-text="sel?.charSpacing"></output>
                        </div>
                    </label>
                    @include($color, ['label' => 'Cor do texto', 'model' => 'sel?.fill', 'set' => "(v) => setColor('fill', v)"])
                </div>
            </template>

            {{-- ---------- formas --}}
            <template x-if="sel?.type === 'rect' || sel?.type === 'ellipse'">
                <div class="ps-section">
                    <div class="ps-section-title">Preenchimento e borda</div>
                    @include($color, ['label' => 'Preenchimento', 'model' => 'sel?.fill', 'set' => "(v) => setColor('fill', v)", 'transparent' => true])
                    @include($color, ['label' => 'Borda', 'model' => 'sel?.stroke', 'set' => "(v) => { setColor('stroke', v); v && !sel?.strokeWidth && setProp('strokeWidth', 4) }", 'transparent' => true])
                    <label class="ps-field"><span>Espessura da borda</span>
                        <div class="ps-range">
                            <input type="range" min="0" max="60" step="1" :value="sel?.strokeWidth" @input="setProp('strokeWidth', Number($event.target.value), { commit: false })">
                            <output x-text="sel?.strokeWidth + ' px'"></output>
                        </div>
                    </label>
                    <template x-if="sel?.type === 'rect'">
                        <label class="ps-field"><span>Cantos arredondados</span>
                            <div class="ps-range">
                                <input type="range" min="0" :max="Math.round(Math.min(sel?.width, sel?.height) / 2)" step="1" :value="sel?.radius" @input="setProp('radius', Number($event.target.value), { commit: false })">
                                <output x-text="Math.round(sel?.radius) + ' px'"></output>
                            </div>
                        </label>
                    </template>
                </div>
            </template>

            {{-- ---------- imagem --}}
            <template x-if="sel?.type === 'image'">
                <div class="ps-section">
                    <div class="ps-section-title">Imagem</div>
                    <button type="button" class="ps-btn ps-btn-block" style="margin-bottom: 10px" @click="openImagePicker(sel?.id)" :disabled="sel?.locked">{!! $i('photo', 'ps-i-sm') !!} <span x-text="sel?.src ? 'Trocar imagem' : 'Escolher imagem'"></span></button>
                    <template x-if="sel?.src">
                        <div>
                            <div class="ps-field">
                                <span>Ajuste na moldura</span>
                                <div class="ps-seg">
                                    <button type="button" :aria-pressed="sel?.fit !== 'contain'" @click="setProp('fit', 'cover')">Preencher</button>
                                    <button type="button" :aria-pressed="sel?.fit === 'contain'" @click="setProp('fit', 'contain')">Inteira</button>
                                </div>
                                <p class="ps-hint">Preencher recorta a foto; arraste as alças laterais para mudar o recorte.</p>
                            </div>
                            <label class="ps-field"><span>Cantos arredondados</span>
                                <div class="ps-range">
                                    <input type="range" min="0" :max="Math.round(Math.min(sel?.width, sel?.height) / 2)" step="1" :value="sel?.radius" @input="setProp('radius', Number($event.target.value), { commit: false })">
                                    <output x-text="Math.round(sel?.radius) + ' px'"></output>
                                </div>
                            </label>
                            @foreach (['brightness' => 'Brilho', 'contrast' => 'Contraste', 'saturation' => 'Saturação'] as $key => $label)
                                <label class="ps-field"><span>{{ $label }}</span>
                                    <div class="ps-range">
                                        <input type="range" min="-1" max="1" step="0.05" :value="sel?.{{ $key }} ?? 0" @change="setProp('{{ $key }}', Number($event.target.value))">
                                        <output x-text="Math.round((sel?.{{ $key }} ?? 0) * 100)"></output>
                                    </div>
                                </label>
                            @endforeach
                            <div class="ps-row">
                                <button type="button" class="ps-btn ps-btn-sm" @click="setProp('flipX', !sel?.flipX)">Espelhar</button>
                                <button type="button" class="ps-btn ps-btn-sm" @click="setProps({ brightness: 0, contrast: 0, saturation: 0 })">Restaurar ajustes</button>
                            </div>
                            <button type="button" class="ps-btn ps-btn-sm ps-btn-block" style="margin-top: 8px" @click="extractFromImage()">{!! $i('sparkles', 'ps-i-sm') !!} Criar paleta com as cores da foto</button>
                        </div>
                    </template>
                </div>
            </template>

            {{-- ---------- ícone --}}
            <template x-if="sel?.type === 'icon'">
                <div class="ps-section">
                    <div class="ps-section-title">Ícone</div>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px">
                        <span class="ps-icon-tile" style="width: 48px" x-html="iconHtml(sel?.icon, 26)"></span>
                        <button type="button" class="ps-btn ps-btn-sm" @click="tool = 'elements'; leftOpen = true">Trocar ícone</button>
                    </div>
                    @include($color, ['label' => 'Cor', 'model' => 'sel?.fill', 'set' => "(v) => setColor('fill', v)"])
                    <label class="ps-field"><span>Espessura do traço</span>
                        <div class="ps-range">
                            <input type="range" min="0.5" max="4" step="0.25" :value="sel?.strokeWidth" @change="setProp('strokeWidth', Number($event.target.value))">
                            <output x-text="sel?.strokeWidth"></output>
                        </div>
                    </label>
                </div>
            </template>

            {{-- ---------- QR Code --}}
            <template x-if="sel?.type === 'qrcode'">
                <div class="ps-section">
                    <div class="ps-section-title">QR Code</div>
                    <label class="ps-field"><span>Link ou texto</span>
                        <input class="ps-input" :value="sel?.value" @change="setProp('value', $event.target.value.trim()); sel?.bind && setProp('bind', null)" maxlength="500" placeholder="https://">
                    </label>
                    <template x-if="bindValue('brand.whatsapp_url') && sel?.bind !== 'brand.whatsapp_url'">
                        <button type="button" class="ps-btn ps-btn-sm ps-btn-block" style="margin-bottom: 10px" @click="setProp('value', bindValue('brand.whatsapp_url')); setProp('bind', 'brand.whatsapp_url')">Usar o WhatsApp da empresa</button>
                    </template>
                    <p class="ps-hint" style="margin: 0 0 10px">Teste a leitura com o celular antes de publicar. Cores escuras sobre fundo claro funcionam melhor.</p>
                    @include($color, ['label' => 'Cor dos módulos', 'model' => 'sel?.fill', 'set' => "(v) => setColor('fill', v)"])
                    @include($color, ['label' => 'Fundo', 'model' => 'sel?.background', 'set' => "(v) => setColor('background', v)", 'transparent' => true])
                </div>
            </template>

            {{-- ---------- posição e camada --}}
            <div class="ps-section">
                <div class="ps-section-title">Posição e tamanho</div>
                <div class="ps-row" style="margin-bottom: 8px">
                    <label class="ps-num"><em>X</em><input type="number" class="ps-input" :value="geometry('x')" @change="setNumber('x', $event.target.value)" :disabled="sel?.locked" aria-label="Posição X"></label>
                    <label class="ps-num"><em>Y</em><input type="number" class="ps-input" :value="geometry('y')" @change="setNumber('y', $event.target.value)" :disabled="sel?.locked" aria-label="Posição Y"></label>
                </div>
                <div class="ps-row" style="margin-bottom: 8px">
                    <label class="ps-num"><em>L</em><input type="number" class="ps-input" min="1" :value="geometry('width')" @change="setSize('width', $event.target.value)" :disabled="sel?.locked" aria-label="Largura"></label>
                    <label class="ps-num"><em>A</em><input type="number" class="ps-input" min="1" :value="geometry('height')" @change="setSize('height', $event.target.value)" :disabled="sel?.locked || sel?.type === 'text'" aria-label="Altura" :title="sel?.type === 'text' ? 'A altura do texto acompanha o conteúdo' : ''"></label>
                </div>
                <div class="ps-row">
                    <label class="ps-num"><em>°</em><input type="number" class="ps-input" min="-360" max="360" :value="geometry('angle')" @change="setNumber('angle', $event.target.value, { min: -360, max: 360 })" :disabled="sel?.locked" aria-label="Rotação em graus"></label>
                    <div></div>
                </div>
                <label class="ps-field" style="margin-top: 10px"><span>Opacidade</span>
                    <div class="ps-range">
                        <input type="range" min="0" max="1" step="0.01" :value="sel?.opacity" @input="setProp('opacity', Number($event.target.value), { commit: false })">
                        <output x-text="Math.round(sel?.opacity * 100) + '%'"></output>
                    </div>
                </label>
                <div class="ps-row">
                    <button type="button" class="ps-btn ps-btn-sm" @click="moveLayer(sel?.id, 'top')">{!! $i('arrow-long-up', 'ps-i-sm') !!} Para a frente</button>
                    <button type="button" class="ps-btn ps-btn-sm" @click="moveLayer(sel?.id, 'bottom')">{!! $i('arrow-long-down', 'ps-i-sm') !!} Para o fundo</button>
                </div>
            </div>
        </div>
    </template>

    {{-- ---------------------------------------------------------- nada selecionado: documento --}}
    <template x-if="selection.length === 0">
        <div>
            <div class="ps-section">
                <div class="ps-section-title">Formato do post <small x-text="doc.width + '×' + doc.height"></small></div>
                <div class="ps-grid-2">
                    <template x-for="(format, key) in formats" :key="key">
                        <button type="button" class="ps-format-btn" @click="changeFormat(key)" :aria-pressed="doc.format === key">
                            <b x-text="format.label"></b>
                            <small x-text="format.width + '×' + format.height"></small>
                        </button>
                    </template>
                </div>
                <div style="display: flex; align-items: flex-end; gap: 6px; margin-top: 10px">
                    <label class="ps-field" style="margin: 0; flex: 1"><span>Largura</span><input id="ps-custom-width" type="number" class="ps-input" x-model.number="customFormat.width" :min="config.customLimits.min" :max="config.customLimits.max"></label>
                    <label class="ps-field" style="margin: 0; flex: 1"><span>Altura</span><input type="number" class="ps-input" x-model.number="customFormat.height" :min="config.customLimits.min" :max="config.customLimits.max"></label>
                    <button type="button" class="ps-btn ps-btn-sm" style="height: 34px" @click="applyCustomFormat()">Aplicar</button>
                </div>
                <p class="ps-hint">Ao mudar o formato, os elementos são reposicionados sem esticar. Você pode desfazer.</p>
            </div>

            <div class="ps-section">
                <div class="ps-section-title">Fundo</div>
                @include($color, ['label' => 'Cor de fundo', 'model' => 'doc.background', 'set' => '(v) => setBackground(v)', 'transparent' => true])
            </div>

            <div class="ps-section">
                <div class="ps-section-title">Paleta <button type="button" class="ps-link" @click="tool = 'colors'; leftOpen = true">Editar cores</button></div>
                <button type="button" class="ps-palette" @click="tool = 'colors'; leftOpen = true">
                    <span class="ps-palette-strip">
                        <template x-for="role in ['primary', 'secondary', 'accent', 'dark', 'background']" :key="role"><i :style="{ background: doc.palette.colors[role] }"></i></template>
                    </span>
                    <span><b x-text="doc.palette.name"></b><small>Clique para trocar</small></span>
                </button>
            </div>

            <div class="ps-section">
                <div class="ps-section-title">Legenda do post <button type="button" class="ps-link" @click="copyCaption()" :disabled="!doc.caption">Copiar</button></div>
                <textarea class="ps-textarea" rows="7" :value="doc.caption" @input="setCaption($event.target.value)" placeholder="Texto para acompanhar a publicação, com hashtags." aria-label="Legenda do post"></textarea>
                <p class="ps-hint">A legenda não entra na imagem; copie e cole ao publicar.</p>
            </div>
        </div>
    </template>
</div>
</template>

{{-- ==================================================================== VARIAÇÕES --}}
<template x-if="rightTab === 'variations'">
<div>
    <div class="ps-section">
        <div class="ps-section-title">Variações de cores</div>
        <p class="ps-hint" style="margin: 0 0 10px">A mesma arte em cada paleta. Clique para aplicar aqui, ou salve como uma versão separada.</p>
        <div class="ps-grid-2">
            <template x-for="palette in palettes" :key="palette.id">
                <div class="ps-card" :aria-pressed="doc.palette.id === palette.id">
                    <button type="button" class="ps-thumb" style="padding: 0" :style="{ aspectRatio: doc.width + ' / ' + doc.height }" @click="applyPalette(palette)" :aria-label="'Aplicar paleta ' + palette.name">
                        <template x-if="variationPreviews[palette.id]"><img :src="variationPreviews[palette.id]" alt=""></template>
                        <template x-if="!variationPreviews[palette.id]"><span class="ps-skeleton"></span></template>
                    </button>
                    <span class="ps-thumb-actions" style="top: 5px">
                        <button type="button" @click="saveVariation(palette)" title="Salvar como variação" aria-label="Salvar como variação">{!! $i('document-duplicate', 'ps-i-sm') !!}</button>
                    </span>
                    <b x-text="palette.name"></b>
                    <small x-text="palette.description"></small>
                </div>
            </template>
        </div>
    </div>

    <div class="ps-section">
        <div class="ps-section-title">Versões desta arte <small x-text="family.length"></small></div>
        <template x-for="item in family" :key="item.id">
            <a :href="item.url" class="ps-list-item" :class="{ 'is-current': item.current }" style="text-decoration: none; color: inherit">
                <span class="ps-thumb" style="width: 44px; flex: none; border-radius: 7px">
                    <template x-if="item.thumbnail"><img :src="item.thumbnail" alt=""></template>
                </span>
                <span class="grow"><b x-text="item.label"></b><small x-text="item.current ? 'Aberta agora' : (item.main ? 'Versão principal' : 'Abrir')"></small></span>
                <template x-if="item.main"><span class="ps-chip linked">principal</span></template>
            </a>
        </template>
        <template x-if="family.some((item) => item.current && !item.main)">
            <button type="button" class="ps-btn ps-btn-sm ps-btn-block" @click="setAsMain()">Definir esta versão como principal</button>
        </template>
    </div>
</div>
</template>

{{-- ==================================================================== EXPORTAR --}}
<template x-if="rightTab === 'export'">
<div>
    <div class="ps-section">
        <div class="ps-section-title">Arquivo</div>
        <div class="ps-field">
            <span>Formato</span>
            <div class="ps-seg">
                <button type="button" :aria-pressed="exportOptions.format === 'png'" @click="exportOptions.format = 'png'">PNG</button>
                <button type="button" :aria-pressed="exportOptions.format === 'jpeg'" @click="exportOptions.format = 'jpeg'; exportOptions.transparent = false">JPEG</button>
            </div>
            <p class="ps-hint" x-text="exportOptions.format === 'png' ? 'Máxima qualidade, ideal para textos e logos.' : 'Arquivo menor, bom para fotos e WhatsApp.'"></p>
        </div>
        <template x-if="exportOptions.format === 'jpeg'">
            <label class="ps-field"><span>Qualidade</span>
                <div class="ps-range">
                    <input type="range" min="0.6" max="1" step="0.02" x-model.number="exportOptions.quality">
                    <output x-text="Math.round(exportOptions.quality * 100) + '%'"></output>
                </div>
            </label>
        </template>
        <div class="ps-field">
            <span>Resolução</span>
            <div class="ps-seg">
                <button type="button" :aria-pressed="exportOptions.scale === 1" @click="exportOptions.scale = 1" x-text="'1× · ' + doc.width + '×' + doc.height"></button>
                <button type="button" :aria-pressed="exportOptions.scale === 2" @click="exportOptions.scale = 2" x-text="'2× · ' + doc.width * 2 + '×' + doc.height * 2"></button>
            </div>
            <p class="ps-hint">Redes sociais usam 1×. O tamanho independe do zoom da tela.</p>
        </div>
        <template x-if="exportOptions.format === 'png'">
            <label class="ps-switch"><span>Fundo transparente</span><input type="checkbox" x-model="exportOptions.transparent"></label>
        </template>
        <label class="ps-field"><span>Nome do arquivo</span>
            <input class="ps-input" x-model="exportOptions.filename" maxlength="60">
        </label>

        <button type="button" class="ps-btn ps-btn-primary ps-btn-block ps-btn-lg" @click="exportImage()" :disabled="exporting || !ready">
            <template x-if="!exporting"><span style="display: inline-flex; gap: 8px; align-items: center">{!! $i('arrow-down-tray') !!} Baixar imagem</span></template>
            <template x-if="exporting"><span>Gerando arquivo…</span></template>
        </button>
    </div>

    <template x-if="exportError">
        <div class="ps-alert danger" role="alert" x-text="exportError"></div>
    </template>
    <template x-if="exportResult">
        <div>
            <div class="ps-alert success" role="status">
                <b x-text="exportResult.filename"></b><br>
                <span x-text="exportResult.width + '×' + exportResult.height + ' px · ' + exportResult.size + ' · conferido'"></span>
            </div>
            <div class="ps-export-preview"><img :src="exportResult.preview" alt="Prévia do arquivo exportado"></div>
        </div>
    </template>

    <div class="ps-section">
        <div class="ps-section-title">Legenda <button type="button" class="ps-link" @click="copyCaption()" :disabled="!doc.caption">Copiar</button></div>
        <p class="ps-hint" style="margin: 0; white-space: pre-line; max-height: 160px; overflow: auto" x-text="doc.caption || 'Sem legenda. Escreva uma na aba Design.'"></p>
    </div>
</div>
</template>

</div>
