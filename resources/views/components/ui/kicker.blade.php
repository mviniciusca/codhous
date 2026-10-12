{{-- Subtítulo curto acima de títulos. variant: pill (com fundo) ou plain. --}}
@props(['variant' => 'pill', 'dot' => true])
<span {{ $attributes->class(['kicker', 'kicker-pill' => $variant === 'pill']) }}>
    @if($dot)<span class="kicker-dot"></span>@endif
    {{ $slot }}
</span>
