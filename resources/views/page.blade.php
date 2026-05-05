<x-layouts.app :title="$meta['title']">
    @foreach($page->content ?? [] as $block)
        @switch($block['type'])

            {{-- ─────────────────────────────────────────────────────────────
                 CABEÇALHO DA PÁGINA — usa o componente page-header fiel ao
                 padrão visual da homepage (section-services)
            ───────────────────────────────────────────────────────────────── --}}
            @case('page_header')
                <x-page-header
                    :badge="$block['data']['badge'] ?? null"
                    :title="$block['data']['title'] ?? null"
                    :description="$block['data']['description'] ?? null"
                />
                @break

            {{-- ──── HERO (só na homepage, mas disponível caso necessário) ─ --}}
            @case('hero')
                <livewire:section-hero-cep
                    :main-slide="$block['data']['slides'][0] ?? []"
                    :badge="$block['data']['badge'] ?? ''"
                    :layout="$block['data']['layout'] ?? 'default'"
                    :stats="$block['data']['stats'] ?? []"
                />
                @break

            {{-- ──── PARCEIROS ─────────────────────────────────────────────── --}}
            @case('partners')
                <x-section-partners
                    :title="$block['data']['title'] ?? null"
                    :description="$block['data']['description'] ?? null"
                    :items="$block['data']['items'] ?? []"
                />
                @break

            {{-- ──── SERVIÇOS ──────────────────────────────────────────────── --}}
            @case('services')
                <x-section-services
                    :title="$block['data']['title'] ?? null"
                    :description="$block['data']['description'] ?? null"
                    :items="$block['data']['items'] ?? []"
                />
                @break

            {{-- ──── LINHA DO TEMPO ────────────────────────────────────────── --}}
            @case('timeline')
                <x-section-timeline
                    :title="$block['data']['title'] ?? null"
                    :steps="$block['data']['steps'] ?? []"
                />
                @break

            {{-- ──── SHOWCASE / GALERIA ────────────────────────────────────── --}}
            @case('showcase')
                <section class="bg-background py-20 lg:py-28">
                    <div class="mx-auto max-w-7xl px-4 lg:px-8">
                        @if(!empty($block['data']['title']))
                            <div class="mb-16 max-w-2xl">
                                <h2 class="font-mono text-3xl font-bold tracking-tight text-foreground md:text-4xl" style="text-wrap: balance;">
                                    {{ $block['data']['title'] }}
                                </h2>
                            </div>
                        @endif
                        <livewire:showcase-feed :limit="$block['data']['limit'] ?? 4" />
                    </div>
                </section>
                @break

            {{-- ──── FAQ ───────────────────────────────────────────────────── --}}
            @case('faq')
                <x-section-faq
                    :title="$block['data']['title'] ?? null"
                    :items="$block['data']['items'] ?? []"
                />
                @break

            {{-- ──── DEPOIMENTOS ───────────────────────────────────────────── --}}
            @case('testimonials')
                <x-section-testimonials
                    :title="$block['data']['title'] ?? null"
                    :items="$block['data']['items'] ?? []"
                />
                @break

            {{-- ──── ÁREA DE COBERTURA ─────────────────────────────────────── --}}
            @case('coverage')
                <x-section-coverage
                    :title="$block['data']['title'] ?? null"
                    :cities="$block['data']['cities'] ?? []"
                />
                @break

            {{-- ──── FORMULÁRIO DE CONTATO ─────────────────────────────────── --}}
            @case('contact_form')
                <section class="bg-background py-20 lg:py-28">
                    <div class="mx-auto max-w-7xl px-4 lg:px-8">
                        <div class="grid lg:grid-cols-2 gap-16 items-start">

                            {{-- Informações de contato --}}
                            <div>
                                @if(!empty($block['data']['title']))
                                    <h2 class="font-mono text-3xl font-bold tracking-tight text-foreground md:text-4xl mb-4" style="text-wrap: balance;">
                                        {{ $block['data']['title'] }}
                                    </h2>
                                @endif
                                @if(!empty($block['data']['description']))
                                    <p class="text-lg leading-relaxed text-muted-foreground mb-10">
                                        {{ $block['data']['description'] }}
                                    </p>
                                @endif

                                @php
                                    $company = \App\Models\Setting::get('company', []);
                                    $addr    = data_get($company, 'address', []);
                                    $addrStr = is_array($addr)
                                        ? implode(', ', array_filter([
                                            data_get($addr, 'street') . (data_get($addr, 'number') ? ', ' . data_get($addr, 'number') : ''),
                                            data_get($addr, 'neighborhood'),
                                            data_get($addr, 'city') . (data_get($addr, 'state') ? ' - ' . data_get($addr, 'state') : ''),
                                          ]))
                                        : (string) $addr;
                                @endphp

                                <div class="space-y-5">
                                    @if(!empty($block['data']['email_to']))
                                        <div class="flex items-center gap-4">
                                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                                <i data-lucide="mail" class="h-5 w-5"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-muted-foreground uppercase tracking-widest font-semibold mb-0.5">E-mail</p>
                                                <p class="font-medium text-foreground">{{ $block['data']['email_to'] }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    @php $phone = is_string(data_get($company, 'phone')) ? data_get($company, 'phone') : ''; @endphp
                                    @if($phone)
                                        <div class="flex items-center gap-4">
                                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                                <i data-lucide="phone" class="h-5 w-5"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-muted-foreground uppercase tracking-widest font-semibold mb-0.5">Telefone</p>
                                                <p class="font-medium text-foreground">{{ $phone }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    @if($addrStr)
                                        <div class="flex items-center gap-4">
                                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                                <i data-lucide="map-pin" class="h-5 w-5"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-muted-foreground uppercase tracking-widest font-semibold mb-0.5">Endereço</p>
                                                <p class="font-medium text-foreground">{{ $addrStr }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Formulário --}}
                            <div class="rounded-2xl border border-border bg-card p-8 shadow-sm">
                                <form action="#" class="space-y-5">
                                    <div class="grid md:grid-cols-2 gap-5">
                                        <div>
                                            <label class="mb-2 block text-sm font-medium text-foreground">Nome</label>
                                            <input type="text" placeholder="Seu nome" class="w-full rounded-lg border border-border bg-background px-4 py-3 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all">
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-sm font-medium text-foreground">E-mail</label>
                                            <input type="email" placeholder="seu@email.com" class="w-full rounded-lg border border-border bg-background px-4 py-3 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-medium text-foreground">Assunto</label>
                                        <input type="text" placeholder="Assunto da mensagem" class="w-full rounded-lg border border-border bg-background px-4 py-3 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all">
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-medium text-foreground">Mensagem</label>
                                        <textarea rows="5" placeholder="Descreva como podemos ajudar..." class="w-full rounded-lg border border-border bg-background px-4 py-3 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all resize-none"></textarea>
                                    </div>
                                    <button type="submit" class="w-full rounded-lg bg-primary px-6 py-3.5 text-sm font-semibold text-primary-foreground transition-colors hover:bg-primary/90">
                                        Enviar Mensagem
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                </section>
                @break

            {{-- ──── MAPA ──────────────────────────────────────────────────── --}}
            @case('map')
                <section class="bg-muted py-16">
                    <div class="mx-auto max-w-7xl px-4 lg:px-8">
                        @if(!empty($block['data']['title']))
                            <div class="mb-8 max-w-2xl">
                                <h2 class="font-mono text-3xl font-bold tracking-tight text-foreground md:text-4xl" style="text-wrap: balance;">
                                    {{ $block['data']['title'] }}
                                </h2>
                            </div>
                        @endif
                        <div class="overflow-hidden rounded-2xl border border-border shadow-sm aspect-video w-full grayscale hover:grayscale-0 transition-all duration-700">
                            {!! $block['data']['iframe_code'] !!}
                        </div>
                    </div>
                </section>
                @break

            {{-- ──── CTA ───────────────────────────────────────────────────── --}}
            @case('cta')
                <x-section-cta-contact
                    :title="$block['data']['title'] ?? null"
                    :subtitle="$block['data']['subtitle'] ?? null"
                    :button-label="$block['data']['button_label'] ?? null"
                    :button-url="$block['data']['button_url'] ?? null"
                />
                @break

            {{-- ──── TEXTO RICO ────────────────────────────────────────────── --}}
            @case('rich_text')
                <section class="bg-background py-16">
                    <div class="mx-auto max-w-7xl px-4 lg:px-8">
                        <div class="prose prose-zinc max-w-3xl">
                            {!! $block['data']['content'] !!}
                        </div>
                    </div>
                </section>
                @break

        @endswitch
    @endforeach
</x-layouts.app>
