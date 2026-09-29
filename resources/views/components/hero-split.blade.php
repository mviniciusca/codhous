@props(['title', 'subtitle', 'image', 'features', 'buttonLabel', 'buttonUrl'])

<section class="relative flex min-h-[70vh] items-center bg-white overflow-hidden">
    <div class="mx-auto w-full max-w-7xl">
        <div class="flex flex-col lg:flex-row">
            
            {{-- Text Content (Left) --}}
            <div class="flex-1 px-4 py-16 lg:px-8 xl:px-16 flex flex-col justify-center">
                <h1 class="font-sans text-4xl font-black leading-[1.1] tracking-tight text-zinc-900 md:text-5xl lg:text-6xl" style="text-wrap: balance;">
                    {{ $title }}
                </h1>
                
                @if($subtitle)
                    <p class="mt-6 text-lg text-zinc-600 leading-relaxed max-w-xl">
                        {{ $subtitle }}
                    </p>
                @endif

                @if(!empty($features))
                    <ul class="mt-10 space-y-4">
                        @foreach($features as $feature)
                            <li class="flex items-start gap-3">
                                <div class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                                    <i data-lucide="check" class="h-3.5 w-3.5"></i>
                                </div>
                                <span class="text-zinc-700 font-medium">{{ $feature['item'] ?? '' }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if($buttonLabel)
                    <div class="mt-12">
                        <a href="{{ $buttonUrl ?? '#' }}" class="inline-flex items-center justify-center rounded-full bg-zinc-900 px-8 py-4 font-mono text-sm font-bold tracking-widest text-white transition-all hover:bg-primary hover:scale-105 shadow-lg">
                            {{ $buttonLabel }}
                        </a>
                    </div>
                @endif
            </div>

            {{-- Image Content (Right) --}}
            @if($image)
                <div class="w-full lg:w-1/2 min-h-[300px] lg:min-h-full relative">
                    <div class="absolute inset-0 clip-path-hero">
                        <img src="{{ str_starts_with($image, 'http') ? $image : \Illuminate\Support\Facades\Storage::url($image) }}" class="h-full w-full object-cover" alt="{{ $title }}">
                    </div>
                </div>
            @endif

        </div>
    </div>
</section>

<style>
    /* Add a nice diagonal cut effect for large screens */
    @media (min-width: 1024px) {
        .clip-path-hero {
            clip-path: polygon(10% 0, 100% 0, 100% 100%, 0 100%);
        }
    }
</style>
