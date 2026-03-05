@php
    $activeHero = \App\Models\ContentSection::getActiveHero();
    if ($activeHero && !empty($activeHero->content['slideshow'])) {
        $slideshow = $activeHero->content['slideshow'];
        $mainSlide = $slideshow[0] ?? [];
        $mainSlide = array_merge(['title' => '', 'subtitle' => ''], $mainSlide);
        $heroBadge = $activeHero->content['badge'] ?? 'Qualidade Certificada';
        $heroLayout = $activeHero->content['layout'] ?? \App\Models\ContentSection::HERO_LAYOUT_DEFAULT;
        $heroStats = $activeHero->content['stats'] ?? [
            ['value' => '500+', 'label' => 'Obras atendidas'],
            ['value' => '98%', 'label' => 'Pontualidade'],
            ['value' => '15+', 'label' => 'Anos de experiência'],
        ];
    } else {
        $website = \App\Models\Setting::get('website', []);
        $homepage = data_get($website, 'homepage', []);
        $slideshow = data_get($homepage, 'slideshow', []);
        $mainSlide = count($slideshow) > 0 ? array_merge(['title' => '', 'subtitle' => ''], $slideshow[0]) : [
            'title' => 'Concreto usinado com agilidade e precisão no traço',
            'subtitle' => 'Entrega rápida, rastreamento em tempo real e suporte técnico especializado para garantir o sucesso da sua obra do início ao fim.',
        ];
        $heroBadge = 'Qualidade Certificada';
        $heroLayout = \App\Models\ContentSection::HERO_LAYOUT_DEFAULT;
        $heroStats = [
            ['value' => '500+', 'label' => 'Obras atendidas'],
            ['value' => '98%', 'label' => 'Pontualidade'],
            ['value' => '15+', 'label' => 'Anos de experiência'],
        ];
    }
@endphp
<livewire:section-hero-cep
    :main-slide="$mainSlide"
    :badge="$heroBadge"
    :layout="$heroLayout"
    :stats="$heroStats"
/>
<x-section-partners />
<x-section-services />
<x-section-timeline />
<livewire:calculator />
<x-section-quote-form />
<x-section-differentials />
<x-section-faq />
<x-section-testimonials />
<x-section-coverage />
<x-section-cta-contact />
