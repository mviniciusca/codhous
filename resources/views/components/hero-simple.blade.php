@props(['title', 'subtitle', 'image', 'primaryButtonLabel', 'primaryButtonUrl', 'secondaryButtonLabel', 'secondaryButtonUrl'])

<section class="relative flex min-h-[60vh] items-center justify-center overflow-hidden bg-zinc-950 pt-20 pb-20">
    @if($image)
        <div class="absolute inset-0 z-0">
            <img src="{{ str_starts_with($image, 'http') ? $image : \Illuminate\Support\Facades\Storage::url($image) }}" class="h-full w-full object-cover opacity-40" alt="{{ $title }}">
            <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/60 to-transparent"></div>
        </div>
    @endif

    <div class="relative z-10 mx-auto w-full max-w-4xl px-4 text-center text-white lg:px-8">
        <h1 class="font-mono text-4xl font-extrabold leading-tight tracking-tighter md:text-6xl lg:text-7xl" style="text-wrap: balance;">
            {{ $title }}
        </h1>
        
        @if($subtitle)
            <p class="mt-6 mx-auto max-w-2xl text-lg text-zinc-300 md:text-xl leading-relaxed">
                {{ $subtitle }}
            </p>
        @endif

        <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
            @if($primaryButtonLabel)
                <a href="{{ $primaryButtonUrl ?? '#' }}" class="w-full sm:w-auto rounded-md bg-primary px-8 py-3.5 font-mono text-sm font-bold tracking-widest text-primary-foreground transition-all hover:bg-primary/90 hover:scale-105 shadow-xl">
                    {{ $primaryButtonLabel }}
                </a>
            @endif
            @if($secondaryButtonLabel)
                <a href="{{ $secondaryButtonUrl ?? '#' }}" class="w-full sm:w-auto rounded-md border-2 border-white/20 bg-white/5 px-8 py-3 font-mono text-sm font-bold tracking-widest text-white backdrop-blur-sm transition-all hover:bg-white/10 hover:border-white/40">
                    {{ $secondaryButtonLabel }}
                </a>
            @endif
        </div>
    </div>
</section>
