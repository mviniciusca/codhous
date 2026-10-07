@props([
    'action',
    'afterItem' => null,
    'blocks',
    'columns' => null,
    'statePath',
    'trigger',
    'width' => null,
])

<x-filament::modal
    width="6xl"
    {{ $attributes->class(['fi-fo-builder-block-picker']) }}
>
    <x-slot name="trigger">
        {{ $trigger }}
    </x-slot>

    <x-slot name="heading">
        Inserir entre blocos
    </x-slot>

    <x-slot name="description">
        Escolha um bloco para adicionar à sua página. Todos os blocos são personalizáveis.
    </x-slot>

    @php
        $categories = [
            'Essenciais' => ['hero', 'hero_simple', 'hero_split', 'page_header', 'rich_text', 'image_with_text', 'cta', 'data_table'],
            'Comercial' => ['equipment_showcase', 'payment_offer', 'calculator', 'budget_form', 'commercial_partners'],
            'Institucional' => ['showcase', 'team', 'partners', 'timeline', 'faq', 'testimonials', 'coverage', 'differentials'],
            'Mídia e Integrações' => ['contact_banner', 'module_reference', 'stats', 'cards', 'featured_testimonial', 'map']
        ];

        $blockDescriptions = [
            'hero' => 'Destaque principal no topo da página.',
            'page_header' => 'Cabeçalho da página com título e descrição.',
            'rich_text' => 'Conteúdo rico com texto e imagens.',
            'image_with_text' => 'Layout em colunas com imagem e texto.',
            'cta' => 'Botões personalizados e chamadas para ação.',
            'data_table' => 'Exibição de dados em tabela.',
            'equipment_showcase' => 'Lista de equipamentos com filtros.',
            'payment_offer' => 'Condições e formas de pagamento.',
            'calculator' => 'Ferramenta interativa de cálculos.',
            'showcase' => 'Galeria de projetos realizados.',
            'team' => 'Apresentação da equipe da empresa.',
            'commercial_partners' => 'Logos de parceiros e marcas.',
            'module_reference' => 'Seção pronta global reutilizável.',
        ];

        $groupedBlocks = [];
        foreach ($blocks as $block) {
            $name = $block->getName();
            $assigned = false;
            foreach ($categories as $catName => $keys) {
                if (in_array($name, $keys)) {
                    $groupedBlocks[$catName][] = $block;
                    $assigned = true;
                    break;
                }
            }
            if (!$assigned) {
                $groupedBlocks['Outros'][] = $block;
            }
        }
    @endphp

    <style>
        .picker-container { display: flex; gap: 2rem; margin-top: 1rem; width: 100%; align-items: flex-start; }
        .picker-sidebar { width: 240px; flex-shrink: 0; display: flex; flex-direction: column; gap: 0.25rem; }
        .picker-sidebar-title { font-weight: 700; margin-bottom: 0.75rem; color: var(--gray-900); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; }
        .dark .picker-sidebar-title { color: var(--gray-100); }
        .picker-cat-btn { display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0.75rem; border-radius: 0.5rem; text-align: left; font-size: 0.875rem; color: var(--gray-600); transition: all 0.2s; border: none; background: transparent; cursor: pointer; width: 100%; }
        .dark .picker-cat-btn { color: var(--gray-400); }
        .picker-cat-btn:hover { background-color: var(--gray-100); color: var(--gray-900); }
        .dark .picker-cat-btn:hover { background-color: rgba(255,255,255,0.05); color: #fff; }
        .picker-cat-badge { padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.7rem; background-color: var(--gray-200); color: var(--gray-600); font-weight: 600; }
        .dark .picker-cat-badge { background-color: rgba(255,255,255,0.1); color: var(--gray-400); }
        
        .picker-content { flex: 1; max-height: 65vh; overflow-y: auto; padding-right: 0.5rem; }
        .picker-section { margin-bottom: 2.5rem; }
        .picker-section-title { font-size: 1rem; font-weight: 700; color: var(--gray-900); margin-bottom: 1rem; display: flex; justify-content: space-between; border-bottom: 1px solid var(--gray-200); padding-bottom: 0.5rem; }
        .dark .picker-section-title { color: #fff; border-bottom-color: rgba(255,255,255,0.1); }
        
        .picker-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; }
        
        .picker-card { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 1.5rem 1rem; border-radius: 1rem; border: 1px solid var(--gray-200); background-color: var(--gray-50); cursor: pointer; transition: all 0.2s; }
        .dark .picker-card { background-color: rgba(255,255,255,0.02); border-color: rgba(255,255,255,0.05); }
        .picker-card:hover { border-color: var(--primary-500); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); transform: translateY(-2px); }
        .dark .picker-card:hover { background-color: rgba(255,255,255,0.05); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2); }
        
        .picker-card-icon { width: 40px; height: 40px; margin-bottom: 0.75rem; color: var(--gray-500); transition: color 0.2s; display: flex; align-items: center; justify-content: center; }
        .picker-card-icon svg { width: 100%; height: 100%; stroke-width: 1.5; }
        .dark .picker-card-icon { color: var(--gray-400); }
        .picker-card:hover .picker-card-icon { color: var(--primary-500); }
        
        .picker-card-title { font-weight: 700; font-size: 0.85rem; color: var(--gray-900); margin-bottom: 0.25rem; width: 100%; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.2; }
        .dark .picker-card-title { color: #fff; }
        .picker-card-desc { font-size: 0.7rem; color: var(--gray-500); line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .dark .picker-card-desc { color: var(--gray-400); }
    </style>

    <div class="picker-container" x-data="{ activeTab: 'Todos' }">
        <!-- Sidebar -->
        <div class="picker-sidebar">
            <div class="picker-sidebar-title">Categorias</div>
            
            <button type="button" class="picker-cat-btn" x-on:click="activeTab = 'Todos'" x-bind:style="activeTab === 'Todos' ? 'background-color: var(--primary-500); color: white;' : ''">
                <span>Todos os blocos</span>
                <span class="picker-cat-badge" x-bind:style="activeTab === 'Todos' ? 'background-color: rgba(255,255,255,0.2); color: white;' : ''">{{ count($blocks) }}</span>
            </button>
            
            @foreach($groupedBlocks as $cat => $catBlocks)
                <button type="button" class="picker-cat-btn" x-on:click="activeTab = '{{ $cat }}'" x-bind:style="activeTab === '{{ $cat }}' ? 'background-color: var(--primary-500); color: white;' : ''">
                    <span>{{ $cat }}</span>
                    <span class="picker-cat-badge" x-bind:style="activeTab === '{{ $cat }}' ? 'background-color: rgba(255,255,255,0.2); color: white;' : ''">{{ count($catBlocks) }}</span>
                </button>
            @endforeach
        </div>

        <!-- Block Grid -->
        <div class="picker-content">
            @foreach($groupedBlocks as $cat => $catBlocks)
                <div class="picker-section" x-show="activeTab === 'Todos' || activeTab === '{{ $cat }}'">
                    <div class="picker-section-title" x-show="activeTab === 'Todos'">
                        <span>{{ $cat }}</span>
                    </div>
                    <div class="picker-grid">
                        @foreach($catBlocks as $block)
                            @php
                                $wireClickActionArguments = ['block' => $block->getName()];
                                if (filled($afterItem)) {
                                    $wireClickActionArguments['afterItem'] = $afterItem;
                                }
                                $wireClickActionArguments = \Illuminate\Support\Js::from($wireClickActionArguments);
                                $wireClickAction = "mountFormComponentAction('{$statePath}', '{$action->getName()}', {$wireClickActionArguments})";
                                
                                $desc = $blockDescriptions[$block->getName()] ?? 'Adicione este bloco à página.';
                            @endphp

                            <div class="picker-card" x-on:click="close; $wire.{{ $wireClickAction }}">
                                <div class="picker-card-icon">
                                    <x-filament::icon :icon="$block->getIcon()" />
                                </div>
                                <div class="picker-card-title">{{ $block->getLabel() }}</div>
                                <div class="picker-card-desc">{{ $desc }}</div>
                            </div>

                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament::modal>
