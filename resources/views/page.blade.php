<x-layouts.app :title="$meta['title']" :description="$meta['description']">
    @php
        $content = $page->content ?? [];
        $firstBlock = $content[0] ?? null;
        $hasHeader = collect($content)->contains('type', 'page_header');
        $isHome = $page->slug === '/' || $page->slug === '';

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

    @foreach($content as $block)
        <x-render-block :type="$block['type']" :data="$block['data']" :page="$page" />
    @endforeach
</x-layouts.app>
