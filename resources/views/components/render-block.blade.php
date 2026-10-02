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
            :items="$data['items'] ?? []"
        />
        @break

    @case('services')
        <x-section-services
            :bg-color="$data['background_color'] ?? null"
            :text-color="$data['text_color'] ?? 'light'"
            :title="$data['header']['title'] ?? $data['title'] ?? null"
            :badge="$data['header']['subtitle'] ?? $data['badge'] ?? null"
            :description="$data['header']['description'] ?? $data['description'] ?? null"
            :items="$data['items'] ?? []"
            :hide-header="$hideHeader"
        />
        @break

    @case('timeline')
        <x-section-timeline
            :bg-color="$data['background_color'] ?? null"
            :text-color="$data['text_color'] ?? 'light'"
            :title="$data['header']['title'] ?? $data['title'] ?? null"
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
            :items="$data['items'] ?? []"
        />
        @break

    @case('testimonials')
        <x-section-testimonials
            :bg-color="$data['background_color'] ?? null"
            :text-color="$data['text_color'] ?? 'light'"
            :title="$data['header']['title'] ?? $data['title'] ?? null"
            :items="$data['items'] ?? []"
        />
        @break

    @case('coverage')
        <livewire:section-coverage
            :bg-color="$data['background_color'] ?? null"
            :text-color="$data['text_color'] ?? 'light'"
            :title="$data['header']['title'] ?? $data['title'] ?? null"
            :cities="$data['cities'] ?? []"
        />
        @break

    @case('differentials')
        <x-section-differentials
            :bg-color="$data['background_color'] ?? null"
            :text-color="$data['text_color'] ?? 'light'"
            :title="$data['header']['title'] ?? $data['title'] ?? null"
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
            
            $bgColor = $data['background_color'] ?? '';
            $isPrimaryBg = str_contains($bgColor, 'bg-primary');
            $badgeTextClass = $isPrimaryBg ? 'text-[color-mix(in_srgb,var(--primary),black_85%)]' : 'text-primary';
            $badgeBgClass = $isPrimaryBg ? 'bg-[color-mix(in_srgb,var(--primary),black_85%)]' : 'bg-primary';
            $badgeBorderClass = $isPrimaryBg ? 'border-black/30 bg-black/20' : 'border-primary/20 bg-primary/5';
            
            $bgImg = !empty($data['background_image']) ? \Illuminate\Support\Facades\Storage::url($data['background_image']) : null;
            $bgFit = $data['background_image_fit'] ?? 'cover';
            $bgPos = $data['background_image_position'] ?? 'center';
            $bgOp = ($data['background_image_opacity'] ?? '100') / 100;
            $bgPullUpAmount = (int) ($data['background_image_pull_up'] ?? 0);
            $bgPullUp = $bgPullUpAmount > 0;
            
            $overflowClass = $bgPullUp ? '' : 'overflow-hidden';
            $bgDivClasses = 'absolute inset-x-0 bottom-0 z-0 pointer-events-none bg-no-repeat';
            $topStyle = $bgPullUp ? "-{$bgPullUpAmount}%" : "0";
            $bgPositionStyle = $bgPullUp ? ($bgPos === 'center' ? 'center bottom' : $bgPos . ' bottom') : $bgPos;
        @endphp
        <section class="{{ $data['background_color'] ?? 'bg-background' }} {{ ($data['text_color'] ?? 'light') === 'dark' ? 'text-scheme-dark' : '' }} py-8 lg:py-12 relative {{ $overflowClass }}">
            @if($bgImg)
                <div class="{{ $bgDivClasses }}" style="top: {{ $topStyle }}; background-image: url('{{ $bgImg }}'); background-size: {{ $bgFit }}; background-position: {{ $bgPositionStyle }}; opacity: {{ $bgOp }};"></div>
            @endif
            <div class="mx-auto max-w-7xl px-4 lg:px-8 relative z-10">
                @if(!empty($calcTitle) || !empty($calcSubtitle))
                    <div class="mb-10">
                        @if(!empty($calcSubtitle))
                            <div class="mb-4 inline-flex items-center gap-2 rounded-full border {{ $badgeBorderClass }} px-4 py-1.5">
                                <span class="h-1.5 w-1.5 rounded-full {{ $badgeBgClass }} animate-pulse"></span>
                                <span class="font-mono text-[10px] font-bold uppercase tracking-[0.2em] {{ $badgeTextClass }}">{{ $calcSubtitle }}</span>
                            </div>
                        @endif
                        @if(!empty($calcTitle))
                            <h2 class="font-mono text-3xl font-bold tracking-tight text-foreground md:text-4xl" style="text-wrap: balance;">
                                {{ $calcTitle }}
                            </h2>
                        @endif
                        @if(!empty($calcDesc))
                            <p class="mt-3 text-lg leading-relaxed text-muted-foreground">
                                {{ $calcDesc }}
                            </p>
                        @endif
                    </div>
                @endif
                <livewire:calculator :bg-color="$data['background_color'] ?? ''" />
            </div>
        </section>
        @break

    @case('payment_offer')
        @php
            $offerTitle = $data['header']['title'] ?? $data['title'] ?? null;
            $offerSubtitle = $data['header']['subtitle'] ?? $data['badge'] ?? null;
            $offerDesc = $data['header']['description'] ?? $data['subtitle'] ?? null;
            
            $bgColor = $data['background_color'] ?? '';
            $isPrimaryBg = str_contains($bgColor, 'bg-primary');
            $badgeTextClass = $isPrimaryBg ? 'text-[color-mix(in_srgb,var(--primary),black_85%)]' : 'text-primary';
            $badgeBgClass = $isPrimaryBg ? 'bg-[color-mix(in_srgb,var(--primary),black_85%)]' : 'bg-primary';
            $badgeBorderClass = $isPrimaryBg ? 'border-black/30 bg-black/20' : 'border-primary/20 bg-primary/10';
            
            $bgImg = !empty($data['background_image']) ? \Illuminate\Support\Facades\Storage::url($data['background_image']) : null;
            $bgFit = $data['background_image_fit'] ?? 'cover';
            $bgPos = $data['background_image_position'] ?? 'center';
            $bgOp = ($data['background_image_opacity'] ?? '100') / 100;
            $bgPullUpAmount = (int) ($data['background_image_pull_up'] ?? 0);
            $bgPullUp = $bgPullUpAmount > 0;
            
            $overflowClass = $bgPullUp ? '' : 'overflow-hidden';
            $bgDivClasses = 'absolute inset-x-0 bottom-0 z-0 pointer-events-none bg-no-repeat';
            $topStyle = $bgPullUp ? "-{$bgPullUpAmount}%" : "0";
            $bgPositionStyle = $bgPullUp ? ($bgPos === 'center' ? 'center bottom' : $bgPos . ' bottom') : $bgPos;
            
            $website = \App\Models\Setting::get('website', []);
            $whatsappNumber = data_get($website, 'features.whatsapp_widget.number', '');
            $buttonUrl = $data['button_url'] ?? null;
            $whatsappUrl = $whatsappNumber && empty($buttonUrl)
                ? "https://wa.me/55" . preg_replace('/[^0-9]/', '', $whatsappNumber) . "?text=" . urlencode('Olá! Gostaria de fazer um orçamento.')
                : $buttonUrl;
                
            $methodsUrl = !empty($data['payment_methods_image']) 
                ? (str_starts_with($data['payment_methods_image'], 'http') ? $data['payment_methods_image'] : \Illuminate\Support\Facades\Storage::url($data['payment_methods_image'])) 
                : null;
        @endphp
        <section class="{{ $data['background_color'] ?? 'bg-background' }} {{ ($data['text_color'] ?? 'light') === 'dark' ? 'text-scheme-dark' : '' }} py-12 lg:py-16 relative {{ $overflowClass }}">
            @if($bgImg)
                <div class="{{ $bgDivClasses }}" style="top: {{ $topStyle }}; background-image: url('{{ $bgImg }}'); background-size: {{ $bgFit }}; background-position: {{ $bgPositionStyle }}; opacity: {{ $bgOp }};"></div>
            @endif
            <div class="mx-auto max-w-4xl px-4 lg:px-8 relative z-10 text-center">
                @if(!empty($offerTitle) || !empty($offerSubtitle))
                    <div class="mb-10">
                        @if(!empty($offerSubtitle))
                            <div class="mb-4 inline-flex items-center gap-2 rounded-full border {{ $badgeBorderClass }} px-4 py-1.5 backdrop-blur-md shadow-lg shadow-primary/5">
                                <span class="h-1.5 w-1.5 rounded-full {{ $badgeBgClass }} animate-pulse shadow-md"></span>
                                <span class="font-mono text-[10px] font-bold uppercase tracking-[0.2em] {{ $badgeTextClass }}">{{ $offerSubtitle }}</span>
                            </div>
                        @endif
                        @if(!empty($offerTitle))
                            <h2 class="font-mono text-3xl font-extrabold tracking-tight text-foreground md:text-5xl drop-shadow-sm mb-4" style="text-wrap: balance;">
                                {{ $offerTitle }}
                            </h2>
                        @endif
                        @if(!empty($offerDesc))
                            <p class="text-lg font-medium leading-relaxed text-muted-foreground max-w-2xl mx-auto" style="text-wrap: balance;">
                                {{ $offerDesc }}
                            </p>
                        @endif
                    </div>
                @endif
                
                <div class="flex flex-col items-center gap-6 mt-8">
                    @if(!empty($data['button_label']))
                    <div class="relative group">
                        <div class="absolute -inset-1.5 rounded-full bg-gradient-to-r from-[#25D366] to-[#128C7E] opacity-60 blur-md transition duration-1000 group-hover:opacity-100 group-hover:duration-200"></div>
                        <a href="{{ $whatsappUrl }}" target="_blank"
                           class="relative inline-flex items-center gap-3 rounded-full bg-[#25D366] px-8 py-4 text-sm sm:text-base font-extrabold text-white shadow-2xl transition-all hover:scale-105 hover:bg-[#20ba5a] border border-white/10">
                            <i data-lucide="message-circle" class="h-5 w-5"></i>
                            {{ $data['button_label'] }}
                        </a>
                    </div>
                    @endif

                    <!-- Meios de pagamento -->
                    <div class="mt-4 opacity-70 transition-opacity hover:opacity-100">
                        @if($methodsUrl)
                            <img src="{{ $methodsUrl }}" alt="Meios de Pagamento Aceitos" class="h-16 sm:h-20 md:h-24 w-auto object-contain mx-auto filter drop-shadow-lg">
                        @else
                            <div class="flex items-center justify-center gap-5 flex-wrap text-foreground/40 [.text-scheme-dark_&]:text-white/40 text-xs sm:text-sm font-semibold tracking-widest">
                                <div class="flex items-center gap-1.5 hover:text-[#32BCAD] transition-colors cursor-default">
                                    <i data-lucide="scan-line" class="h-5 w-5"></i>
                                    <span>PIX</span>
                                </div>
                                <div class="flex items-center gap-1.5 hover:text-foreground [.text-scheme-dark_&]:hover:text-white transition-colors cursor-default">
                                    <i data-lucide="credit-card" class="h-5 w-5"></i>
                                    <span>VISA</span>
                                </div>
                                <div class="flex items-center gap-1.5 hover:text-foreground [.text-scheme-dark_&]:hover:text-white transition-colors cursor-default">
                                    <i data-lucide="credit-card" class="h-5 w-5"></i>
                                    <span>MASTER</span>
                                </div>
                                <div class="flex items-center gap-1.5 hover:text-foreground [.text-scheme-dark_&]:hover:text-white transition-colors cursor-default">
                                    <i data-lucide="credit-card" class="h-5 w-5"></i>
                                    <span>AMEX</span>
                                </div>
                                <div class="flex items-center gap-1.5 hover:text-foreground [.text-scheme-dark_&]:hover:text-white transition-colors cursor-default">
                                    <i data-lucide="credit-card" class="h-5 w-5"></i>
                                    <span>ELO</span>
                                </div>
                                <div class="flex items-center gap-1.5 hover:text-foreground [.text-scheme-dark_&]:hover:text-white transition-colors cursor-default">
                                    <i data-lucide="credit-card" class="h-5 w-5"></i>
                                    <span>HIPERCARD</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
        @break

    @case('budget_form')
        @php
            $budgetTitle = $data['header']['title'] ?? $data['title'] ?? null;
            $budgetSubtitle = $data['header']['subtitle'] ?? $data['badge'] ?? 'Orçamento Online';
            $budgetDesc = $data['header']['description'] ?? $data['description'] ?? null;
            
            $bgColor = $data['background_color'] ?? '';
            $isPrimaryBg = str_contains($bgColor, 'bg-primary');
            $badgeTextClass = $isPrimaryBg ? 'text-[color-mix(in_srgb,var(--primary),black_85%)]' : 'text-primary';
            $badgeBgClass = $isPrimaryBg ? 'bg-[color-mix(in_srgb,var(--primary),black_85%)]' : 'bg-primary';
            $badgeBorderClass = $isPrimaryBg ? 'border-black/30 bg-black/20' : 'border-primary/20 bg-primary/5';
        @endphp
        <section id="orcamento" class="{{ $data['background_color'] ?? 'bg-muted/50' }} {{ ($data['text_color'] ?? 'light') === 'dark' ? 'text-scheme-dark' : '' }} py-8 lg:py-12">
            <div class="mx-auto max-w-7xl px-4 lg:px-8">
                @if(!empty($budgetTitle) || !empty($budgetSubtitle))
                    <div class="mb-10">
                        @if(!empty($budgetSubtitle))
                            <div class="mb-4 inline-flex items-center gap-2 rounded-full border {{ $badgeBorderClass }} px-4 py-1.5">
                                <span class="h-1.5 w-1.5 rounded-full {{ $badgeBgClass }} animate-pulse"></span>
                                <span class="font-mono text-[10px] font-bold uppercase tracking-[0.2em] {{ $badgeTextClass }}">{{ $budgetSubtitle }}</span>
                            </div>
                        @endif
                        @if(!empty($budgetTitle))
                            <h2 class="font-mono text-3xl font-bold tracking-tight text-foreground md:text-4xl" style="text-wrap: balance;">
                                {{ $budgetTitle }}
                            </h2>
                        @endif
                        @if(!empty($budgetDesc))
                            <p class="mt-3 text-lg leading-relaxed text-muted-foreground">
                                {{ $budgetDesc }}
                            </p>
                        @endif
                    </div>
                @endif
                <livewire:budget :bg-color="$data['background_color'] ?? ''" />
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
            <section class="{{ $bgColor }} {{ $textColor }} py-6 lg:py-10">
                <div class="mx-auto max-w-7xl px-4 lg:px-8">
                    @if($bannerLink)
                        <a href="{{ $bannerLink }}" target="{{ $target }}" class="block overflow-hidden rounded-2xl shadow-xl transition-transform hover:-translate-y-1 hover:shadow-2xl duration-300">
                            <img src="{{ $bannerImg }}" alt="Banner" class="w-full h-auto object-cover" />
                        </a>
                    @else
                        <div class="block overflow-hidden rounded-2xl shadow-xl">
                            <img src="{{ $bannerImg }}" alt="Banner" class="w-full h-auto object-cover" />
                        </div>
                    @endif
                </div>
            </section>
        @endif
        @break

@endswitch
