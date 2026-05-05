<div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto">
        <header class="mb-12 text-center">
            <h2 class="text-4xl font-extrabold tracking-tight text-zinc-900 dark:text-white sm:text-5xl">
                Nossas Obras
            </h2>
            <p class="mt-4 text-lg text-zinc-600 dark:text-zinc-400">
                Conheça alguns dos nossos projetos realizados com excelência e qualidade.
            </p>
        </header>

        <div class="space-y-16">
            @forelse($showcases as $showcase)
                <article class="group bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl overflow-hidden shadow-lg transition-all duration-300 hover:shadow-xl">
                    <!-- Gallery -->
                    @if($showcase->images && count($showcase->images) > 0)
                        <div class="relative">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 p-2">
                                @foreach(array_slice($showcase->images, 0, 2) as $index => $image)
                                    <div class="relative h-72 overflow-hidden rounded-xl {{ count($showcase->images) === 1 ? 'md:col-span-2' : '' }}">
                                        <img src="{{ Storage::url($image) }}" 
                                             alt="{{ $showcase->title }} - {{ $index + 1 }}" 
                                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                        @if($index === 1 && count($showcase->images) > 2)
                                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center backdrop-blur-[2px]">
                                                <span class="text-white text-2xl font-bold">+{{ count($showcase->images) - 2 }} fotos</span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="p-8">
                        <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
                            <div>
                                <h3 class="text-3xl font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    {{ $showcase->title }}
                                </h3>
                                @if($showcase->location)
                                    <div class="flex items-center mt-2 text-zinc-500 dark:text-zinc-400">
                                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="text-sm font-medium">{{ $showcase->location }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="prose prose-zinc dark:prose-invert max-w-none">
                            <p class="text-zinc-600 dark:text-zinc-300 text-lg leading-relaxed">
                                {{ $showcase->description }}
                            </p>
                        </div>
                    </div>
                </article>
            @empty
                <div class="text-center py-20 bg-zinc-50 dark:bg-zinc-900/50 rounded-2xl border-2 border-dashed border-zinc-200 dark:border-zinc-800">
                    <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="mt-4 text-xl font-medium text-zinc-900 dark:text-white">Nenhuma obra encontrada</p>
                    <p class="mt-1 text-zinc-500">Em breve mostraremos nossos novos projetos aqui.</p>
                </div>
            @endforelse

            @if($showcases->hasPages())
                <div class="mt-12 flex justify-center">
                    <div class="inline-flex rounded-xl shadow-sm bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-1">
                        {{ $showcases->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
