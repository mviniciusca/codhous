@props([
    'data' => [],
    'textColor' => null,
    'bgColor' => null,
    'title' => null,
    'header' => null,
    'items' => null,
])
@php
    $data = array_merge(array_filter(['background_color' => $bgColor, 'text_color' => $textColor]), (array) $data);
    $section = \App\Models\ContentSection::getBySlug('testimonials');
    $header = $header ?: ($section?->content['header'] ?? []);
    $items = $items ?: ($section?->content['items'] ?? null);

    if (empty($items)) {
        $header = ['subtitle' => 'Depoimentos', 'title' => 'O que dizem nossos clientes', 'description' => 'Empresas e obras que confiam na nossa entrega, na qualidade do concreto e no nosso suporte.'];
        $items = [
            ['quote' => 'Atendimento rápido, concreto dentro do prazo e equipe técnica sempre disponível.', 'author_name' => 'Carlos Mendes', 'author_role' => "Engenheiro\nConstrutora Mendes", 'stars' => 5, 'avatar' => 'https://randomuser.me/api/portraits/men/32.jpg'],
            ['quote' => 'Pontualidade e qualidade do traço fazem a diferença. A Codhous é nossa parceira em várias obras.', 'author_name' => 'Ana Paula Costa', 'author_role' => "Mestre de obras\nAPC Construções", 'stars' => 5, 'avatar' => 'https://randomuser.me/api/portraits/women/44.jpg'],
            ['quote' => 'Orçamento claro, entrega no horário e suporte pós-venda excelente.', 'author_name' => 'Roberto Lima', 'author_role' => "Arquiteto\nLima Arquitetura", 'stars' => 5, 'avatar' => 'https://randomuser.me/api/portraits/men/46.jpg'],
        ];
    }

    $items = array_values((array) $items);
    $useSlider = count($items) > 3;
    $sliderId = 'testimonials-swiper-' . Str::random(6);
    $imageUrl = fn ($path) => empty($path) ? null : (str_starts_with($path, 'http') ? $path : \Illuminate\Support\Facades\Storage::url($path));
    $average = round(collect($items)->avg(fn ($i) => (int) ($i['stars'] ?? 5)), 1);
@endphp
@if(!\App\Models\ContentSection::isHidden('testimonials'))
<x-ui.section :data="$data" id="depoimentos" background="bg-background">
    <x-ui.section-header
        :header="$header"
        :fallback-title="$header['title'] ?? 'O que dizem nossos clientes'"
        :fallback-subtitle="$header['subtitle'] ?? 'Depoimentos'"
        :fallback-description="$header['description'] ?? null"
    >
        <x-slot:actions>
            {{-- Prova social compacta + navegação --}}
            <div class="flex items-center gap-3 rounded-full border border-border/70 bg-card py-1.5 pl-1.5 pr-4">
                <div class="flex -space-x-2">
                    @foreach(array_slice($items, 0, 3) as $item)
                        @if($avatar = $imageUrl($item['avatar'] ?? null))
                            <img src="{{ $avatar }}" alt="" class="h-8 w-8 rounded-full object-cover ring-2 ring-card" loading="lazy">
                        @endif
                    @endforeach
                </div>
                <div class="leading-tight">
                    <div class="flex items-center gap-1 text-sm font-bold text-foreground">
                        <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400 text-amber-400"></i>
                        {{ number_format($average, 1, ',', '') }}
                    </div>
                    <div class="text-[11px] text-muted-foreground">{{ count($items) }} avaliações</div>
                </div>
            </div>
            @if($useSlider)
                <x-ui.slider-nav :id="$sliderId" class="hidden md:flex" />
            @endif
        </x-slot:actions>
    </x-ui.section-header>

    @if($useSlider)
        <div class="swiper {{ $sliderId }} !overflow-visible md:!overflow-hidden">
            <div class="swiper-wrapper">
    @else
        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
    @endif

    @foreach($items as $index => $item)
        @if($useSlider)<div class="swiper-slide !h-auto">@endif
        <figure class="surface-card surface-card-interactive group flex h-full gap-5 p-6 md:p-7" data-spotlight data-reveal style="--reveal-delay: {{ ($index % 3) * 90 }}ms">
            <div class="flex flex-1 flex-col">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex gap-0.5">
                        @foreach(range(1, 5) as $star)
                            <i data-lucide="star" @class(['h-4 w-4', 'fill-amber-400 text-amber-400' => $star <= (int) ($item['stars'] ?? 5), 'text-border' => $star > (int) ($item['stars'] ?? 5)])></i>
                        @endforeach
                    </div>
                    <i data-lucide="quote" class="h-6 w-6 text-primary-500/20 transition-colors group-hover:text-primary-500/50"></i>
                </div>

                <blockquote class="flex-1 text-[15px] leading-relaxed text-foreground/85">
                    {{ trim($item['quote'] ?? '', '"“” ') }}
                </blockquote>

                <figcaption class="mt-6 flex items-center gap-3 border-t border-border/60 pt-5">
                    @if($avatar = $imageUrl($item['avatar'] ?? null))
                        <img src="{{ $avatar }}" alt="{{ $item['author_name'] ?? '' }}" class="h-10 w-10 rounded-full object-cover" loading="lazy">
                    @else
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-500/10 font-mono text-sm font-bold text-primary-ink">
                            {{ mb_strtoupper(mb_substr($item['author_name'] ?? '?', 0, 1)) }}
                        </span>
                    @endif
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-foreground">{{ $item['author_name'] ?? '' }}</p>
                        @if(!empty($item['author_role']))
                            <p class="truncate text-xs text-muted-foreground">{{ str_replace("\n", ' · ', $item['author_role']) }}</p>
                        @endif
                    </div>
                </figcaption>
            </div>

            @if($photo = $imageUrl($item['image'] ?? null))
                <img src="{{ $photo }}" alt="" class="hidden w-28 shrink-0 rounded-xl object-cover md:block" loading="lazy">
            @endif
        </figure>
        @if($useSlider)</div>@endif
    @endforeach

    @if($useSlider)
            </div>
            <div class="mt-8 flex items-center justify-between gap-4">
                <div class="swiper-pagination !static !w-auto"></div>
                <x-ui.slider-nav :id="$sliderId" class="md:hidden" />
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof Swiper === 'undefined' || !document.querySelector('.{{ $sliderId }}')) return;
                new Swiper('.{{ $sliderId }}', {
                    slidesPerView: 1.08,
                    spaceBetween: 16,
                    grabCursor: true,
                    pagination: { el: '.{{ $sliderId }} .swiper-pagination', clickable: true },
                    navigation: { nextEl: '.{{ $sliderId }}-next', prevEl: '.{{ $sliderId }}-prev' },
                    breakpoints: {
                        768: { slidesPerView: 2, spaceBetween: 20 },
                        1024: { slidesPerView: 3, spaceBetween: 20 },
                    },
                });
            });
        </script>
    @else
        </div>
    @endif
</x-ui.section>
@endif
