@props(['type', 'data', 'page' => null, 'theme' => 'default', 'hideHeader' => false])

@php
    $isHome = $page ? ($page->slug === '/' || $page->slug === '') : false;
@endphp

@switch($type)
    @case('module_reference')
        @php
            $section = \App\Models\ContentSection::find($data['content_section_id']);
        @endphp
        @if($section && $section->is_active)
            <x-render-block :type="$section->type" :data="$section->content" :page="$page" :theme="$theme" />
        @endif
        @break

    @case('page_header')
        <x-page-header
            :badge="$data['badge'] ?? null"
            :title="$data['title'] ?? null"
            :description="$data['description'] ?? null"
            :background-image="$data['background_image'] ?? null"
            :breadcrumbs="[['label' => $data['title'] ?? 'Página']]"
        />
        @break

    @case('hero')
        @php
            $heroBadge = $data['header']['subtitle'] ?? $data['badge'] ?? '';
            $heroTitle = $data['header']['title'] ?? $data['title'] ?? '';
            $heroSubtitle = $data['header']['description'] ?? $data['subtitle'] ?? '';
            
            $primaryIconSelect = $data['primary_button_icon_select'] ?? '';
            $primaryIcon = $primaryIconSelect === 'outro' 
                ? ($data['primary_button_icon'] ?? '') 
                : ($primaryIconSelect ?: ($data['primary_button_icon'] ?? ''));

            $secondaryIconSelect = $data['secondary_button_icon_select'] ?? '';
            $secondaryIcon = $secondaryIconSelect === 'outro' 
                ? ($data['secondary_button_icon'] ?? '') 
                : ($secondaryIconSelect ?: ($data['secondary_button_icon'] ?? ''));
        @endphp
        <livewire:section-hero-cep
            :bg-color="$data['background_color'] ?? null"
            :text-color="$data['text_color'] ?? 'light'"
            :main-slide="[
                'title' => $heroTitle,
                'subtitle' => $heroSubtitle,
                'image' => $data['image'] ?? null,
                'video' => $data['video'] ?? null,
                'image_alignment' => $data['image_alignment'] ?? 'center'
            ]"
            :buttons="[
                'primary' => [
                    'text' => $data['primary_button_text'] ?? null,
                    'url' => $data['primary_button_url'] ?? null,
                    'icon' => $primaryIcon,
                ],
                'secondary' => [
                    'text' => $data['secondary_button_text'] ?? null,
                    'url' => $data['secondary_button_url'] ?? null,
                    'icon' => $secondaryIcon,
                ]
            ]"
            :show-action-buttons="$data['show_action_buttons'] ?? true"
            :show-stats="$data['show_stats'] ?? true"
            :show-slideshow="$data['show_slideshow'] ?? false"
            :slideshow="$data['slideshow'] ?? []"
            :badge="$heroBadge"
            :layout="$data['layout'] ?? 'default'"
            :theme="$theme"
            :stats="$data['stats'] ?? []"
        />
        @break

    @case('hero_simple')
        <x-hero-simple
            :title="$data['title'] ?? ''"
            :subtitle="$data['subtitle'] ?? ''"
            :image="$data['image'] ?? null"
            :primary-button-label="$data['primaryButtonLabel'] ?? null"
            :primary-button-url="$data['primaryButtonUrl'] ?? null"
            :secondary-button-label="$data['secondaryButtonLabel'] ?? null"
            :secondary-button-url="$data['secondaryButtonUrl'] ?? null"
        />
        @break

    @case('hero_split')
        <x-hero-split
            :title="$data['title'] ?? ''"
            :subtitle="$data['subtitle'] ?? ''"
            :image="$data['image'] ?? null"
            :features="$data['features'] ?? []"
            :button-label="$data['buttonLabel'] ?? null"
            :button-url="$data['buttonUrl'] ?? null"
        />
        @break

    @case('partners')
        <x-section-partners
            :bg-color="$data['background_color'] ?? null"
            :text-color="$data['text_color'] ?? 'light'"
            :layout="$data['layout'] ?? 'slider'"
            :subtitle="$data['header']['subtitle'] ?? $data['subtitle'] ?? null"
            :title="$data['header']['title'] ?? $data['title'] ?? null"
            :description="$data['header']['description'] ?? $data['description'] ?? null"
            :header="$data['header'] ?? []"
            :items="$data['items'] ?? []"
        />
        @break

    @case('services')
        @php
            $headerVisible = $data['header']['visible'] ?? true;
            $headerAlignment = $data['header']['alignment'] ?? 'center';
            $alignClass = $headerAlignment === 'left' ? 'text-left mr-auto' : ($headerAlignment === 'right' ? 'text-right ml-auto' : 'text-center mx-auto');

            $servicesTitle = $data['header']['title'] ?? $data['title'] ?? 'Nossos Serviços';
            $servicesSubtitle = $data['header']['subtitle'] ?? $data['badge'] ?? 'O que fazemos';
            $servicesDesc = $data['header']['description'] ?? $data['description'] ?? 'Soluções completas com qualidade garantida.';
            
            $bgColor = $data['background_color'] ?? 'bg-background';
            $isPrimaryBg = str_contains($bgColor, 'bg-primary');
            $badgeTextClass = $isPrimaryBg ? 'text-[color-mix(in_srgb,var(--primary),black_85%)]' : 'text-primary';
            $badgeBgClass = $isPrimaryBg ? 'bg-[color-mix(in_srgb,var(--primary),black_85%)]' : 'bg-primary';
            $badgeBorderClass = $isPrimaryBg ? 'border-black/30 bg-black/20' : 'border-primary/20 bg-primary/10';
            
            $bgImg = !empty($data['background_image']) ? \Illuminate\Support\Facades\Storage::url($data['background_image']) : null;
            $bgFit = $data['background_image_fit'] ?? 'cover';
            $bgPos = $data['background_image_position'] ?? 'center';
            $bgOp = ($data['background_image_opacity'] ?? '100') / 100;
            $bgPullUpAmount = (int) ($data['background_image_pull_up'] ?? 0);
            $overflowClass = $bgPullUpAmount !== 0 ? '' : 'overflow-hidden';
            $bgDivClasses = 'absolute inset-x-0 z-0 pointer-events-none bg-no-repeat';
            $bgPosParts = explode(' ', $bgPos);
            $bgPosHorizontal = $bgPosParts[0];
            if ($bgPullUpAmount > 0) {
                $topStyle = "-{$bgPullUpAmount}%";
                $bottomStyle = "0";
                $bgPositionStyle = $bgPosHorizontal . ' bottom';
            } elseif ($bgPullUpAmount < 0) {
                $topStyle = "0";
                $bottomStyle = $bgPullUpAmount . "%";
                $bgPositionStyle = $bgPosHorizontal . ' top';
            } else {
                $topStyle = "0";
                $bottomStyle = "0";
                $bgPositionStyle = $bgPos;
            }
            
            $displayItems = $data['items'] ?? [];
            if (empty($displayItems)) {
                // Fallback de demonstração
                $displayItems = [
                    ['title' => 'Concreto Usinado', 'subtitle' => 'Alta Performance', 'description' => 'Concreto de alta resistência com controle rigoroso de qualidade. Entregue pontualmente na sua obra.', 'icon' => 'truck', 'bullets' => ['Até 50 MPa', 'Laudo técnico', 'Frota moderna'], 'cta_label' => 'Fazer Orçamento', 'cta_url' => '#orcamento'],
                    ['title' => 'Bombeamento', 'subtitle' => 'Alcance Máximo', 'description' => 'Serviço de bombeamento eficiente para lajes e locais de difícil acesso, otimizando o tempo da sua equipe.', 'icon' => 'arrow-up-circle', 'bullets' => ['Bomba lança', 'Bomba estacionária', 'Operadores treinados'], 'cta_label' => 'Fazer Orçamento', 'cta_url' => '#orcamento'],
                    ['title' => 'Locação de Máquinas', 'subtitle' => 'Frota Renovada', 'description' => 'Equipamentos pesados para terraplanagem, escavação e compactação. Disponibilidade imediata.', 'icon' => 'tractor', 'bullets' => ['Retroescavadeiras', 'Rolos compactadores', 'Manutenção em dia'], 'cta_label' => 'Fazer Orçamento', 'cta_url' => '#orcamento'],
                ];
            }
        @endphp
        <section class="{{ $bgColor }} {{ ($data['text_color'] ?? 'light') === 'dark' ? 'text-scheme-dark' : '' }} py-16 lg:py-24 relative {{ $overflowClass }}">
            <x-ui.section-background :data="$data" />
            <div class="mx-auto max-w-7xl px-4 lg:px-8 relative z-10">
                @if(!$hideHeader && $headerVisible && (!empty($servicesTitle) || !empty($servicesSubtitle)))
                    <div class="mb-16 max-w-3xl {{ $alignClass }}">
                        @if(!empty($servicesSubtitle))
                            <div class="mb-4 inline-flex items-center gap-2 rounded-full border {{ $badgeBorderClass }} px-4 py-1.5 backdrop-blur-md shadow-lg shadow-primary/5">
                                <span class="h-1.5 w-1.5 rounded-full {{ $badgeBgClass }} animate-pulse shadow-md"></span>
                                <span class="font-mono text-[10px] font-bold uppercase tracking-[0.2em] {{ $badgeTextClass }}">{{ $servicesSubtitle }}</span>
                            </div>
                        @endif
                        @if(!empty($servicesTitle))
                            <h2 class="font-mono text-4xl font-extrabold tracking-tight text-foreground md:text-5xl drop-shadow-sm mb-4" style="text-wrap: balance;">
                                {{ $servicesTitle }}
                            </h2>
                        @endif
                        @if(!empty($servicesDesc))
                            <p class="text-lg font-medium leading-relaxed text-muted-foreground {{ $headerAlignment === 'center' ? 'mx-auto' : '' }}" style="text-wrap: balance;">
                                {{ $servicesDesc }}
                            </p>
                        @endif
                    </div>

                @endif

                @php
                    $itemsCount = count($displayItems);
                    $useSlider = $itemsCount > 3;
                    $sliderId = 'services-swiper-' . Str::random(6);
                @endphp

                @if($useSlider)
                    <div class="relative w-full">
                        <div class="swiper {{ $sliderId }} w-full pb-16">
                            <div class="swiper-wrapper">
                @else
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @endif

                    @foreach($displayItems as $index => $service)
                        @if($useSlider) <div class="swiper-slide h-auto"> @endif
                        
                        <div class="flex h-full flex-col rounded-[24px] bg-card border border-border/40 p-8 shadow-sm transition-all hover:shadow-md hover:border-primary/20">
                            <div class="flex items-start justify-between mb-8">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                    <i data-lucide="{{ $service['icon'] ?? 'droplets' }}" class="h-6 w-6"></i>
                                </div>
                                <span class="font-mono text-sm font-medium text-muted-foreground/30">
                                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                            
                            @if(!empty($service['subtitle']))
                                <div class="mb-2 text-[10px] font-bold tracking-[0.2em] uppercase text-muted-foreground">
                                    {{ $service['subtitle'] }}
                                </div>
                            @endif
                            
                            <h3 class="mb-3 text-xl font-bold text-foreground">
                                {{ $service['title'] ?? '' }}
                            </h3>
                            
                            @if(!empty($service['description']))
                                <p class="mb-6 text-sm leading-relaxed text-muted-foreground">
                                    {{ $service['description'] }}
                                </p>
                            @endif
                            
                            @if(!empty($service['bullets']))
                                <ul class="mb-8 flex flex-col gap-3">
                                    @foreach((array) $service['bullets'] as $bullet)
                                        <li class="flex items-start gap-3 text-sm text-muted-foreground">
                                            <div class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                                                <i data-lucide="check" class="h-2.5 w-2.5"></i>
                                            </div>
                                            <span>{{ $bullet }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            
                            <div class="mt-auto pt-6 border-t border-border/30">
                                <a href="{{ $service['cta_url'] ?? '#orcamento' }}" class="group flex items-center justify-between text-sm font-bold text-primary hover:text-primary/80 transition-colors">
                                    {{ $service['cta_label'] ?? 'Solicitar orçamento' }}
                                    <i data-lucide="arrow-right" class="h-4 w-4 transition-transform group-hover:translate-x-1"></i>
                                </a>
                            </div>
                        </div>
                        
                        @if($useSlider) </div> @endif
                    @endforeach

                @if($useSlider)
                            </div>
                            <div class="swiper-pagination !bottom-0"></div>
                        </div>
                        
                        <!-- Botões de Navegação (Escondidos no mobile, visíveis a partir de md) -->
                        <div class="{{ $sliderId }}-prev absolute top-[calc(50%-2rem)] -translate-y-1/2 -left-4 xl:-left-6 hidden md:flex h-12 w-12 cursor-pointer items-center justify-center rounded-full bg-white border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.15)] text-primary z-10 transition-transform hover:scale-110 group">
                            <i data-lucide="chevron-left" class="h-6 w-6 transition-transform group-hover:-translate-x-0.5"></i>
                        </div>
                        <div class="{{ $sliderId }}-next absolute top-[calc(50%-2rem)] -translate-y-1/2 -right-4 xl:-right-6 hidden md:flex h-12 w-12 cursor-pointer items-center justify-center rounded-full bg-white border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.15)] text-primary z-10 transition-transform hover:scale-110 group">
                            <i data-lucide="chevron-right" class="h-6 w-6 transition-transform group-hover:translate-x-0.5"></i>
                        </div>
                    </div>
                    
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
                @else
                    </div>
                @endif
            </div>
        </section>
        @break

    @case('timeline')
        <x-section-timeline
            :bg-color="$data['background_color'] ?? null"
            :text-color="$data['text_color'] ?? 'light'"
            :title="$data['header']['title'] ?? $data['title'] ?? null"
            :header="$data['header'] ?? []"
            :steps="$data['steps'] ?? []"
        />
        @break

    @case('showcase')
        <section class="{{ $data['background_color'] ?? 'bg-background' }} {{ ($data['text_color'] ?? 'light') === 'dark' ? 'text-scheme-dark' : '' }} py-8 lg:py-12">
            <div class="mx-auto max-w-7xl px-4 lg:px-8">
                @if(!empty($data['title']) || !empty($data['badge']))
                    <div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                        <div class="max-w-2xl">
                            @if(!empty($data['badge']))
                                <span class="mb-4 inline-block text-xs font-semibold uppercase tracking-widest text-primary">
                                    {{ $data['badge'] }}
                                </span>
                            @endif
                            @if(!empty($data['title']))
                                <h2 class="font-mono text-3xl font-bold tracking-tight text-foreground md:text-4xl" style="text-wrap: balance;">
                                    {{ $data['title'] }}
                                </h2>
                            @endif
                            @if(!empty($data['description']))
                                <p class="mt-4 text-lg leading-relaxed text-muted-foreground">
                                    {{ $data['description'] }}
                                </p>
                            @endif
                        </div>
                        
                        @if($isHome)
                            <a href="/nossas-obras" class="group inline-flex items-center gap-2 text-sm font-bold uppercase tracking-widest text-primary transition-all hover:gap-3">
                                Ver todas <i data-lucide="arrow-right" class="h-4 w-4"></i>
                            </a>
                        @endif
                    </div>
                @endif
                <livewire:showcase-feed 
                    :limit="$data['limit'] ?? 4" 
                    :show-pagination="!$isHome"
                />
            </div>
        </section>
        @break

    @case('faq')
        <x-section-faq
            :bg-color="$data['background_color'] ?? null"
            :text-color="$data['text_color'] ?? 'light'"
            :title="$data['header']['title'] ?? $data['title'] ?? null"
            :header="$data['header'] ?? []"
            :items="$data['items'] ?? []"
        />
        @break

    @case('testimonials')
        <x-section-testimonials
            :data="$data"
            :bg-color="$data['background_color'] ?? null"
            :text-color="$data['text_color'] ?? 'light'"
            :title="$data['header']['title'] ?? $data['title'] ?? null"
            :header="$data['header'] ?? []"
            :items="$data['items'] ?? []"
        />
        @break

    @case('team')
        <x-section-team
            :data="$data"
            :bg-color="$data['background_color'] ?? null"
            :text-color="$data['text_color'] ?? 'light'"
            :header="$data['header'] ?? []"
            :items="$data['items'] ?? []"
        />
        @break

    @case('coverage')
        <livewire:section-coverage
            :bg-color="$data['background_color'] ?? null"
            :text-color="$data['text_color'] ?? 'light'"
            :title="$data['header']['title'] ?? $data['title'] ?? null"
            :header="$data['header'] ?? []"
            :cities="$data['cities'] ?? []"
            :overlay-data="[
                'enabled' => $data['background_overlay_enabled'] ?? false,
                'type' => $data['background_overlay_type'] ?? 'dark',
                'opacity' => $data['background_overlay_opacity'] ?? '50',
            ]"
        />
        @break

    @case('differentials')
        <x-section-differentials
            :bg-color="$data['background_color'] ?? null"
            :text-color="$data['text_color'] ?? 'light'"
            :title="$data['header']['title'] ?? $data['title'] ?? null"
            :subtitle="$data['header']['subtitle'] ?? $data['badge'] ?? null"
            :description="$data['header']['description'] ?? $data['description'] ?? null"
            :header="$data['header'] ?? []"
            :items="$data['items'] ?? []"
        />
        @break

    @case('contact_banner')
        <x-section-contact-banner
            :bg-color="$data['background_color'] ?? null"
            :text-color="$data['text_color'] ?? 'light'"
            :badge="$data['badge'] ?? null"
            :title="$data['title'] ?? null"
            :description="$data['description'] ?? null"
            :whatsapp-enabled="$data['whatsapp_enabled'] ?? true"
            :call-enabled="$data['call_enabled'] ?? true"
            :email-enabled="$data['email_enabled'] ?? true"
        />
        @break

    @case('calculator')
        @php
            $calcTitle = $data['header']['title'] ?? $data['title'] ?? null;
            $calcSubtitle = $data['header']['subtitle'] ?? $data['badge'] ?? null;
            $calcDesc = $data['header']['description'] ?? $data['description'] ?? null;
            $bgPullUpAmount = (int) ($data['background_image_pull_up'] ?? 0);
            $overflowClass = $bgPullUpAmount !== 0 ? '' : 'overflow-hidden';
        @endphp
        <section class="{{ $data['background_color'] ?? 'bg-background' }} {{ ($data['text_color'] ?? 'light') === 'dark' ? 'text-scheme-dark' : '' }} py-8 lg:py-12 relative {{ $overflowClass }}">
            <x-ui.section-background :data="$data" />
            <div class="mx-auto max-w-7xl px-4 lg:px-8 relative z-10">
                <livewire:calculator 
                    :bg-color="$data['background_color'] ?? ''" 
                    :title="$calcTitle"
                    :subtitle="$calcSubtitle"
                    :description="$calcDesc"
                    :header-visible="$data['header']['visible'] ?? true"
                    :header-alignment="$data['header']['alignment'] ?? 'left'"
                />
            </div>
        </section>
        @break

    @case('payment_offer')
        @php
            $headerVisible = $data['header']['visible'] ?? true;
            $headerAlignment = $data['header']['alignment'] ?? 'center';
            $alignClass = $headerAlignment === 'left' ? 'text-left items-start' : ($headerAlignment === 'right' ? 'text-right items-end' : 'text-center items-center');

            $offerTitle = $data['header']['title'] ?? $data['title'] ?? null;
            $offerSubtitle = $data['header']['subtitle'] ?? $data['badge'] ?? null;
            $offerDesc = $data['header']['description'] ?? $data['subtitle'] ?? null;
            
            $bgColor = $data['background_color'] ?? '';
            $bgImg = !empty($data['background_image']) ? \Illuminate\Support\Facades\Storage::url($data['background_image']) : null;
            $bgFit = $data['background_image_fit'] ?? 'cover';
            $bgPos = $data['background_image_position'] ?? 'center';
            $bgOp = ($data['background_image_opacity'] ?? '100') / 100;
            $bgPullUpAmount = (int) ($data['background_image_pull_up'] ?? 0);
            $overflowClass = $bgPullUpAmount !== 0 ? '' : 'overflow-hidden';
            $bgDivClasses = 'absolute inset-x-0 z-0 pointer-events-none bg-no-repeat';
            $bgPosParts = explode(' ', $bgPos);
            $bgPosHorizontal = $bgPosParts[0];
            if ($bgPullUpAmount > 0) {
                $topStyle = "-{$bgPullUpAmount}%";
                $bottomStyle = "0";
                $bgPositionStyle = $bgPosHorizontal . ' bottom';
            } elseif ($bgPullUpAmount < 0) {
                $topStyle = "0";
                $bottomStyle = $bgPullUpAmount . "%";
                $bgPositionStyle = $bgPosHorizontal . ' top';
            } else {
                $topStyle = "0";
                $bottomStyle = "0";
                $bgPositionStyle = $bgPos;
            }
            
            $website = \App\Models\Setting::get('website', []);
            $whatsappNumber = data_get($website, 'features.whatsapp_widget.number', '');
            $buttonUrl = $data['button_url'] ?? null;
            $whatsappUrl = $whatsappNumber && empty($buttonUrl)
                ? "https://wa.me/55" . preg_replace('/[^0-9]/', '', $whatsappNumber) . "?text=" . urlencode('Olá! Gostaria de fazer um orçamento.')
                : $buttonUrl;
                
            $methodsUrl = !empty($data['payment_methods_image']) 
                ? (str_starts_with($data['payment_methods_image'], 'http') ? $data['payment_methods_image'] : \Illuminate\Support\Facades\Storage::url($data['payment_methods_image'])) 
                : null;
                
            $isClickableBanner = !empty($whatsappUrl) && empty($data['button_label']);
            $wrapperTag = $isClickableBanner ? 'a' : 'div';
            $wrapperHref = $isClickableBanner ? 'href="' . $whatsappUrl . '" target="_blank"' : '';
        @endphp
        <section class="bg-transparent py-4 lg:py-8 w-full">
            <div class="mx-auto max-w-7xl px-4 lg:px-8 relative z-10">
                <{{ $wrapperTag }} {!! $wrapperHref !!} class="relative {{ $overflowClass }} flex flex-col {{ $alignClass }} rounded-[24px] {{ $bgColor ?: 'bg-card' }} {{ ($data['text_color'] ?? 'light') === 'dark' ? 'text-scheme-dark' : '' }} border border-border/40 p-8 md:p-10 shadow-sm hover:shadow-md transition-all w-full {{ $isClickableBanner ? 'cursor-pointer hover:border-primary/50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2' : '' }}">
                    
                    <x-ui.section-background :data="$data" />
                    
                    <div class="relative z-10 flex flex-col {{ $alignClass }} w-full">
                        @if(!$hideHeader && $headerVisible && (!empty($offerTitle) || !empty($offerSubtitle)))
                            <div class="mb-8 max-w-3xl">
                                @if(!empty($offerSubtitle))
                                    <div class="mb-3 text-[10px] font-bold tracking-[0.2em] uppercase text-muted-foreground {{ ($data['text_color'] ?? 'light') === 'dark' ? 'text-white/70' : '' }}">
                                        {{ $offerSubtitle }}
                                    </div>
                                @endif
                                @if(!empty($offerTitle))
                                    <h2 class="mb-4 font-mono text-3xl font-extrabold tracking-tight text-foreground md:text-4xl" style="text-wrap: balance;">
                                        {{ $offerTitle }}
                                    </h2>
                                @endif
                                @if(!empty($offerDesc))
                                    <p class="text-base md:text-lg leading-relaxed text-muted-foreground {{ ($data['text_color'] ?? 'light') === 'dark' ? 'text-white/80' : '' }}" style="text-wrap: balance;">
                                        {{ $offerDesc }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    
                    <div class="flex flex-col gap-8 {{ $headerAlignment === 'center' ? 'items-center' : '' }} w-full">
                        @if(!empty($data['button_label']))
                            <a href="{{ $whatsappUrl }}" target="_blank" class="group flex h-14 w-fit items-center gap-3 rounded-2xl bg-primary px-8 font-bold text-primary-foreground shadow-sm hover:shadow-md hover:bg-primary/90 transition-all">
                                <i data-lucide="message-circle" class="h-5 w-5"></i>
                                {{ $data['button_label'] }}
                                <i data-lucide="arrow-right" class="h-4 w-4 ml-1 transition-transform group-hover:translate-x-1"></i>
                            </a>
                        @endif

                        @if(($data['show_payment_methods'] ?? true) !== false)
                        <!-- Meios de pagamento -->
                        <div class="mt-4 pt-6 w-full {{ $headerAlignment === 'center' ? 'text-center' : 'text-left' }}">
                            <div class="text-[10px] font-bold tracking-[0.2em] text-muted-foreground/50 uppercase mb-4 {{ ($data['text_color'] ?? 'light') === 'dark' ? 'text-white/60' : '' }}">PAGUE COM:</div>
                            @if($methodsUrl)
                                <img src="{{ $methodsUrl }}" alt="Meios de Pagamento" class="h-12 md:h-16 w-auto object-contain {{ $headerAlignment === 'center' ? 'mx-auto' : '' }} opacity-80 hover:opacity-100 transition-opacity">
                            @else
                                <div class="flex flex-wrap gap-4 md:gap-6 {{ $headerAlignment === 'center' ? 'justify-center' : '' }} text-muted-foreground/60 {{ ($data['text_color'] ?? 'light') === 'dark' ? 'text-white/60' : '' }} text-xs md:text-sm font-semibold tracking-widest">
                                    <div class="flex items-center gap-1.5 hover:text-primary transition-colors cursor-default">
                                        <i data-lucide="scan-line" class="h-5 w-5"></i>
                                        <span>PIX</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 hover:text-foreground transition-colors cursor-default">
                                        <i data-lucide="barcode" class="h-5 w-5"></i>
                                        <span>BOLETO</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 hover:text-foreground transition-colors cursor-default">
                                        <i data-lucide="credit-card" class="h-5 w-5"></i>
                                        <span>VISA</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 hover:text-foreground transition-colors cursor-default">
                                        <i data-lucide="credit-card" class="h-5 w-5"></i>
                                        <span>MASTER</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 hover:text-foreground transition-colors cursor-default">
                                        <i data-lucide="credit-card" class="h-5 w-5"></i>
                                        <span>AMEX</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 hover:text-foreground transition-colors cursor-default">
                                        <i data-lucide="credit-card" class="h-5 w-5"></i>
                                        <span>ELO</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        @endif
                    </div>
                    </div>
                </{{ $wrapperTag }}>
            </div>
        </section>
        @break

    @case('budget_form')
        @php
            $budgetTitle = $data['header']['title'] ?? $data['title'] ?? null;
            $budgetSubtitle = $data['header']['subtitle'] ?? $data['badge'] ?? 'Orçamento Online';
            $budgetDesc = $data['header']['description'] ?? $data['description'] ?? null;
            
            $bgPullUpAmount = (int) ($data['background_image_pull_up'] ?? 0);
            $overflowClass = $bgPullUpAmount !== 0 ? '' : 'overflow-hidden';
        @endphp
        <section id="orcamento" class="{{ $data['background_color'] ?? 'bg-muted/50' }} {{ ($data['text_color'] ?? 'light') === 'dark' ? 'text-scheme-dark' : '' }} py-8 lg:py-12 relative {{ $overflowClass }}">
            <x-ui.section-background :data="$data" />
            <div class="mx-auto max-w-7xl px-4 lg:px-8 relative z-10">
                <livewire:budget 
                    :bg-color="$data['background_color'] ?? ''"
                    :title="$budgetTitle"
                    :subtitle="$budgetSubtitle"
                    :description="$budgetDesc"
                    :header-visible="$data['header']['visible'] ?? true"
                    :header-alignment="$data['header']['alignment'] ?? 'left'"
                />
            </div>
        </section>
        @break

    @case('cta_contact')
    @case('contact_form')
        <section class="{{ $data['background_color'] ?? 'bg-background' }} {{ ($data['text_color'] ?? 'light') === 'dark' ? 'text-scheme-dark' : '' }} py-20 lg:py-28">
            <div class="mx-auto max-w-7xl px-4 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-16 items-start">
                    <div>
                        @php
                            $contactSubtitle = $data['header']['subtitle'] ?? $data['subtitle'] ?? null;
                            $contactTitle = $data['header']['title'] ?? $data['title'] ?? null;
                            $contactDesc = $data['header']['description'] ?? $data['description'] ?? null;
                        @endphp
                        @if(!empty($contactSubtitle))
                            <div class="mb-4 inline-block text-xs font-bold uppercase tracking-widest text-primary">
                                {{ $contactSubtitle }}
                            </div>
                        @endif
                        @if(!empty($contactTitle))
                            <h2 class="font-mono text-3xl font-bold tracking-tight text-foreground md:text-4xl mb-4" style="text-wrap: balance;">
                                {{ $contactTitle }}
                            </h2>
                        @endif
                        @if(!empty($contactDesc))
                            <p class="text-lg leading-relaxed text-muted-foreground mb-10">
                                {{ $contactDesc }}
                            </p>
                        @endif

                        @php
                            $company = \App\Models\Setting::get('company', []);
                            $website = \App\Models\Setting::get('website', []);
                            $contactEmail = !empty($data['email_to']) ? $data['email_to'] : data_get($company, 'email');
                            $phone = data_get($company, 'phone');
                            $addr = data_get($company, 'address', []);
                            $addrStr = is_array($addr) ? implode(', ', array_filter($addr)) : (string)$addr;

                            $whatsappBtnEnabled = $data['whatsapp_btn_enabled'] ?? true;
                            $whatsappBtnText = data_get($website, 'features.whatsapp_button.text', 'Chamar no WhatsApp');
                            $whatsappNumber = data_get($website, 'features.whatsapp_widget.number');
                            $whatsappUrl = $whatsappNumber 
                                ? "https://wa.me/55" . preg_replace('/[^0-9]/', '', $whatsappNumber) . "?text=" . urlencode('Olá! Vim pela página de contato do site.')
                                : '#';

                            $budgetBtnEnabled = $data['budget_btn_enabled'] ?? true;
                            $budgetBtnTitle = $data['budget_btn_title'] ?? 'Orçamento Grátis Online';
                            $budgetBtnSubtitle = $data['budget_btn_subtitle'] ?? 'Faça uma cotação rápida agora';
                            $budgetBtnUrl = $data['budget_btn_url'] ?? url('/#orcamento');

                            $emailBtnEnabled = $data['email_btn_enabled'] ?? true;
                            $phoneBtnEnabled = $data['phone_btn_enabled'] ?? true;
                            $addressEnabled = $data['address_enabled'] ?? true;
                        @endphp

                        <div class="space-y-4">
                            @if($budgetBtnEnabled)
                            <a href="{{ $budgetBtnUrl }}" class="flex items-center gap-4 rounded-xl bg-primary p-4 text-white shadow transition-all hover:-translate-y-0.5 hover:shadow-md group">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-white/20 text-white transition-transform group-hover:scale-110">
                                    <i data-lucide="calculator" class="h-6 w-6 fill-none stroke-current stroke-2"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-white">{{ $budgetBtnTitle }}</p>
                                    <p class="text-xs font-medium text-white/80">{{ $budgetBtnSubtitle }}</p>
                                </div>
                            </a>
                            @endif

                            @if($whatsappBtnEnabled && $whatsappNumber)
                                <a href="{{ $whatsappUrl }}" target="_blank" class="flex items-center gap-4 rounded-xl bg-[#25D366] p-4 text-white shadow transition-all hover:-translate-y-0.5 hover:bg-[#20ba5a] hover:shadow-md group">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-white/20 text-white transition-transform group-hover:scale-110">
                                        <i data-lucide="message-circle" class="h-6 w-6 fill-current"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-white">{{ $whatsappBtnText }}</p>
                                        <p class="text-xs font-medium text-white/80">{{ $whatsappNumber }}</p>
                                    </div>
                                </a>
                            @endif

                            @if($emailBtnEnabled && $contactEmail)
                                <a href="mailto:{{ $contactEmail }}" class="flex items-center gap-4 rounded-xl border border-primary bg-card p-4 shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md group">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary transition-transform group-hover:scale-110">
                                        <i data-lucide="mail" class="h-6 w-6 fill-current"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-primary">E-mail</p>
                                        <p class="text-xs font-medium text-foreground">{{ $contactEmail }}</p>
                                    </div>
                                </a>
                            @endif

                            @if($phoneBtnEnabled && $phone)
                                @php
                                    $phoneClean = preg_replace('/[^0-9]/', '', $phone);
                                    $phoneLink = strlen($phoneClean) >= 10 ? '55' . $phoneClean : $phoneClean;
                                @endphp
                                <a href="tel:{{ $phoneLink }}" class="flex items-center gap-4 rounded-xl border border-primary bg-card p-4 shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md group">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary transition-transform group-hover:scale-110">
                                        <i data-lucide="phone" class="h-6 w-6 fill-current"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-primary">Telefone</p>
                                        <p class="text-xs font-medium text-foreground">{{ $phone }}</p>
                                    </div>
                                </a>
                            @endif
                        </div>

                        @if($addressEnabled && $addrStr)
                            <div class="mt-8 pt-8 border-t border-border">
                                <div class="flex items-start gap-4">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                        <i data-lucide="map-pin" class="h-5 w-5"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-foreground mb-1">Nosso Endereço</p>
                                        <p class="text-sm text-muted-foreground leading-relaxed">{{ $addrStr }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="rounded-2xl border border-border bg-card p-8 shadow-sm">
                        <livewire:mail.form />
                    </div>
                </div>
            </div>
        </section>
        @break

    @case('map')
        <x-section-map
            :bg-color="$data['background_color'] ?? null"
            :text-color="$data['text_color'] ?? 'light'"
            :title="$data['header']['title'] ?? $data['title'] ?? null"
            :header="$data['header'] ?? []"
            :iframe="$data['iframe_code'] ?? null"
        />
        @break

    @case('rich_text')
        <section class="{{ $data['background_color'] ?? 'bg-background' }} {{ ($data['text_color'] ?? 'light') === 'dark' ? 'text-scheme-dark' : '' }} py-16">
            <div class="mx-auto max-w-7xl px-4 lg:px-8">
                <div class="prose prose-zinc max-w-3xl">
                    {!! $data['content'] !!}
                </div>
            </div>
        </section>
        @break

    @case('cta')
        <x-section-cta-contact
            :bg-color="$data['background_color'] ?? null"
            :text-color="$data['text_color'] ?? 'light'"
            :title="$data['title'] ?? null"
            :subtitle="$data['subtitle'] ?? null"
            :button-label="$data['button_label'] ?? null"
            :button-url="$data['button_url'] ?? null"
        />
        @break


    @case('simple_banner')
        @php
            $bannerImg = !empty($data['image']) ? \Illuminate\Support\Facades\Storage::url($data['image']) : null;
            $bannerLink = $data['link_url'] ?? null;
            $openInNewTab = $data['open_in_new_tab'] ?? true;
            $target = $openInNewTab ? '_blank' : '_self';
            
            $bgColor = $data['background_color'] ?? 'bg-transparent';
            $textColor = ($data['text_color'] ?? 'light') === 'dark' ? 'text-scheme-dark' : '';
        @endphp
        
        @if($bannerImg)
            <section class="{{ $bgColor }} {{ $textColor }} py-4">
                <div class="mx-auto max-w-7xl px-4 lg:px-8">
                    @if($bannerLink)
                        <a href="{{ $bannerLink }}" target="{{ $target }}" class="block overflow-hidden rounded-xl transition-transform hover:-translate-y-1 duration-300">
                            <img src="{{ $bannerImg }}" alt="Banner" class="w-full h-auto object-contain" />
                        </a>
                    @else
                        <div class="block overflow-hidden rounded-xl">
                            <img src="{{ $bannerImg }}" alt="Banner" class="w-full h-auto object-contain" />
                        </div>
                    @endif
                </div>
            </section>
        @endif
        @break

@endswitch
