<x-layouts.app 
    :title="$meta['title']" 
    :description="$meta['description']"
    :keywords="$meta['keywords'] ?? null"
    :ogImage="$meta['og_image'] ?? null"
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

    {{-- Hero Section as a Fixed Theme Component on Home Page --}}
    @if($isHome)
        @php
            $heroData = data_get($websiteSettings, 'hero', []);
            $slides = data_get($heroData, 'slideshow', []);
            $firstSlide = count($slides) > 0 ? $slides[0] : [];
        @endphp
        @if(!empty($firstSlide) || !empty($heroData))
            <livewire:section-hero-cep
                :main-slide="[
                    'title' => $firstSlide['title'] ?? '',
                    'subtitle' => $firstSlide['subtitle'] ?? '',
                    'image' => $firstSlide['image'] ?? null,
                    'video' => $firstSlide['video'] ?? null,
                    'image_alignment' => $heroData['image_alignment'] ?? 'center'
                ]"
                :badge="$heroData['badge'] ?? ''"
                :layout="$heroData['layout'] ?? 'default'"
                :theme="$pageTheme"
                :stats="$heroData['stats'] ?? []"
            />
        @endif
    @endif

    @foreach($content as $block)
        <x-render-block 
            :type="$block['type']" 
            :data="$block['data']" 
            :page="$page" 
            :theme="$pageTheme" 
            :hide-header="(!$isHome && !$hasHeader && $loop->first)"
        />
    @endforeach
</x-layouts.app>
