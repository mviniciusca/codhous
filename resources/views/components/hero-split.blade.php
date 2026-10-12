@props(['title', 'subtitle', 'image', 'features', 'buttonLabel', 'buttonUrl'])

@php
    $imageUrl = function_exists('resolveFilamentImagePath') ? resolveFilamentImagePath($image) : null;
@endphp

<section class="relative isolate overflow-hidden bg-background">
    <div class="absolute -left-32 top-1/3 -z-10 h-80 w-80 rounded-full bg-primary-500/10 blur-3xl"></div>

    <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-14 sm:px-6 md:py-20 lg:grid-cols-2 lg:gap-16 lg:px-8">
        <div data-reveal>
            <h1 class="font-mono text-4xl font-bold leading-[1.05] tracking-tight text-foreground md:text-5xl lg:text-6xl" style="text-wrap: balance;">{{ $title }}</h1>

            @if($subtitle)
                <p class="section-lead mt-5 max-w-xl">{{ $subtitle }}</p>
            @endif

            @if(!empty($features))
                <ul class="mt-8 grid gap-3 sm:grid-cols-2">
                    @foreach($features as $feature)
                        <li class="flex items-center gap-3 text-sm font-medium text-foreground/80">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary-500/10 text-primary-ink">
                                <i data-lucide="check" class="h-3.5 w-3.5"></i>
                            </span>
                            {{ $feature['item'] ?? '' }}
                        </li>
                    @endforeach
                </ul>
            @endif

            @if($buttonLabel)
                <x-ui.button :href="$buttonUrl ?? '#'" size="lg" trailing-icon="arrow-right" class="mt-10">{{ $buttonLabel }}</x-ui.button>
            @endif
        </div>

        @if($imageUrl)
            <div class="relative" data-reveal style="--reveal-delay: 120ms">
                <div class="absolute -inset-3 -z-10 rotate-2 rounded-[2rem] bg-gradient-to-br from-primary-400/30 to-primary-700/20"></div>
                <img src="{{ $imageUrl }}" alt="{{ $title }}" class="aspect-[4/3] w-full rounded-[1.75rem] object-cover shadow-2xl">
            </div>
        @endif
    </div>
</section>
