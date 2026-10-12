@props(['type', 'data', 'page' => null, 'theme' => 'default', 'hideHeader' => false])

@php
    $data = (array) $data;
    $isHome = $page ? ($page->slug === '/' || $page->slug === '') : false;

    if (!function_exists('resolveFilamentImagePath')) {
        function resolveFilamentImagePath($image) {
            if (empty($image)) return null;
            if (is_array($image)) {
                $image = count($image) > 0 ? array_values($image)[0] : null;
            }
            if (empty($image)) return null;
            if (is_string($image)) {
                return str_starts_with($image, 'http') ? $image : \Illuminate\Support\Facades\Storage::url($image);
            }
            if (is_object($image) && method_exists($image, 'temporaryUrl')) {
                return $image->temporaryUrl();
            }
            return null;
        }
    }

    // Ícone escolhido num select com opção "outro"/"other" + campo livre
    $pickIcon = function (?string $selected, ?string $custom, string $default) {
        return in_array($selected, ['other', 'outro'], true) ? ($custom ?: $default) : ($selected ?: $custom ?: $default);
    };
@endphp

@switch($type)
    @case('module_reference')
        @php
            $sectionId = $data['content_section_id'] ?? null;
            $section = $sectionId ? \App\Models\ContentSection::find($sectionId) : null;
        @endphp
        @if($section && $section->is_active)
            @php
                // Estilo e opções avançadas definidos no Page Builder sobrepõem os da seção global
                $overrides = array_filter(\Illuminate\Support\Arr::only($data, [
                    'background_color', 'text_color', 'spacing_top', 'spacing_bottom', 'custom_id', 'custom_css_classes',
                ]));
                $mergedData = array_merge($section->content ?? [], $overrides);
            @endphp
            <x-render-block :type="$section->type" :data="$mergedData" :page="$page" :theme="$theme" />
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

    @case('stats')
        @php
            $statItems = array_values(array_filter((array) ($data['items'] ?? []), fn ($i) => filled($i['value'] ?? null)));
            $statCols = match (min(count($statItems), 4)) {
                1 => 'lg:grid-cols-1',
                2 => 'lg:grid-cols-2',
                3 => 'lg:grid-cols-3',
                default => 'lg:grid-cols-4',
            };
            $overlap = (bool) ($data['overlap'] ?? true);
        @endphp
        @if($statItems)
            <x-ui.section :data="$data" :spacing="$overlap ? 'none' : 'sm'" :background="$overlap ? 'bg-transparent' : 'bg-background'" :class="$overlap ? '!overflow-visible z-20' : '!overflow-visible'">
                <div @class(['-mt-10 md:-mt-14' => $overlap])>
                    <div class="surface-card grid grid-cols-2 gap-px overflow-hidden bg-border/70 shadow-[0_24px_60px_-32px_rgb(0_0_0/0.35)] {{ $statCols }}" data-reveal>
                        @foreach($statItems as $stat)
                            <div class="group flex items-center gap-4 bg-card p-5 transition-colors duration-300 hover:bg-primary-500/[0.04] md:p-7">
                                <x-ui.icon-box :icon="$stat['icon'] ?? 'check-circle'" class="hidden sm:inline-flex" />
                                <div class="min-w-0">
                                    <p class="font-mono text-2xl font-bold tracking-tight text-foreground md:text-3xl" data-count>{{ $stat['value'] }}</p>
                                    <p class="mt-0.5 text-xs font-medium text-muted-foreground md:text-sm">{{ $stat['label'] ?? '' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-ui.section>
        @endif
        @break

    @case('image_with_text')
        @php
            $badgeIcon = $pickIcon($data['badge_icon_select'] ?? 'zap', $data['badge_icon_custom'] ?? null, 'zap');
            $btn1Icon = $pickIcon($data['button_icon_select'] ?? 'arrow-right', $data['button_icon_custom'] ?? null, 'arrow-right');
            $btn2Icon = $pickIcon($data['secondary_button_icon_select'] ?? 'play', $data['secondary_button_icon_custom'] ?? null, 'play');
            $imageUrl = resolveFilamentImagePath($data['image'] ?? null);
            $imageRight = ($data['image_position'] ?? 'left') === 'right';
            $vAlign = match ($data['image_vertical_alignment'] ?? 'center') {
                'start' => 'lg:items-start',
                'end' => 'lg:items-end',
                default => 'lg:items-center',
            };
        @endphp
        <x-ui.section :data="$data">
            <div class="grid items-center gap-10 lg:grid-cols-12 lg:gap-16 {{ $vAlign }}">
                @if($imageUrl)
                    <div @class(['lg:col-span-5', 'lg:order-last' => $imageRight]) data-reveal>
                        <div class="relative mx-auto max-w-lg lg:max-w-none">
                            <div class="absolute -inset-4 -z-10 rounded-[2rem] bg-gradient-to-br from-primary-500/15 via-primary-400/5 to-transparent blur-2xl"></div>
                            <img src="{{ $imageUrl }}" alt="{{ $data['title'] ?? '' }}" class="h-auto w-full object-contain" loading="lazy">
                        </div>
                    </div>
                @endif

                <div @class(['lg:col-span-7' => $imageUrl, 'lg:col-span-12 max-w-3xl' => ! $imageUrl]) data-reveal style="--reveal-delay: 100ms">
                    @if(!empty($data['badge']))
                        <span class="kicker kicker-pill mb-5">
                            <i data-lucide="{{ $badgeIcon }}" class="h-3.5 w-3.5"></i>
                            {{ $data['badge'] }}
                        </span>
                    @endif

                    @if(!empty($data['title']))
                        <h2 class="section-title">{{ $data['title'] }}</h2>
                    @endif

                    @if(!empty($data['description']))
                        <div class="section-lead mt-5 max-w-2xl">{!! nl2br(e($data['description'])) !!}</div>
                    @endif

                    @if((!empty($data['button_text']) && !empty($data['button_url'])) || (!empty($data['secondary_button_text']) && !empty($data['secondary_button_url'])))
                        <div class="mt-8 flex flex-wrap items-center gap-3">
                            @if(!empty($data['button_text']) && !empty($data['button_url']))
                                <x-ui.button :href="$data['button_url']" size="lg" :trailing-icon="$btn1Icon">{{ $data['button_text'] }}</x-ui.button>
                            @endif
                            @if(!empty($data['secondary_button_text']) && !empty($data['secondary_button_url']))
                                <x-ui.button :href="$data['secondary_button_url']" variant="secondary" size="lg" :icon="$btn2Icon">{{ $data['secondary_button_text'] }}</x-ui.button>
                            @endif
                        </div>
                    @endif

                    @if(!empty($data['mini_stats']))
                        <dl @class([
                            'mt-10 grid grid-cols-2 gap-3',
                            'sm:grid-cols-3' => count($data['mini_stats']) === 3,
                            'sm:grid-cols-4' => count($data['mini_stats']) >= 4,
                        ])>
                            @foreach($data['mini_stats'] as $stat)
                                <div class="group flex items-center gap-3 rounded-xl border border-border/70 bg-card/60 p-3 transition-colors hover:border-primary-500/30">
                                    <x-ui.icon-box size="sm" :icon="$pickIcon($stat['icon_select'] ?? 'check-circle', $stat['icon_custom'] ?? null, 'check-circle')" />
                                    <div class="min-w-0 leading-tight">
                                        @if(!empty($stat['title']))
                                            <dt class="text-sm font-semibold text-foreground">{{ $stat['title'] }}</dt>
                                        @endif
                                        @if(!empty($stat['subtitle']))
                                            <dd class="mt-0.5 text-xs text-muted-foreground">{{ $stat['subtitle'] }}</dd>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </dl>
                    @endif
                </div>
            </div>
        </x-ui.section>
        @break

    @case('data_table')
        @php
            $statusMap = [
                'success' => ['Concluído', 'bg-emerald-500/10 text-emerald-700'],
                'warning' => ['Em andamento', 'bg-amber-500/10 text-amber-700'],
                'danger' => ['Atrasado', 'bg-red-500/10 text-red-700'],
                'info' => ['Planejamento', 'bg-sky-500/10 text-sky-700'],
            ];
        @endphp
        <x-ui.section :data="$data" spacing="sm">
            <div class="surface-card overflow-hidden" data-reveal>
                @if(!empty($data['title']) || !empty($data['badge']))
                    <div class="flex items-center justify-between gap-4 border-b border-border/70 px-5 py-4 md:px-6">
                        @if(!empty($data['title']))
                            <h3 class="font-mono text-lg font-bold text-foreground">{{ $data['title'] }}</h3>
                        @endif
                        @if(!empty($data['badge']))
                            <span class="kicker kicker-pill">{{ $data['badge'] }}</span>
                        @endif
                    </div>
                @endif
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-sm">
                        <thead>
                            <tr class="bg-muted/50 text-xs uppercase tracking-wider text-muted-foreground">
                                <th class="px-5 py-3 font-semibold md:px-6">Projeto</th>
                                <th class="px-5 py-3 font-semibold md:px-6">Local</th>
                                <th class="px-5 py-3 font-semibold md:px-6">Status</th>
                                <th class="px-5 py-3 font-semibold md:px-6">Prazo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/60">
                            @foreach($data['rows'] ?? [] as $row)
                                @php [$statusLabel, $statusClass] = $statusMap[$row['col3'] ?? 'success'] ?? $statusMap['success']; @endphp
                                <tr class="transition-colors hover:bg-primary-500/[0.03]">
                                    <td class="px-5 py-3.5 font-medium text-foreground md:px-6">{{ $row['col1'] ?? '' }}</td>
                                    <td class="px-5 py-3.5 text-muted-foreground md:px-6">{{ $row['col2'] ?? '' }}</td>
                                    <td class="px-5 py-3.5 md:px-6">
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClass }}">
                                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-muted-foreground md:px-6">{{ $row['col4'] ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </x-ui.section>
        @break

    @case('featured_testimonial')
        @php
            // background_image aqui é o fundo do cartão, não da seção
            $quoteBg = resolveFilamentImagePath($data['background_image'] ?? null);
            $authorImg = resolveFilamentImagePath($data['author_image'] ?? null);
        @endphp
        <x-ui.section :data="\Illuminate\Support\Arr::except($data, ['background_image'])" spacing="sm">
            <figure class="dark relative isolate overflow-hidden rounded-[calc(var(--radius-card)*1.5)] bg-primary-950 px-6 py-12 text-white md:px-14 md:py-16" data-reveal>
                @if($quoteBg)
                    <img src="{{ $quoteBg }}" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover opacity-25 mix-blend-luminosity" loading="lazy">
                @endif
                <div class="absolute inset-0 -z-10 bg-gradient-to-br from-primary-900/80 via-primary-950/90 to-black/90"></div>
                <div class="absolute -right-24 -top-24 -z-10 h-72 w-72 rounded-full bg-primary-500/30 blur-3xl"></div>

                <i data-lucide="quote" class="mb-6 h-10 w-10 text-primary-400"></i>
                <blockquote class="max-w-3xl font-mono text-xl font-medium leading-snug md:text-3xl" style="text-wrap: balance;">
                    “{{ trim($data['quote'] ?? '', '"“” ') }}”
                </blockquote>
                <figcaption class="mt-8 flex items-center gap-4">
                    @if($authorImg)
                        <img src="{{ $authorImg }}" alt="{{ $data['author'] ?? '' }}" class="h-12 w-12 rounded-full object-cover ring-2 ring-primary-400/60 ring-offset-2 ring-offset-primary-950">
                    @endif
                    <div>
                        <div class="font-semibold">{{ $data['author'] ?? '' }}</div>
                        @if(!empty($data['role']))
                            <div class="text-sm text-white/60">{{ $data['role'] }}</div>
                        @endif
                    </div>
                </figcaption>
            </figure>
        </x-ui.section>
        @break

    @case('hero')
        @php
            $heroBadge = $data['header']['subtitle'] ?? $data['badge'] ?? '';
            $heroTitle = $data['header']['title'] ?? $data['title'] ?? '';
            $heroSubtitle = $data['header']['description'] ?? $data['subtitle'] ?? '';
            $primaryIcon = $pickIcon($data['primary_button_icon_select'] ?? '', $data['primary_button_icon'] ?? '', '');
            $secondaryIcon = $pickIcon($data['secondary_button_icon_select'] ?? '', $data['secondary_button_icon'] ?? '', '');
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
            :alignment="$data['header']['alignment'] ?? 'center'"
            :overlay-enabled="$data['overlay_enabled'] ?? true"
            :overlay-theme="$data['overlay_theme'] ?? 'dark'"
            :theme="$theme"
            :stats="$data['stats'] ?? []"
            :spacing="\App\Support\SectionStyle::spacing($data, 'md')"
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
            :data="$data"
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
            $displayItems = $data['items'] ?? [];
            if (empty($displayItems)) {
                // Conteúdo de demonstração
                $displayItems = [
                    ['title' => 'Concreto Usinado', 'subtitle' => 'Alta Performance', 'description' => 'Concreto de alta resistência com controle rigoroso de qualidade. Entregue pontualmente na sua obra.', 'icon' => 'truck', 'bullets' => ['Até 50 MPa', 'Laudo técnico', 'Frota moderna'], 'cta_label' => 'Fazer Orçamento', 'cta_url' => '#orcamento'],
                    ['title' => 'Bombeamento', 'subtitle' => 'Alcance Máximo', 'description' => 'Serviço de bombeamento eficiente para lajes e locais de difícil acesso, otimizando o tempo da sua equipe.', 'icon' => 'arrow-up-circle', 'bullets' => ['Bomba lança', 'Bomba estacionária', 'Operadores treinados'], 'cta_label' => 'Fazer Orçamento', 'cta_url' => '#orcamento'],
                    ['title' => 'Locação de Máquinas', 'subtitle' => 'Frota Renovada', 'description' => 'Equipamentos pesados para terraplanagem, escavação e compactação. Disponibilidade imediata.', 'icon' => 'tractor', 'bullets' => ['Retroescavadeiras', 'Rolos compactadores', 'Manutenção em dia'], 'cta_label' => 'Fazer Orçamento', 'cta_url' => '#orcamento'],
                ];
            }
            $useSlider = count($displayItems) > 3;
            $sliderId = 'services-swiper-' . Str::random(6);
            $showServicesHeader = ! $hideHeader && ($data['header']['visible'] ?? true);
        @endphp
        <x-ui.section :data="$data">
            @if($showServicesHeader)
                <x-ui.section-header
                    :header="$data['header'] ?? []"
                    default-align="center"
                    :fallback-title="$data['title'] ?? 'Nossos Serviços'"
                    :fallback-subtitle="$data['badge'] ?? 'O que fazemos'"
                    :fallback-description="$data['description'] ?? 'Soluções completas com qualidade garantida.'"
                >
                    @if($useSlider)
                        <x-slot:actions><x-ui.slider-nav :id="$sliderId" class="hidden md:flex" /></x-slot:actions>
                    @endif
                </x-ui.section-header>
            @endif

            @if($useSlider)
                <div class="swiper {{ $sliderId }} !overflow-visible md:!overflow-hidden">
                    <div class="swiper-wrapper">
            @else
                <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @endif

            @foreach($displayItems as $index => $service)
                @if($useSlider)<div class="swiper-slide !h-auto">@endif
                <article class="surface-card surface-card-interactive group flex h-full flex-col p-6 md:p-7" data-spotlight data-reveal style="--reveal-delay: {{ ($index % 3) * 90 }}ms">
                    <div class="mb-6 flex items-start justify-between">
                        <x-ui.icon-box size="lg" :icon="$service['icon'] ?? 'droplets'" />
                        <span class="font-mono text-sm font-medium text-muted-foreground/40 transition-colors group-hover:text-primary-ink">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    @if(!empty($service['subtitle']))
                        <p class="mb-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-muted-foreground">{{ $service['subtitle'] }}</p>
                    @endif
                    <h3 class="text-xl font-bold text-foreground">{{ $service['title'] ?? '' }}</h3>

                    @if(!empty($service['description']))
                        <p class="mt-3 text-sm leading-relaxed text-muted-foreground">{{ $service['description'] }}</p>
                    @endif

                    @if(!empty($service['bullets']))
                        <ul class="mt-5 flex flex-wrap gap-2">
                            @foreach((array) $service['bullets'] as $bullet)
                                <li class="inline-flex items-center gap-1.5 rounded-full border border-border/70 px-2.5 py-1 text-xs font-medium text-foreground/80">
                                    <i data-lucide="check" class="h-3 w-3 text-primary-ink"></i>
                                    {{ is_array($bullet) ? ($bullet['text'] ?? $bullet['value'] ?? '') : $bullet }}
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="mt-auto pt-6">
                        <a href="{{ $service['cta_url'] ?? '#orcamento' }}" class="link-arrow w-full justify-between border-t border-border/60 pt-5">
                            {{ $service['cta_label'] ?? 'Solicitar orçamento' }}
                            <i data-lucide="arrow-right"></i>
                        </a>
                    </div>
                </article>
                @if($useSlider)</div>@endif
            @endforeach

            @if($useSlider)
                    </div>
                    <div class="mt-8 flex items-center justify-between gap-4">
                        <div class="swiper-pagination !static !w-auto"></div>
                        <x-ui.slider-nav :id="$sliderId" :class="$showServicesHeader ? 'md:hidden' : ''" />
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
        @break

    @case('timeline')
        <x-section-timeline
            :data="$data"
            :header="$data['header'] ?? array_filter(['title' => $data['title'] ?? null])"
            :steps="$data['steps'] ?? []"
        />
        @break

    @case('commercial_partners')
        <x-ui.section :data="$data">
            <livewire:commercial-partners :data="$data" />
        </x-ui.section>
        @break

    @case('showcase')
    @case('equipment_showcase')
        <x-ui.section :data="$data">
            @unless($hideHeader)
                <x-ui.section-header
                    :header="$data['header'] ?? []"
                    default-align="center"
                    :fallback-title="$data['title'] ?? null"
                    :fallback-subtitle="$data['badge'] ?? null"
                    :fallback-description="$data['description'] ?? null"
                />
            @endunless

            @if($type === 'showcase')
                <livewire:showcase-feed :limit="$data['limit'] ?? 6" />
            @else
                <livewire:equipment-showcase />
            @endif
        </x-ui.section>
        @break

    @case('faq')
        <x-section-faq
            :data="$data"
            :header="$data['header'] ?? array_filter(['title' => $data['title'] ?? null])"
            :items="$data['items'] ?? []"
        />
        @break

    @case('testimonials')
        <x-section-testimonials
            :data="$data"
            :header="$data['header'] ?? array_filter(['title' => $data['title'] ?? null])"
            :items="$data['items'] ?? []"
        />
        @break

    @case('team')
        <x-section-team
            :data="$data"
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
            :spacing="\App\Support\SectionStyle::spacing($data, 'md')"
            :overlay-data="[
                'enabled' => $data['background_overlay_enabled'] ?? false,
                'type' => $data['background_overlay_type'] ?? 'dark',
                'opacity' => $data['background_overlay_opacity'] ?? '50',
            ]"
        />
        @break

    @case('differentials')
        <x-section-differentials
            :data="$data"
            :title="$data['header']['title'] ?? $data['title'] ?? null"
            :subtitle="$data['header']['subtitle'] ?? $data['subtitle'] ?? $data['badge'] ?? null"
            :description="$data['header']['description'] ?? $data['description'] ?? null"
            :header="$data['header'] ?? []"
            :items="$data['items'] ?? []"
        />
        @break

    @case('contact_banner')
        <x-section-contact-banner
            :data="$data"
            :badge="$data['badge'] ?? null"
            :title="$data['title'] ?? null"
            :description="$data['description'] ?? null"
            :whatsapp-enabled="$data['whatsapp_enabled'] ?? true"
            :call-enabled="$data['call_enabled'] ?? true"
            :email-enabled="$data['email_enabled'] ?? true"
        />
        @break

    @case('calculator')
        <x-ui.section :data="$data" spacing="sm">
            <livewire:calculator
                :bg-color="$data['background_color'] ?? ''"
                :title="$data['header']['title'] ?? $data['title'] ?? null"
                :subtitle="$data['header']['subtitle'] ?? $data['badge'] ?? null"
                :description="$data['header']['description'] ?? $data['description'] ?? null"
                :header-visible="$data['header']['visible'] ?? true"
                :header-alignment="$data['header']['alignment'] ?? 'left'"
            />
        </x-ui.section>
        @break

    @case('payment_offer')
        @php
            $offerHeader = $data['header'] ?? [];
            $offerAlign = $offerHeader['alignment'] ?? 'center';
            $offerTitle = $offerHeader['title'] ?? $data['title'] ?? null;
            $offerSubtitle = $offerHeader['subtitle'] ?? $data['badge'] ?? null;
            $offerDesc = $offerHeader['description'] ?? $data['subtitle'] ?? null;
            $showOfferHeader = ! $hideHeader && ($offerHeader['visible'] ?? true) && (filled($offerTitle) || filled($offerSubtitle));

            $whatsappNumber = \App\Models\Setting::get('website.features.whatsapp_widget.number', '');
            $buttonUrl = $data['button_url'] ?? null;
            $offerUrl = $whatsappNumber && empty($buttonUrl)
                ? 'https://wa.me/55' . preg_replace('/[^0-9]/', '', $whatsappNumber) . '?text=' . urlencode('Olá! Gostaria de fazer um orçamento.')
                : $buttonUrl;
            $methodsUrl = resolveFilamentImagePath($data['payment_methods_image'] ?? null);
            $isClickableBanner = filled($offerUrl) && empty($data['button_label']);
            $tag = $isClickableBanner ? 'a' : 'div';

            $methods = [['scan-line', 'Pix'], ['barcode', 'Boleto'], ['credit-card', 'Visa'], ['credit-card', 'Master'], ['credit-card', 'Amex'], ['credit-card', 'Elo']];
            $cardData = \Illuminate\Support\Arr::only($data, ['background_color', 'text_color', 'background_image', 'background_image_fit', 'background_image_scale', 'background_image_position', 'background_image_opacity', 'background_overlay_enabled', 'background_overlay_type', 'background_overlay_opacity']);
        @endphp
        {{-- A cor e a imagem de fundo do bloco pintam o cartão; a seção em volta fica transparente --}}
        <x-ui.section :data="\Illuminate\Support\Arr::except($data, array_keys($cardData))" spacing="xs" background="bg-transparent">
            <{{ $tag }} @if($isClickableBanner) href="{{ $offerUrl }}" target="_blank" rel="noopener noreferrer" @endif
                @class([
                    'surface-card group relative isolate flex flex-col gap-8 overflow-hidden p-6 md:p-10 lg:flex-row lg:items-center lg:justify-between',
                    $data['background_color'] ?? 'bg-card',
                    \App\Support\SectionStyle::schemeClass(\App\Support\SectionStyle::scheme($data)),
                    'surface-card-interactive cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500' => $isClickableBanner,
                    'lg:flex-col lg:items-center text-center' => $offerAlign === 'center',
                ])
                data-spotlight data-reveal>
                <x-ui.section-background :data="$cardData" />

                @if($showOfferHeader)
                    <div @class(['relative z-10 max-w-2xl', 'mx-auto' => $offerAlign === 'center'])>
                        @if(filled($offerSubtitle))
                            <span class="kicker mb-3">{{ $offerSubtitle }}</span>
                        @endif
                        @if(filled($offerTitle))
                            <h2 class="font-mono text-2xl font-bold tracking-tight text-foreground md:text-3xl" style="text-wrap: balance;">{{ $offerTitle }}</h2>
                        @endif
                        @if(filled($offerDesc))
                            <p class="mt-3 text-base leading-relaxed text-muted-foreground">{{ $offerDesc }}</p>
                        @endif
                    </div>
                @endif

                <div @class(['relative z-10 flex flex-col gap-6', 'items-center' => $offerAlign === 'center', 'lg:items-end' => $offerAlign !== 'center'])>
                    @if(!empty($data['button_label']))
                        <x-ui.button :href="$offerUrl" target="_blank" size="lg" icon="message-circle" trailing-icon="arrow-right">{{ $data['button_label'] }}</x-ui.button>
                    @endif

                    @if(($data['show_payment_methods'] ?? true) !== false)
                        <div @class(['flex flex-col gap-3', 'items-center' => $offerAlign === 'center', 'lg:items-end' => $offerAlign !== 'center'])>
                            <span class="text-[11px] font-bold uppercase tracking-[0.18em] text-muted-foreground">Pague com</span>
                            @if($methodsUrl)
                                <img src="{{ $methodsUrl }}" alt="Meios de pagamento" class="h-10 w-auto object-contain opacity-80 transition-opacity group-hover:opacity-100 md:h-12" loading="lazy">
                            @else
                                <ul class="flex flex-wrap justify-center gap-2">
                                    @foreach($methods as [$methodIcon, $methodLabel])
                                        <li class="inline-flex items-center gap-1.5 rounded-lg border border-border/70 bg-card/60 px-2.5 py-1.5 text-xs font-semibold text-foreground/80 transition-colors hover:border-primary-500/40 hover:text-primary-ink">
                                            <i data-lucide="{{ $methodIcon }}" class="h-4 w-4"></i>
                                            {{ $methodLabel }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif
                </div>
            </{{ $tag }}>
        </x-ui.section>
        @break

    @case('budget_form')
        <x-ui.section :data="$data" id="orcamento" spacing="sm" background="bg-muted/50">
            <livewire:budget
                :bg-color="$data['background_color'] ?? ''"
                :title="$data['header']['title'] ?? $data['title'] ?? null"
                :subtitle="$data['header']['subtitle'] ?? $data['badge'] ?? 'Orçamento Online'"
                :description="$data['header']['description'] ?? $data['description'] ?? null"
                :header-visible="$data['header']['visible'] ?? true"
                :header-alignment="$data['header']['alignment'] ?? 'left'"
            />
        </x-ui.section>
        @break

    @case('cta_contact')
    @case('contact_form')
        @php
            $company = \App\Models\Setting::get('company', []);
            $website = \App\Models\Setting::get('website', []);
            $contactEmail = !empty($data['email_to']) ? $data['email_to'] : data_get($company, 'email');
            $phone = data_get($company, 'phone');
            $addr = data_get($company, 'address', []);
            $addrStr = is_array($addr) ? implode(', ', array_filter($addr)) : (string) $addr;

            $whatsappNumber = data_get($website, 'features.whatsapp_widget.number');
            $phoneClean = preg_replace('/[^0-9]/', '', (string) $phone);

            // Canais de contato na ordem de exibição; cada um pode ser desligado no bloco
            $channels = array_filter([
                ($data['budget_btn_enabled'] ?? true) ? [
                    'href' => $data['budget_btn_url'] ?? url('/#orcamento'), 'icon' => 'calculator', 'featured' => true,
                    'title' => $data['budget_btn_title'] ?? 'Orçamento Grátis Online', 'text' => $data['budget_btn_subtitle'] ?? 'Faça uma cotação rápida agora',
                ] : null,
                ($data['whatsapp_btn_enabled'] ?? true) && $whatsappNumber ? [
                    'href' => 'https://wa.me/55' . preg_replace('/[^0-9]/', '', $whatsappNumber) . '?text=' . urlencode('Olá! Vim pela página de contato do site.'),
                    'icon' => 'message-circle', 'whatsapp' => true, 'target' => '_blank',
                    'title' => data_get($website, 'features.whatsapp_button.text', 'Chamar no WhatsApp'), 'text' => $whatsappNumber,
                ] : null,
                ($data['email_btn_enabled'] ?? true) && $contactEmail ? [
                    'href' => 'mailto:' . $contactEmail, 'icon' => 'mail', 'title' => 'E-mail', 'text' => $contactEmail,
                ] : null,
                ($data['phone_btn_enabled'] ?? true) && $phone ? [
                    'href' => 'tel:' . (strlen($phoneClean) >= 10 ? '55' . $phoneClean : $phoneClean), 'icon' => 'phone', 'title' => 'Telefone', 'text' => $phone,
                ] : null,
            ]);
        @endphp
        <x-ui.section :data="$data">
            <div class="grid items-start gap-10 lg:grid-cols-12 lg:gap-14">
                <div class="lg:col-span-5">
                    <x-ui.section-header
                        :header="$data['header'] ?? []"
                        :fallback-title="$data['title'] ?? null"
                        :fallback-subtitle="$data['subtitle'] ?? null"
                        :fallback-description="$data['description'] ?? null"
                        align="left"
                        class="!mb-8"
                    />

                    <div class="grid gap-3">
                        @foreach($channels as $i => $channel)
                            <a href="{{ $channel['href'] }}" @if(!empty($channel['target'])) target="_blank" rel="noopener noreferrer" @endif
                               @class([
                                   'group flex items-center gap-4 rounded-card p-4 transition-all duration-300 ease-out-expo hover:-translate-y-0.5',
                                   'bg-primary-500 text-primary-foreground shadow-md shadow-primary-700/20 hover:bg-primary-600' => !empty($channel['featured']),
                                   'bg-[#25D366] text-white shadow-md shadow-green-700/20 hover:bg-[#1ebe5b]' => !empty($channel['whatsapp']),
                                   'surface-card hover:border-primary-500/40 hover:shadow-md' => empty($channel['featured']) && empty($channel['whatsapp']),
                               ])
                               data-reveal style="--reveal-delay: {{ $i * 70 }}ms">
                                @if(!empty($channel['featured']) || !empty($channel['whatsapp']))
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/20 transition-transform duration-300 group-hover:scale-110">
                                        <i data-lucide="{{ $channel['icon'] }}" class="h-5 w-5"></i>
                                    </span>
                                @else
                                    <x-ui.icon-box :icon="$channel['icon']" />
                                @endif
                                <span class="min-w-0 flex-1">
                                    <span class="block font-semibold">{{ $channel['title'] }}</span>
                                    <span @class(['block truncate text-sm', 'opacity-80' => !empty($channel['featured']) || !empty($channel['whatsapp']), 'text-muted-foreground' => empty($channel['featured']) && empty($channel['whatsapp'])])>{{ $channel['text'] }}</span>
                                </span>
                                <i data-lucide="arrow-up-right" class="h-4 w-4 shrink-0 opacity-50 transition-all duration-300 group-hover:-translate-y-0.5 group-hover:translate-x-0.5 group-hover:opacity-100"></i>
                            </a>
                        @endforeach
                    </div>

                    @if(($data['address_enabled'] ?? true) && $addrStr)
                        <div class="mt-6 flex items-start gap-3 border-t border-border/60 pt-6">
                            <x-ui.icon-box size="sm" icon="map-pin" :react="false" />
                            <div>
                                <p class="text-sm font-semibold text-foreground">Nosso endereço</p>
                                <p class="mt-0.5 text-sm leading-relaxed text-muted-foreground">{{ $addrStr }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="surface-card text-scheme-light p-6 md:p-8 lg:col-span-7" data-reveal style="--reveal-delay: 120ms">
                    <livewire:mail.form />
                </div>
            </div>
        </x-ui.section>
        @break

    @case('map')
        <x-section-map
            :data="$data"
            :title="$data['header']['title'] ?? $data['title'] ?? null"
            :header="$data['header'] ?? []"
            :iframe="$data['iframe_code'] ?? null"
        />
        @break

    @case('rich_text')
        <x-ui.section :data="$data" spacing="sm" container="narrow">
            <div class="prose prose-zinc max-w-none prose-headings:font-mono prose-headings:tracking-tight prose-headings:text-foreground prose-p:text-muted-foreground prose-a:text-primary-ink prose-a:underline-offset-4 hover:prose-a:text-primary-700 prose-strong:text-foreground prose-li:marker:text-primary-500">
                {!! $data['content'] ?? '' !!}
            </div>
        </x-ui.section>
        @break

    @case('cta')
        <x-section-cta-contact
            :data="$data"
            :image="$data['image'] ?? null"
            :image-size="$data['image_size'] ?? 155"
            :image-offset-x="$data['image_offset_x'] ?? 0"
            :image-offset-y="$data['image_offset_y'] ?? 0"
            :show-features="$data['show_features'] ?? true"
            :title="$data['title'] ?? null"
            :subtitle="$data['subtitle'] ?? null"
            :button-label="$data['button_label'] ?? null"
            :button-url="$data['button_url'] ?? null"
        />
        @break

    @case('cards')
        @php
            $cardsCols = match ($data['columns'] ?? '3') {
                '2' => 'sm:grid-cols-2',
                '4' => 'sm:grid-cols-2 lg:grid-cols-4',
                default => 'sm:grid-cols-2 lg:grid-cols-3',
            };
        @endphp
        <x-ui.section :data="$data" background="bg-background">
            <x-ui.section-header
                :header="$data['header'] ?? []"
                default-align="center"
                :fallback-title="$data['title'] ?? null"
                :fallback-subtitle="$data['badge'] ?? null"
                :fallback-description="$data['description'] ?? null"
            />

            <div class="grid gap-5 {{ $cardsCols }}">
                @foreach($data['items'] ?? [] as $index => $item)
                    <div class="surface-card surface-card-interactive group p-6 md:p-7" data-spotlight data-reveal style="--reveal-delay: {{ ($index % 4) * 80 }}ms">
                        <x-ui.icon-box size="lg" :icon="$item['icon'] ?? 'star'" class="mb-5" />
                        <h3 class="text-lg font-bold text-foreground">{{ $item['title'] ?? '' }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-muted-foreground">{{ $item['description'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </x-ui.section>
        @break

    @case('simple_banner')
        @php
            $bannerImg = resolveFilamentImagePath($data['image'] ?? null);
            $bannerLink = $data['link_url'] ?? null;
        @endphp
        @if($bannerImg)
            <x-ui.section :data="$data" spacing="xs" background="bg-transparent">
                @if($bannerLink)
                    <a href="{{ $bannerLink }}" target="{{ ($data['open_in_new_tab'] ?? true) ? '_blank' : '_self' }}" class="group block overflow-hidden rounded-card shadow-sm transition-all duration-500 ease-out-expo hover:-translate-y-1 hover:shadow-xl" data-reveal>
                        <img src="{{ $bannerImg }}" alt="Banner" class="h-auto w-full object-contain transition-transform duration-700 ease-out-expo group-hover:scale-[1.02]" loading="lazy" />
                    </a>
                @else
                    <div class="overflow-hidden rounded-card" data-reveal>
                        <img src="{{ $bannerImg }}" alt="Banner" class="h-auto w-full object-contain" loading="lazy" />
                    </div>
                @endif
            </x-ui.section>
        @endif
        @break

@endswitch
