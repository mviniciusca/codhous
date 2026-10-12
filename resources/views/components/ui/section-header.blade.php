{{--
    Cabeçalho de seção: kicker + título + descrição, com slot opcional de ações.

    Respeita header.visible e header.alignment do bloco. Com alinhamento à
    esquerda, as ações (ex.: "Ver todos") ficam na mesma linha do título em
    telas largas, aproveitando o espaço lateral.
--}}
@props([
    'header' => [],
    'fallbackTitle' => null,
    'fallbackSubtitle' => null,
    'fallbackDescription' => null,
    // Força um alinhamento (left, center, right); sem valor usa header.alignment
    'align' => null,
    'defaultAlign' => 'left',
    'as' => 'h2',
    // Mantidos por compatibilidade; o esquema de cor agora vem da seção
    'textColor' => null,
    'badgeBorderClass' => null,
    'badgeBgClass' => null,
    'badgeTextClass' => null,
])
@php
    $header = (array) $header;
    $isVisible = $header['visible'] ?? true;
    $alignment = $align ?: ($header['alignment'] ?? $defaultAlign);
    $title = filled($header['title'] ?? null) ? $header['title'] : $fallbackTitle;
    $subtitle = filled($header['subtitle'] ?? null) ? $header['subtitle'] : $fallbackSubtitle;
    $description = filled($header['description'] ?? null) ? $header['description'] : $fallbackDescription;
    $hasActions = isset($actions) && $actions->isNotEmpty();

    $textAlign = match ($alignment) {
        'center' => 'items-center text-center mx-auto',
        'right' => 'items-end text-right ml-auto',
        default => 'items-start text-left',
    };
@endphp

@if($isVisible && (filled($title) || filled($subtitle)))
    <div {{ $attributes->class([
        'mb-10 md:mb-12',
        'flex flex-col gap-6 md:flex-row md:items-end md:justify-between' => $hasActions && $alignment === 'left',
    ]) }}>
        <div class="flex max-w-2xl flex-col {{ $textAlign }}" data-reveal>
            @if(filled($subtitle))
                <span class="kicker kicker-pill mb-4">
                    <span class="kicker-dot"></span>
                    {!! $subtitle !!}
                </span>
            @endif

            @if(filled($title))
                <{{ $as }} class="section-title">{!! $title !!}</{{ $as }}>
            @endif

            @if(filled($description))
                <p class="section-lead mt-4">{!! $description !!}</p>
            @endif
        </div>

        @if($hasActions)
            <div @class([
                'flex shrink-0 flex-wrap items-center gap-3',
                'mt-6 justify-center' => $alignment === 'center',
                'mt-6 justify-end' => $alignment === 'right',
            ]) data-reveal style="--reveal-delay: 120ms">
                {{ $actions }}
            </div>
        @endif
    </div>
@endif
