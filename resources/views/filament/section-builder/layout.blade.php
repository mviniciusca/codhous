{{-- Construtor de seções: biblioteca | live preview | inspetor. Prefixo sb- --}}
@include('filament.section-builder.styles')

<div class="sb-host">
    <div {{ $attributes->merge($getExtraAttributes(), escape: false)->class(['sb cx-ui']) }}>
        @foreach ($getChildComponentContainer()->getComponents() as $child)
            @php $column = \App\Filament\Forms\Components\SectionBuilder::getColumnFor($child); @endphp

            <div class="sb-col sb-col--{{ $column }}" wire:key="section-builder.{{ $column }}">
                {{ $child }}
            </div>
        @endforeach
    </div>
</div>
