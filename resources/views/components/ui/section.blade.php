{{--
    Invólucro padrão das seções do site.

    Lê do bloco: background_color, text_color, spacing_top/spacing_bottom,
    imagem/overlay de fundo, custom_id e custom_css_classes. O esquema de cor
    (claro, escuro ou primário) redefine os tokens dentro da seção, então os
    filhos só usam text-foreground, bg-card, text-primary-ink etc.

    <x-ui.section :data="$data" spacing="sm" background="bg-background" container="narrow">…</x-ui.section>
--}}
@props([
    'data' => [],
    'as' => 'section',
    'id' => null,
    // Espaçamento padrão quando o bloco não define o seu (none, xs, sm, md, lg, xl)
    'spacing' => 'md',
    // Fundo padrão quando o bloco não define o seu
    'background' => 'bg-white',
    // default | narrow | wide | none
    'container' => 'default',
    'containerClass' => '',
])
@php
    use App\Support\SectionStyle;

    $data = (array) $data;
    $bg = filled($data['background_color'] ?? null) ? $data['background_color'] : $background;
    $scheme = SectionStyle::scheme($data, $bg);
    $sectionId = $id ?: ($data['custom_id'] ?? null);
    // A imagem de fundo pode "vazar" para a seção anterior; nesse caso não cortamos o overflow
    $pullsUp = (int) ($data['background_image_pull_up'] ?? 0) !== 0;

    $wrapper = match ($container) {
        'narrow' => 'mx-auto max-w-4xl px-4 sm:px-6 lg:px-8',
        'wide' => 'mx-auto max-w-[88rem] px-4 sm:px-6 lg:px-8',
        'none' => '',
        default => 'mx-auto max-w-7xl px-4 sm:px-6 lg:px-8',
    };
@endphp

<{{ $as }}
    @if($sectionId) id="{{ $sectionId }}" @endif
    data-scheme="{{ $scheme }}"
    {{ $attributes->class([
        'relative isolate',
        $bg,
        SectionStyle::schemeClass($scheme),
        SectionStyle::spacing($data, $spacing),
        'overflow-hidden' => ! $pullsUp,
        $data['custom_css_classes'] ?? '',
    ]) }}
>
    <x-ui.section-background :data="$data" />

    @if($container === 'none')
        {{ $slot }}
    @else
        <div class="relative z-10 {{ $wrapper }} {{ $containerClass }}">
            {{ $slot }}
        </div>
    @endif
</{{ $as }}>
