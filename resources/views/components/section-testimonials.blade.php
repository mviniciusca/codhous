@props([
    'data' => [],
    'textColor' => 'light',
    'bgColor' => null,
    'header' => null,
    'items' => null,
])
@php
    $section = \App\Models\ContentSection::getBySlug('testimonials');
    $header = $header ?? $section?->content['header'] ?? [];
    $items = $items ?? $section?->content['items'] ?? null;
    if (empty($items)) {
        $header = ['subtitle' => 'Depoimentos', 'title' => 'O que dizem nossos clientes', 'description' => 'Empresas e obras que confiam na nossa entrega, na qualidade do concreto e no nosso suporte.'];
        $items = [
            ['quote' => '"Atendimento rápido, concreto dentro do prazo e equipe técnica sempre disponível."', 'author_name' => 'Carlos Mendes', 'author_role' => "Engenheiro\nConstrutora Mendes", 'stars' => 5, 'avatar' => 'https://randomuser.me/api/portraits/men/32.jpg'],
            ['quote' => '"Pontualidade e qualidade do traço fazem a diferença. A Codhous é nossa parceira em várias obras."', 'author_name' => 'Ana Paula Costa', 'author_role' => "Mestre de obras\nAPC Construções", 'stars' => 5, 'avatar' => 'https://randomuser.me/api/portraits/women/44.jpg'],
            ['quote' => '"Orçamento claro, entrega no horário e suporte pós-venda excelente."', 'author_name' => 'Roberto Lima', 'author_role' => "Arquiteto\nLima Arquitetura", 'stars' => 5, 'avatar' => 'https://randomuser.me/api/portraits/men/46.jpg'],
        ];
    }
@endphp
@if(!\App\Models\ContentSection::isHidden('testimonials'))
@php
    $bgPullUpAmount = (int) ($data['background_image_pull_up'] ?? 0);
    $overflowClass = $bgPullUpAmount !== 0 ? '' : 'overflow-hidden';
@endphp
<section id="depoimentos" class="{{ $bgColor ?? 'bg-background' }} py-16 lg:py-24 relative {{ $overflowClass }} {{ ($textColor ?? 'light') === 'dark' ? 'text-scheme-dark' : '' }}">
    <x-ui.section-background :data="$data" />
    <div class="mx-auto max-w-7xl px-4 lg:px-8 relative z-10">
        
        <!-- Header -->
        <div class="mb-12 max-w-2xl">
            <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/5 px-4 py-1.5">
                <span class="h-1.5 w-1.5 rounded-full bg-primary animate-pulse shadow-[0_0_8px_var(--primary)]"></span>
                <span class="font-mono text-[10px] font-bold uppercase tracking-[0.2em] text-primary">{{ $header['subtitle'] ?? 'Depoimentos' }}</span>
            </div>
            
            <h2 class="font-mono text-4xl font-extrabold tracking-tight text-foreground md:text-5xl drop-shadow-sm mb-4" style="text-wrap: balance;">
                {{ $header['title'] ?? 'O que dizem nossos clientes' }}
            </h2>
            
            <p class="text-lg font-medium leading-relaxed text-muted-foreground" style="text-wrap: balance;">
                {{ $header['description'] ?? 'Empresas e obras que confiam na nossa entrega, na qualidade do concreto e no nosso suporte.' }}
            </p>
            
            <!-- Trust badges (Inspired by the reference image) -->
            <div class="mt-8 flex flex-wrap gap-6 border-t border-border/30 pt-8">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary shadow-sm">
                        <i data-lucide="shield-check" class="h-5 w-5"></i>
                    </div>
                    <span class="text-xs font-bold text-foreground max-w-[80px] leading-tight">Qualidade comprovada</span>
                </div>
                <div class="h-10 w-px bg-border/50 hidden md:block"></div>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary shadow-sm">
                        <i data-lucide="truck" class="h-5 w-5"></i>
                    </div>
                    <span class="text-xs font-bold text-foreground max-w-[80px] leading-tight">Entrega no prazo</span>
                </div>
                <div class="h-10 w-px bg-border/50 hidden md:block"></div>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary shadow-sm">
                        <i data-lucide="headphones" class="h-5 w-5"></i>
                    </div>
                    <span class="text-xs font-bold text-foreground max-w-[80px] leading-tight">Suporte especializado</span>
                </div>
            </div>
        </div>

        <!-- Carousel -->
        @php
            $sliderId = 'testimonials-swiper-' . Str::random(6);
        @endphp
        <div class="relative w-full -mx-4 px-4 md:mx-0 md:px-0">
            <div class="swiper {{ $sliderId }} w-full pb-20 pt-4">
                <div class="swiper-wrapper !items-stretch">
                    @foreach($items as $index => $item)
                        <div class="swiper-slide !h-auto">
                            <!-- Card Design matching the minimal style & reference image -->
                            <div class="h-full w-full flex flex-col md:flex-row gap-6 rounded-[24px] bg-card border border-border/40 p-8 shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] transition-all hover:shadow-md hover:border-primary/20">
                                
                                <div class="flex-1 flex flex-col">
                                    <!-- Stars -->
                                    <div class="mb-5 flex gap-1 text-primary">
                                        @foreach(range(1, (int) ($item['stars'] ?? 5)) as $i)
                                            <i data-lucide="star" class="h-5 w-5 fill-current"></i>
                                        @endforeach
                                    </div>
                                    
                                    <!-- Quote -->
                                    <p class="mb-8 flex-1 text-base font-medium leading-relaxed text-foreground/80">
                                        {{ $item['quote'] ?? '' }}
                                    </p>
                                    
                                    <!-- Author -->
                                    <div class="flex items-center gap-4 border-t border-border/30 pt-6 mt-auto">
                                        @if(!empty($item['avatar']))
                                            @php
                                                $avatarUrl = str_starts_with($item['avatar'], 'http') ? $item['avatar'] : \Illuminate\Support\Facades\Storage::url($item['avatar']);
                                            @endphp
                                            <img src="{{ $avatarUrl }}" alt="{{ $item['author_name'] ?? '' }}" class="h-12 w-12 rounded-full object-cover border border-border/50">
                                        @else
                                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                                                <i data-lucide="user" class="h-6 w-6"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-bold text-foreground text-sm">{{ $item['author_name'] ?? '' }}</p>
                                            @if(!empty($item['author_role']))
                                                <p class="text-[11px] font-medium text-muted-foreground whitespace-pre-line">{{ $item['author_role'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Right-side Image -->
                                @if(!empty($item['image']))
                                    <div class="hidden md:block w-32 shrink-0">
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($item['image']) }}" alt="" class="h-full w-full rounded-2xl object-cover border border-border/40 shadow-sm">
                                    </div>
                                @endif
                                
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination !bottom-0"></div>
            </div>
            
            <!-- Navigation arrows -->
            <div class="{{ $sliderId }}-prev absolute top-[calc(50%-2rem)] -translate-y-1/2 -left-4 xl:-left-6 hidden md:flex h-12 w-12 cursor-pointer items-center justify-center rounded-full bg-white border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.15)] text-foreground z-10 transition-transform hover:scale-110 group">
                <i data-lucide="chevron-left" class="h-6 w-6 transition-transform group-hover:-translate-x-0.5"></i>
            </div>
            <div class="{{ $sliderId }}-next absolute top-[calc(50%-2rem)] -translate-y-1/2 -right-4 xl:-right-6 hidden md:flex h-12 w-12 cursor-pointer items-center justify-center rounded-full bg-white border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.15)] text-foreground z-10 transition-transform hover:scale-110 group">
                <i data-lucide="chevron-right" class="h-6 w-6 transition-transform group-hover:translate-x-0.5"></i>
            </div>
        </div>
        
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swiper !== 'undefined' && document.querySelector('.{{ $sliderId }}')) {
            new Swiper('.{{ $sliderId }}', {
                slidesPerView: 1,
                spaceBetween: 24,
                pagination: {
                    el: '.{{ $sliderId }} .swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.{{ $sliderId }}-next',
                    prevEl: '.{{ $sliderId }}-prev',
                },
                breakpoints: {
                    768: { slidesPerView: 2 },
                    1024: { slidesPerView: 3 },
                }
            });
        }
    });
</script>
@endif
