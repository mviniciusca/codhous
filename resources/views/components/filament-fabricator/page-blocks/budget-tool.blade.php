@aware(['page'])

@php
    $title = $title ?? __('Request Your Budget');
    $subtitle = $subtitle ?? __('Fill in the form below and our team will get back to you within 24 hours');
@endphp

<section id="budget-tool" class="px-4 py-8 md:py-16">
    <div class="max-w-4xl mx-auto">
        {{-- Section Header --}}
        <div class="text-center mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">{{ $title }}</h2>
            <p class="text-sm md:text-base text-slate-500">{{ $subtitle }}</p>
        </div>

        {{-- Budget Form --}}
        @livewire('budget')
    </div>
</section>
