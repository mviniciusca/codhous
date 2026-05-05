<x-layouts.app :title="$meta['title']">
    @foreach($page->content ?? [] as $block)
        @switch($block['type'])
            @case('hero')
                <livewire:section-hero-cep 
                    :main-slide="$block['data']['slides'][0] ?? []" 
                    :badge="$block['data']['badge'] ?? ''"
                    :layout="$block['data']['layout'] ?? 'default'"
                    :stats="$block['data']['stats'] ?? []"
                />
                @break
            
            @case('partners')
                <x-section-partners 
                    :title="$block['data']['title'] ?? null"
                    :description="$block['data']['description'] ?? null"
                    :items="$block['data']['items'] ?? []"
                />
                @break

            @case('services')
                <x-section-services 
                    :title="$block['data']['title'] ?? null"
                    :description="$block['data']['description'] ?? null"
                    :items="$block['data']['items'] ?? []"
                />
                @break

            @case('timeline')
                <x-section-timeline 
                    :title="$block['data']['title'] ?? null"
                    :steps="$block['data']['steps'] ?? []"
                />
                @break

            @case('showcase')
                <div class="py-12">
                    <div class="container mx-auto px-4 mb-8">
                        <h2 class="text-3xl font-bold text-zinc-950">{{ $block['data']['title'] ?? 'Nossas Obras' }}</h2>
                    </div>
                    <livewire:showcase-feed :limit="$block['data']['limit'] ?? 4" />
                </div>
                @break

            @case('faq')
                <x-section-faq 
                    :title="$block['data']['title'] ?? null"
                    :items="$block['data']['items'] ?? []"
                />
                @break

            @case('testimonials')
                <x-section-testimonials 
                    :title="$block['data']['title'] ?? null"
                    :items="$block['data']['items'] ?? []"
                />
                @break

            @case('coverage')
                <x-section-coverage 
                    :title="$block['data']['title'] ?? null"
                    :cities="$block['data']['cities'] ?? []"
                />
                @break

            @case('cta')
                <x-section-cta-contact 
                    :title="$block['data']['title'] ?? null"
                    :subtitle="$block['data']['subtitle'] ?? null"
                    :button-label="$block['data']['button_label'] ?? null"
                    :button-url="$block['data']['button_url'] ?? null"
                />
                @break

            @case('rich_text')
                <div class="py-12 prose prose-zinc max-w-none container mx-auto px-4">
                    {!! $block['data']['content'] !!}
                </div>
                @break
        @endswitch
    @endforeach
</x-layouts.app>
