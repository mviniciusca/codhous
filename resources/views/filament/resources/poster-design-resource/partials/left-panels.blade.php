@php($i = fn (string $name, string $class = 'ps-i') => svg('heroicon-o-'.$name, $class, ['aria-hidden' => 'true'])->toHtml())

{{-- ================================================================ Templates --}}
<template x-if="tool === 'templates'">
    <div style="display: contents">
        <div class="ps-panel-head">
            <h2>Templates</h2>
            <button type="button" class="ps-icon-btn" @click="leftOpen = false" aria-label="Fechar painel">{!! $i('x-mark') !!}</button>
        </div>
        <div class="ps-scroll">
            <p class="ps-hint" style="margin: 0 0 12px">Composições editáveis com os dados da sua empresa. Aplicar mantém o formato e a paleta atuais.</p>
            <div class="ps-grid-2">
                <template x-for="template in templates" :key="template.id">
                    <button type="button" class="ps-card" @click="applyTemplate(template)" :aria-pressed="doc.templateId === template.id">
                        <span class="ps-thumb">
                            <template x-if="templatePreviews[template.id]"><img :src="templatePreviews[template.id]" :alt="'Prévia: ' + template.name"></template>
                            <template x-if="!templatePreviews[template.id]"><span class="ps-skeleton"></span></template>
                        </span>
                        <b x-text="template.name"></b>
                        <small x-text="template.category"></small>
                    </button>
                </template>
            </div>
        </div>
    </div>
</template>

{{-- ================================================================ Texto --}}
<template x-if="tool === 'text'">
    <div style="display: contents">
        <div class="ps-panel-head">
            <h2>Texto</h2>
            <button type="button" class="ps-icon-btn" @click="leftOpen = false" aria-label="Fechar painel">{!! $i('x-mark') !!}</button>
        </div>
        <div class="ps-scroll">
            <button type="button" class="ps-text-preset" @click="addElement('heading')"><span style="font: 900 26px/1.1 Montserrat">Adicionar título</span></button>
            <button type="button" class="ps-text-preset" @click="addElement('subheading')"><span style="font: 600 18px/1.2 Inter">Adicionar subtítulo</span></button>
            <button type="button" class="ps-text-preset" @click="addElement('body')"><span style="font: 400 14px/1.35 Inter">Adicionar um parágrafo de texto</span></button>
            <button type="button" class="ps-text-preset" @click="addElement('label')"><span style="font: 800 12px/1 Inter; letter-spacing: .25em; color: var(--ps-accent)">ADICIONAR ETIQUETA</span></button>

            <div class="ps-section">
                <div class="ps-section-title">Dados da empresa <small>ficam vinculados</small></div>
                <template x-for="key in ['brand.phone', 'brand.whatsapp', 'brand.instagram', 'brand.website', 'brand.email', 'brand.address', 'brand.hours']" :key="key">
                    <template x-if="bindValue(key)">
                        <button type="button" class="ps-list-item" @click="useText(bindValue(key), key)">
                            <span class="grow"><b x-text="bindLabel(key)"></b><small x-text="bindValue(key)"></small></span>
                            {!! $i('plus', 'ps-i-sm') !!}
                        </button>
                    </template>
                </template>
            </div>
        </div>
    </div>
</template>

{{-- ================================================================ Imagens --}}
<template x-if="tool === 'images'">
    <div style="display: contents">
        <div class="ps-panel-head">
            <h2>Imagens</h2>
            <button type="button" class="ps-icon-btn" @click="leftOpen = false; imageTarget = null" aria-label="Fechar painel">{!! $i('x-mark') !!}</button>
        </div>
        <div class="ps-tabs" role="tablist">
            <button type="button" role="tab" :aria-selected="imageSource === 'uploads'" @click="imageSource = 'uploads'">Enviadas</button>
            <button type="button" role="tab" :aria-selected="imageSource === 'company'" @click="imageSource = 'company'">Da empresa</button>
            <button type="button" role="tab" :aria-selected="imageSource === 'equipment'" @click="imageSource = 'equipment'">Equipamentos</button>
        </div>
        <div class="ps-scroll" style="padding-top: 8px">
            <template x-if="imageTarget || sel?.type === 'image'">
                <div class="ps-alert info">
                    Clique numa imagem para <b>substituir</b> a selecionada.
                    <button type="button" class="ps-link" @click="imageTarget = null; clearSelection()">Inserir como nova</button>
                </div>
            </template>

            <template x-if="imageSource === 'uploads'">
                <div>
                    <label class="ps-dropzone" x-data="{ over: false }" :class="{ over }"
                           @dragover.prevent="over = true" @dragleave="over = false"
                           @drop.prevent="over = false; uploadFiles($event.dataTransfer.files)">
                        {!! $i('cloud-arrow-up') !!}
                        <b>Enviar imagens</b>
                        <small x-text="'PNG, JPG ou WebP até ' + Math.round(config.uploads.maxKb / 1024) + ' MB'"></small>
                        <input type="file" class="ps-sr" accept="image/png,image/jpeg,image/webp" multiple @change="uploadFiles($event.target.files); $event.target.value = ''">
                    </label>
                    <template x-if="uploading"><div class="ps-progress" role="progressbar" :aria-valuenow="uploadProgress"><i :style="{ width: uploadProgress + '%' }"></i></div></template>
                    <template x-if="uploadError"><div class="ps-alert danger" x-text="uploadError"></div></template>
                </div>
            </template>

            <input type="search" class="ps-input" placeholder="Buscar pelo nome…" x-model="imageQuery" style="margin-bottom: 12px" aria-label="Buscar imagens">

            <div class="ps-grid-2">
                <template x-for="(img, index) in libraryImages" :key="img.url + index">
                    <div class="ps-card">
                        <button type="button" class="ps-thumb" @click="insertImage(img.url, { bind: img.bind ?? null, name: img.name, fit: img.bind ? 'contain' : 'cover' })" :title="img.name">
                            <img :src="img.url" :alt="img.name" loading="lazy">
                        </button>
                        <template x-if="img.asset">
                            <span class="ps-thumb-actions">
                                <button type="button" @click="deleteAsset(img.asset)" title="Excluir da biblioteca" aria-label="Excluir da biblioteca">{!! $i('trash', 'ps-i-sm') !!}</button>
                            </span>
                        </template>
                        <small style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap" x-text="img.meta ? img.name + ' · ' + img.meta : img.name"></small>
                    </div>
                </template>
            </div>
            <template x-if="!libraryImages.length">
                <div class="ps-empty">{!! $i('photo') !!}
                    <span x-text="imageQuery ? 'Nenhuma imagem com esse nome.' : (imageSource === 'uploads' ? 'Envie fotos da empresa para reutilizar em todas as artes.' : 'Nenhuma imagem disponível ainda.')"></span>
                </div>
            </template>
        </div>
    </div>
</template>

{{-- ================================================================ Elementos --}}
<template x-if="tool === 'elements'">
    <div style="display: contents">
        <div class="ps-panel-head">
            <h2>Elementos</h2>
            <button type="button" class="ps-icon-btn" @click="leftOpen = false" aria-label="Fechar painel">{!! $i('x-mark') !!}</button>
        </div>
        <div class="ps-scroll">
            <div class="ps-section">
                <div class="ps-section-title">Formas</div>
                <div class="ps-grid-3">
                    <button type="button" class="ps-tile" @click="addElement('rect')"><span class="ps-shape" style="width: 30px; height: 22px"></span>Retângulo</button>
                    <button type="button" class="ps-tile" @click="addElement('rounded')"><span class="ps-shape" style="width: 30px; height: 22px; border-radius: 6px"></span>Cartão</button>
                    <button type="button" class="ps-tile" @click="addElement('pill')"><span class="ps-shape" style="width: 34px; height: 14px; border-radius: 99px"></span>Botão</button>
                    <button type="button" class="ps-tile" @click="addElement('ellipse')"><span class="ps-shape" style="width: 24px; height: 24px; border-radius: 50%"></span>Círculo</button>
                    <button type="button" class="ps-tile" @click="addElement('outline')"><span style="width: 30px; height: 22px; border: 3px solid currentColor; border-radius: 4px"></span>Moldura</button>
                    <button type="button" class="ps-tile" @click="addElement('line')"><span class="ps-shape" style="width: 32px; height: 4px; border-radius: 2px"></span>Linha</button>
                    <button type="button" class="ps-tile" @click="addElement('band')"><span class="ps-shape" style="width: 38px; height: 10px"></span>Faixa</button>
                    <button type="button" class="ps-tile" @click="addQrCode()">{!! $i('qr-code') !!}QR Code</button>
                    <button type="button" class="ps-tile" @click="addElement('image', null)">{!! $i('photo') !!}Espaço de foto</button>
                </div>
            </div>
            <div class="ps-section">
                <div class="ps-section-title">
                    Ícones
                    <small x-show="sel?.type === 'icon'">clique para trocar o selecionado</small>
                </div>
                <input type="search" class="ps-input" placeholder="Buscar ícone (em inglês: phone, truck…)" x-model.debounce.200ms="iconQuery" style="margin-bottom: 10px" aria-label="Buscar ícones">
                <div class="ps-grid-5">
                    <template x-for="name in iconList" :key="name">
                        <button type="button" class="ps-icon-tile" @click="addIcon(name)" :title="iconLabel(name)" :aria-label="'Ícone ' + iconLabel(name)" x-html="iconHtml(name)"></button>
                    </template>
                </div>
                <template x-if="!iconList.length"><p class="ps-hint">Nenhum ícone encontrado.</p></template>
            </div>
        </div>
    </div>
</template>

{{-- ================================================================ Dados --}}
<template x-if="tool === 'data'">
    <div style="display: contents">
        <div class="ps-panel-head">
            <h2>Dados da empresa</h2>
            <button type="button" class="ps-icon-btn" @click="leftOpen = false" aria-label="Fechar painel">{!! $i('x-mark') !!}</button>
        </div>
        <div class="ps-scroll">
            <p class="ps-hint" style="margin: 0 0 12px">
                Vêm das Configurações e do catálogo. Com um texto selecionado, o clique substitui o conteúdo dele; sem seleção, cria um texto novo.
            </p>

            <div class="ps-section">
                <div class="ps-section-title">Identidade
                    <button type="button" class="ps-link" @click="refreshBrand()">Atualizar</button>
                </div>
                <div class="ps-grid-2" style="margin-bottom: 10px">
                    <template x-for="key in ['brand.logo', 'brand.mascot']" :key="key">
                        <button type="button" class="ps-card" @click="bindValue(key) && useBrandImage(key)" :disabled="!bindValue(key)">
                            <span class="ps-thumb" style="aspect-ratio: 16/10">
                                <template x-if="bindValue(key)"><img :src="bindValue(key)" :alt="bindLabel(key)"></template>
                                <template x-if="!bindValue(key)"><small>Não configurado</small></template>
                            </span>
                            <b x-text="bindLabel(key)"></b>
                        </button>
                    </template>
                </div>
                <template x-for="key in ['brand.name', 'brand.phone', 'brand.whatsapp', 'brand.instagram', 'brand.website', 'brand.email', 'brand.address', 'brand.city', 'brand.hours']" :key="key">
                    <button type="button" class="ps-list-item" @click="bindValue(key) && useText(bindValue(key), key)" :disabled="!bindValue(key)">
                        <span class="grow"><b x-text="bindLabel(key)"></b><small x-text="bindValue(key) ?? 'Não configurado nas Configurações'"></small></span>
                        <template x-if="bindValue(key)">{!! $i('plus', 'ps-i-sm') !!}</template>
                    </button>
                </template>
            </div>

            <div class="ps-section">
                <div class="ps-section-title">Produtos e preços</div>
                <template x-for="product in brand.products" :key="product.id">
                    <div x-data="{ open: false }" style="margin-bottom: 6px">
                        <button type="button" class="ps-list-item" style="margin: 0" @click="open = !open" :aria-expanded="open">
                            <span class="grow"><b x-text="product.name"></b><small x-text="product.options.length + ' opções'"></small></span>
                            <span x-show="!open">{!! $i('chevron-down', 'ps-i-sm') !!}</span><span x-show="open">{!! $i('chevron-up', 'ps-i-sm') !!}</span>
                        </button>
                        <div x-show="open" x-collapse style="padding: 6px 0 4px 8px">
                            <button type="button" class="ps-btn ps-btn-sm" style="margin-bottom: 6px" @click="useText(product.name)">Usar nome do produto</button>
                            <template x-for="option in product.options" :key="option.id">
                                <div style="display: flex; align-items: center; gap: 6px; padding: 6px 0; border-top: 1px solid var(--ps-border)">
                                    <span style="flex: 1; min-width: 0"><b style="display: block; font-size: 12px" x-text="option.name"></b><small style="color: var(--ps-muted)" x-text="option.price_formatted + (option.unit ? ' / ' + option.unit : '')"></small></span>
                                    <button type="button" class="ps-btn ps-btn-sm" @click="useText(option.name)" title="Usar o nome">Nome</button>
                                    <button type="button" class="ps-btn ps-btn-sm" @click="useText(option.price_formatted)" title="Usar o preço">Preço</button>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
                <template x-if="!brand.products.length"><p class="ps-hint">Nenhum produto ativo no catálogo.</p></template>
            </div>

            <div class="ps-section">
                <div class="ps-section-title">Equipamentos</div>
                <template x-for="item in brand.equipment" :key="item.id">
                    <div class="ps-list-item">
                        <template x-if="item.image"><img :src="item.image" alt="" style="width: 38px; height: 38px; object-fit: contain; border-radius: 6px; background: var(--ps-panel-2)"></template>
                        <span class="grow"><b x-text="item.name"></b><small x-text="item.category ?? ''"></small></span>
                        <button type="button" class="ps-btn ps-btn-sm" @click="useText(item.name)">Nome</button>
                        <template x-if="item.image"><button type="button" class="ps-btn ps-btn-sm" @click="insertImage(item.image, { name: item.name, fit: 'contain' })">Foto</button></template>
                    </div>
                </template>
                <template x-if="!brand.equipment.length"><p class="ps-hint">Nenhum equipamento cadastrado.</p></template>
            </div>
        </div>
    </div>
</template>

{{-- ================================================================ Cores --}}
<template x-if="tool === 'colors'">
    <div style="display: contents">
        <div class="ps-panel-head">
            <h2>Cores</h2>
            <button type="button" class="ps-icon-btn" @click="leftOpen = false" aria-label="Fechar painel">{!! $i('x-mark') !!}</button>
        </div>
        <div class="ps-scroll">
            <div class="ps-section">
                <div class="ps-section-title">Paleta da arte <small x-text="doc.palette.name"></small></div>
                <p class="ps-hint" style="margin: 0 0 10px">Elementos vinculados mudam juntos. Cores "personalizadas" não são afetadas.</p>
                <div class="ps-roles">
                    <template x-for="(roleLabel, role) in colorRoles" :key="role">
                        <label class="ps-role">
                            <input type="color" class="ps-color-input" :value="doc.palette.colors[role]" @change="setPaletteColor(role, $event.target.value)" :aria-label="roleLabel">
                            <span x-text="roleLabel"></span>
                        </label>
                    </template>
                </div>
                <div style="display: flex; gap: 6px; margin-top: 10px">
                    <input class="ps-input" placeholder="Nome da paleta" x-model="paletteName" maxlength="60" aria-label="Nome da nova paleta">
                    <button type="button" class="ps-btn ps-btn-sm" style="height: auto" @click="savePalette()">Salvar</button>
                </div>
                <button type="button" class="ps-btn ps-btn-sm ps-btn-block" style="margin-top: 8px" @click="extractFromImage()" :disabled="sel?.type !== 'image' || !sel?.src">
                    {!! $i('sparkles', 'ps-i-sm') !!} Extrair cores da imagem selecionada
                </button>
            </div>

            <div class="ps-section">
                <div class="ps-section-title">Paletas</div>
                <div class="ps-palette-list">
                    <template x-for="palette in palettes" :key="palette.id">
                        <div style="position: relative">
                            <button type="button" class="ps-palette" @click="applyPalette(palette)" :aria-pressed="doc.palette.id === palette.id">
                                <span class="ps-palette-strip">
                                    <template x-for="role in ['primary', 'secondary', 'accent', 'dark', 'background']" :key="role"><i :style="{ background: palette.colors[role] }"></i></template>
                                </span>
                                <span><b x-text="palette.name"></b><small x-text="palette.description"></small></span>
                            </button>
                            <template x-if="palette.custom">
                                <button type="button" class="ps-icon-btn danger" style="position: absolute; right: 6px; top: 8px" @click="deletePalette(palette)" aria-label="Excluir paleta">{!! $i('trash', 'ps-i-sm') !!}</button>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

{{-- ================================================================ Camadas --}}
<template x-if="tool === 'layers'">
    <div style="display: contents">
        <div class="ps-panel-head">
            <h2>Camadas</h2>
            <button type="button" class="ps-icon-btn" @click="leftOpen = false" aria-label="Fechar painel">{!! $i('x-mark') !!}</button>
        </div>
        <div class="ps-scroll">
            <p class="ps-hint" style="margin: 0 0 10px">A primeira da lista fica na frente. Arraste para reordenar; Shift+clique seleciona várias.</p>
            <template x-for="layer in layers" :key="layer.id">
                <div class="ps-layer" :class="{ 'is-hidden': layer.hidden, 'drag-over': dragOver === layer.id }"
                     :aria-selected="selection.includes(layer.id)" role="option" tabindex="0"
                     draggable="true"
                     @dragstart="dragLayer = layer.id" @dragend="dragLayer = null; dragOver = null"
                     @dragover.prevent="dragOver = layer.id" @drop.prevent="dragOver = null; dropLayer(layer.id)"
                     @click="selectLayer(layer.id, $event)" @keydown.enter="selectLayer(layer.id, $event)">
                    <span class="ps-layer-type" x-html="layerIcon(layer.type)"></span>
                    <span class="ps-layer-name" x-text="layer.name" :title="layer.name" @dblclick.stop="const n = prompt('Nome da camada', layer.name); n !== null && renameLayer(layer.id, n)"></span>
                    <template x-if="layer.bind"><span class="ps-chip linked" title="Vinculado aos dados da empresa">dado</span></template>
                    <template x-if="layer.out"><span class="ps-chip warn" title="Parte do elemento está fora da arte">fora</span></template>
                    <template x-if="layer.placeholder"><span class="ps-chip warn" title="Espaço de imagem sem foto">vazio</span></template>
                    <button type="button" class="ps-icon-btn" @click.stop="toggleLayer(layer.id, 'locked')" :aria-pressed="layer.locked" :aria-label="layer.locked ? 'Desbloquear' : 'Bloquear'" :title="layer.locked ? 'Desbloquear' : 'Bloquear'">
                        <span x-show="layer.locked">{!! $i('lock-closed', 'ps-i-sm') !!}</span><span x-show="!layer.locked">{!! $i('lock-open', 'ps-i-sm') !!}</span>
                    </button>
                    <button type="button" class="ps-icon-btn" @click.stop="toggleLayer(layer.id, 'hidden')" :aria-label="layer.hidden ? 'Mostrar' : 'Ocultar'" :title="layer.hidden ? 'Mostrar' : 'Ocultar'">
                        <span x-show="layer.hidden">{!! $i('eye-slash', 'ps-i-sm') !!}</span><span x-show="!layer.hidden">{!! $i('eye', 'ps-i-sm') !!}</span>
                    </button>
                </div>
            </template>
            <template x-if="!layers.length">
                <div class="ps-empty">{!! $i('square-3-stack-3d') !!}A arte ainda não tem elementos.</div>
            </template>
        </div>
    </div>
</template>
