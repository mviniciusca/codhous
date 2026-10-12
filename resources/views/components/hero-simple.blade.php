@props(['title', 'subtitle', 'image', 'primaryButtonLabel', 'primaryButtonUrl', 'secondaryButtonLabel', 'secondaryButtonUrl'])

@php
    $imageUrl = function_exists('resolveFilamentImagePath') ? resolveFilamentImagePath($image) : null;
@endphp

<section class="dark relative isolate flex min-h-[60vh] items-center overflow-hidden bg-primary-950 py-20 text-white">
    @if($imageUrl)
        <img src="{{ $imageUrl }}" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover opacity-40">
    @endif
    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-black via-black/60 to-primary-950/40"></div>
    <div class="absolute left-1/2 top-0 -z-10 h-96 w-[48rem] -translate-x-1/2 -translate-y-1/2 rounded-full bg-primary-500/30 blur-3xl"></div>

    <div class="mx-auto w-full max-w-4xl px-4 text-center sm:px-6 lg:px-8" data-reveal>
        <h1 class="font-mono text-4xl font-bold leading-[1.05] tracking-tight md:text-6xl lg:text-7xl" style="text-wrap: balance;">{{ $title }}</h1>

        @if($subtitle)
            <p class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-white/70 md:text-xl">{{ $subtitle }}</p>
        @endif

        @if($primaryButtonLabel || $secondaryButtonLabel)
            <div class="mt-10 flex flex-col items-center justify-center gap-3 sm:flex-row">
                @if($primaryButtonLabel)
                    <x-ui.button :href="$primaryButtonUrl ?? '#'" size="lg" trailing-icon="arrow-right" class="w-full sm:w-auto">{{ $primaryButtonLabel }}</x-ui.button>
                @endif
                @if($secondaryButtonLabel)
                    <x-ui.button :href="$secondaryButtonUrl ?? '#'" variant="secondary" size="lg" class="w-full backdrop-blur sm:w-auto">{{ $secondaryButtonLabel }}</x-ui.button>
                @endif
            </div>
        @endif
    </div>
</section>
