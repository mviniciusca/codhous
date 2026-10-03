@props([
    'header' => [],
    'fallbackTitle' => null,
    'fallbackSubtitle' => null,
    'fallbackDescription' => null,
    'textColor' => 'light',
    'badgeBorderClass' => 'border-primary/20 bg-primary/10',
    'badgeBgClass' => 'bg-primary',
    'badgeTextClass' => 'text-primary'
])
@php
    $isVisible = $header['visible'] ?? true;
    $alignment = $header['alignment'] ?? 'left';
    $title = $header['title'] ?? $fallbackTitle;
    $subtitle = $header['subtitle'] ?? $fallbackSubtitle;
    $description = $header['description'] ?? $fallbackDescription;

    $alignClass = $alignment === 'left' ? 'text-left mr-auto items-start' : ($alignment === 'right' ? 'text-right ml-auto items-end' : 'text-center mx-auto items-center');
    $isDark = $textColor === 'dark';
@endphp

@if($isVisible && (!empty($title) || !empty($subtitle)))
<div class="mb-12 flex flex-col max-w-3xl {{ $alignClass }}">
    @if(!empty($subtitle))
        @php
            $currentBadgeBorder = $isDark ? 'border-white/10 bg-black/40' : $badgeBorderClass;
            $currentBadgeText = $isDark ? 'text-primary' : $badgeTextClass;
        @endphp
        <div class="mb-4 inline-flex items-center gap-2 rounded-full border {{ $currentBadgeBorder }} px-4 py-1.5 backdrop-blur-md shadow-lg shadow-primary/5">
            <span class="h-1.5 w-1.5 rounded-full {{ $badgeBgClass }} animate-pulse shadow-md"></span>
            <span class="font-mono text-[10px] font-bold uppercase tracking-[0.2em] {{ $currentBadgeText }}">{!! $subtitle !!}</span>
        </div>
    @endif
    
    @if(!empty($title))
        <h2 class="font-mono text-3xl font-extrabold tracking-tight md:text-4xl drop-shadow-sm mb-4 {{ $isDark ? 'text-white' : 'text-foreground' }}" style="text-wrap: balance;">
            {!! $title !!}
        </h2>
    @endif
    
    @if(!empty($description))
        <p class="text-base md:text-lg font-medium leading-relaxed {{ $isDark ? 'text-white/70' : 'text-muted-foreground' }}" style="text-wrap: balance;">
            {!! $description !!}
        </p>
    @endif
</div>
@endif
