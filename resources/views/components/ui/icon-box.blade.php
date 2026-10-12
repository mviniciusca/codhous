{{-- Ícone Lucide em caixa suave. react: fica sólido no hover do .group pai. size: sm, md, lg. --}}
@props(['icon' => 'circle', 'size' => 'md', 'react' => true])
<span {{ $attributes->class(['icon-box', 'icon-box-sm' => $size === 'sm', 'icon-box-lg' => $size === 'lg', 'icon-box-react' => $react]) }}>
    <i data-lucide="{{ $icon }}"></i>
</span>
