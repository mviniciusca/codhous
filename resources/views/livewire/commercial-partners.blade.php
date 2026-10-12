<div class="mt-12 w-full">
    <!-- Top Section with Text, Badges and Image -->
    <div class="flex flex-col gap-12 lg:flex-row lg:items-center lg:justify-between mb-16">
        <!-- Text and Features -->
        <div class="flex-1 max-w-2xl">
            @if(!empty($headerSubtitle))
                <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/10 px-4 py-1.5 shadow-sm">
                    <i data-lucide="users" class="h-4 w-4 text-primary"></i>
                    <span class="font-mono text-[11px] font-bold uppercase tracking-widest text-primary">{{ $headerSubtitle }}</span>
                </div>
            @endif
            @if(!empty($headerTitle))
                <h2 class="mb-6 font-mono text-4xl font-extrabold tracking-tight text-foreground md:text-5xl lg:text-6xl" style="text-wrap: balance;">
                    {{ $headerTitle }}
                </h2>
            @endif
            @if(!empty($headerDesc))
                <p class="mb-8 text-lg leading-relaxed text-muted-foreground" style="text-wrap: balance;">
                    {{ $headerDesc }}
                </p>
            @endif

            @if(!empty($features))
                <div class="mt-8 flex flex-wrap gap-4">
                    @foreach($features as $feature)
                        <div class="inline-flex items-center gap-3 rounded-2xl bg-orange-50 border border-orange-100 px-4 py-3 shadow-sm dark:bg-orange-950/20 dark:border-orange-900/30">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-100 text-orange-600 dark:bg-orange-900/50 dark:text-orange-400">
                                <i data-lucide="{{ $feature['icon'] ?? 'check-circle' }}" class="h-5 w-5"></i>
                            </div>
                            <span class="text-sm font-semibold leading-tight text-foreground" style="max-width: 120px;">
                                {{ $feature['title'] ?? '' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Main Image -->
        @if($mainImage)
            <div class="flex-1 relative lg:max-w-xl">
                <!-- Diagonal/Abstract Shape background like in the design could be achieved with pseudo elements -->
                <div class="absolute -inset-4 z-0 rounded-[3rem] bg-gradient-to-tr from-orange-100 to-orange-50/50 transform rotate-3 dark:from-orange-950/40 dark:to-orange-900/10"></div>
                <div class="relative z-10 overflow-hidden rounded-[2.5rem] border-4 border-white shadow-2xl dark:border-zinc-900">
                    <img src="{{ Storage::url($mainImage) }}" alt="Parceria" class="w-full h-auto object-cover aspect-[4/3] sm:aspect-video lg:aspect-[4/3] scale-105 hover:scale-110 transition-transform duration-700">
                </div>
            </div>
        @endif
    </div>

    <!-- Grid of Logos -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
        @foreach($partners as $partner)
            @if(!empty($partner->content['logo']))
                <a href="{{ $partner->content['website'] ?? '#' }}" target="_blank" rel="noopener noreferrer" class="group flex h-24 items-center justify-center rounded-2xl border border-border/60 bg-card p-6 shadow-sm transition-all hover:border-primary/30 hover:shadow-md hover:-translate-y-1">
                    <img src="{{ Storage::url($partner->content['logo']) }}" alt="{{ $partner->name }}" class="max-h-full max-w-full object-contain filter grayscale opacity-70 transition-all duration-300 group-hover:grayscale-0 group-hover:opacity-100 dark:brightness-200 dark:group-hover:brightness-100">
                </a>
            @else
                <a href="{{ $partner->content['website'] ?? '#' }}" target="_blank" rel="noopener noreferrer" class="group flex h-24 items-center justify-center rounded-2xl border border-border/60 bg-card p-6 shadow-sm transition-all hover:border-primary/30 hover:shadow-md hover:-translate-y-1">
                    <span class="font-bold text-muted-foreground transition-colors group-hover:text-foreground text-center">{{ $partner->name }}</span>
                </a>
            @endif
        @endforeach
    </div>
</div>
