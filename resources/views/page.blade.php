<x-layouts.app 
    :title="$meta['title']" 
    :description="$meta['description']"
    :keywords="$meta['keywords'] ?? null"
    :ogImage="$meta['og_image'] ?? null"
    :builder-preview="$builderPreview ?? false"
    :site-preview="$sitePreview ?? false"
>
    @php
        $content = $page->content ?? [];
        $firstBlock = $content[0] ?? null;
        $hasHeader = collect($content)->contains('type', 'page_header');
        $isHome = $page->slug === '/' || $page->slug === '';
        
        $websiteSettings = \App\Models\Setting::get('website', []);
        $pageTheme = data_get($websiteSettings, 'theme', 'default');

        // Dados base para o cabeçalho automático
        $autoHeader = [
            'title' => $page->title,
            'badge' => null,
            'description' => data_get($page->meta, 'description'),
        ];

        // Se for página interna sem header manual, e o primeiro bloco for 'services',
        // nós "promovemos" as informações do bloco para o cabeçalho principal.
        if (!$isHome && !$hasHeader && $firstBlock && $firstBlock['type'] === 'services') {
            $autoHeader['title'] = $firstBlock['data']['title'] ?? $page->title;
            $autoHeader['badge'] = $firstBlock['data']['badge'] ?? null;
            $autoHeader['description'] = $firstBlock['data']['description'] ?? $autoHeader['description'];
        }
    @endphp

    {{-- 
        Injeção Automática de Cabeçalho:
        Se não for a homepage e não houver um bloco de 'page_header' definido,
        renderizamos um cabeçalho padrão ou promovido.
    --}}
    @if(!$isHome && !$hasHeader)
        <x-page-header
            :badge="$autoHeader['badge']"
            :title="$autoHeader['title']"
            :description="$autoHeader['description']"
            :breadcrumbs="[['label' => $autoHeader['title']]]"
        />
    @endif



    @php
        // Modo Live Preview do Page Builder (painel administrativo)
        $builderPreview = $builderPreview ?? false;
        $blockKeys = $blockKeys ?? [];
        $blockLabels = $blockLabels ?? [];
    @endphp

    @foreach($content as $block)
        @if($builderPreview)
            @php $pbUuid = $blockKeys[$loop->index] ?? ''; @endphp
            <div class="pb-block" data-pb-block="{{ $pbUuid }}" data-pb-label="{{ $blockLabels[$pbUuid] ?? $block['type'] }}">
                @php
                    try {
                        $pbHtml = \Illuminate\Support\Facades\Blade::render(
                            '<x-render-block :type="$type" :data="$data" :page="$page" :theme="$theme" :hide-header="$hideHeader" />',
                            [
                                'type' => $block['type'],
                                'data' => $block['data'] ?? [],
                                'page' => $page,
                                'theme' => $pageTheme,
                                'hideHeader' => (!$isHome && !$hasHeader && $loop->first),
                            ],
                        );
                        $pbError = null;
                    } catch (\Throwable $e) {
                        $pbHtml = '';
                        $pbError = $e->getMessage();
                    }
                @endphp
                @if($pbError)
                    <div class="pb-block-error">
                        <strong>Não foi possível renderizar "{{ $blockLabels[$pbUuid] ?? $block['type'] }}"</strong>
                        <span>Complete os campos do bloco. ({{ \Illuminate\Support\Str::limit($pbError, 160) }})</span>
                    </div>
                @else
                    {!! $pbHtml !!}
                @endif
            </div>
        @else
            <x-render-block 
                :type="$block['type']" 
                :data="$block['data']" 
                :page="$page" 
                :theme="$pageTheme" 
                :hide-header="(!$isHome && !$hasHeader && $loop->first)"
            />
        @endif
    @endforeach

    @if($builderPreview)
        @if(empty($content))
            <div class="pb-empty">
                <div class="pb-empty-icon">+</div>
                <h2>Sua página está vazia</h2>
                <p>Adicione blocos pela coluna da esquerda para começar a construir.</p>
            </div>
        @endif
        @include('filament.page-builder.preview-bridge')
    @endif
</x-layouts.app>
