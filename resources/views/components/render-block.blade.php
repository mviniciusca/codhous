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
            :layout="$data['layout'] ?? 'slider'"
            :subtitle="$data['header']['subtitle'] ?? $data['subtitle'] ?? null"
            :title="$data['header']['title'] ?? $data['title'] ?? null"
            :description="$data['header']['description'] ?? $data['description'] ?? null"
            :items="$data['items'] ?? []"
        />
        @break

    @case('services')
        <x-section-services
            :title="$data['header']['title'] ?? $data['title'] ?? null"
            :badge="$data['header']['subtitle'] ?? $data['badge'] ?? null"
            :description="$data['header']['description'] ?? $data['description'] ?? null"
            :items="$data['items'] ?? []"
            :hide-header="$hideHeader"
        />
        @break

    @case('timeline')
        <x-section-timeline
            :title="$data['header']['title'] ?? $data['title'] ?? null"
            :steps="$data['steps'] ?? []"
        />
        @break

    @case('showcase')
        <section class="bg-background py-8 lg:py-12">
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
            :title="$data['header']['title'] ?? $data['title'] ?? null"
            :items="$data['items'] ?? []"
        />
        @break

    @case('testimonials')
        <x-section-testimonials
            :title="$data['header']['title'] ?? $data['title'] ?? null"
            :items="$data['items'] ?? []"
        />
        @break

    @case('coverage')
        <livewire:section-coverage
            :title="$data['header']['title'] ?? $data['title'] ?? null"
            :cities="$data['cities'] ?? []"
        />
        @break

    @case('differentials')
        <x-section-differentials
            :title="$data['header']['title'] ?? $data['title'] ?? null"
            :items="$data['items'] ?? []"
        />
        @break

    @case('contact_banner')
        <x-section-contact-banner
            :badge="$data['badge'] ?? null"
            :title="$data['title'] ?? null"
            :description="$data['description'] ?? null"
            :whatsapp-enabled="$data['whatsapp_enabled'] ?? true"
            :call-enabled="$data['call_enabled'] ?? true"
            :email-enabled="$data['email_enabled'] ?? true"
        />
        @break

    @case('calculator')
        <livewire:calculator />
        @break

    @case('budget_form')
        <section id="orcamento" class="bg-muted/50 py-8 lg:py-12">
            <div class="mx-auto max-w-7xl px-4 lg:px-8">
                @if(!empty($data['title']))
                    <div class="mb-10">
                        <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/5 px-4 py-1.5">
                            <span class="h-1.5 w-1.5 rounded-full bg-primary animate-pulse"></span>
                            <span class="font-mono text-[10px] font-bold uppercase tracking-[0.2em] text-primary">Orçamento Online</span>
                        </div>
                        <h2 class="font-mono text-3xl font-bold tracking-tight text-foreground md:text-4xl" style="text-wrap: balance;">
                            {{ $data['title'] }}
                        </h2>
                        @if(!empty($data['description']))
                            <p class="mt-3 text-lg leading-relaxed text-muted-foreground">
                                {{ $data['description'] }}
                            </p>
                        @endif
                    </div>
                @endif
                <livewire:budget />
            </div>
        </section>
        @break

    @case('cta_contact')
    @case('contact_form')
        <section class="bg-background py-20 lg:py-28">
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
            :title="$data['header']['title'] ?? $data['title'] ?? null"
            :iframe="$data['iframe_code'] ?? null"
        />
        @break

    @case('rich_text')
        <section class="bg-background py-16">
            <div class="mx-auto max-w-7xl px-4 lg:px-8">
                <div class="prose prose-zinc max-w-3xl">
                    {!! $data['content'] !!}
                </div>
            </div>
        </section>
        @break

    @case('cta')
        <x-section-cta-contact
            :title="$data['title'] ?? null"
            :subtitle="$data['subtitle'] ?? null"
            :button-label="$data['button_label'] ?? null"
            :button-url="$data['button_url'] ?? null"
        />
        @break

    @case('payment_offer')
        <x-section-payment-offer
            :badge="$data['badge'] ?? null"
            :title="$data['title'] ?? null"
            :subtitle="$data['subtitle'] ?? null"
            :button-label="$data['button_label'] ?? null"
            :button-url="$data['button_url'] ?? null"
            :background-image="$data['background_image'] ?? null"
            :payment-methods-image="$data['payment_methods_image'] ?? null"
        />
        @break

@endswitch
