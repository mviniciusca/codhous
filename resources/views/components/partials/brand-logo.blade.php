{{-- Logo de parceiro: cinza e discreto, ganha cor no hover do .group --}}
@if(!empty($brand->logo))
    <img src="{{ \Illuminate\Support\Facades\Storage::url($brand->logo) }}" alt="{{ $brand->name }}" loading="lazy"
         class="max-h-12 max-w-full object-contain opacity-60 grayscale transition-all duration-300 group-hover:opacity-100 group-hover:grayscale-0">
@else
    <span class="flex items-center gap-2 text-muted-foreground/70 transition-colors group-hover:text-primary-ink">
        <i data-lucide="{{ $brand->icon ?? 'building-2' }}" class="h-6 w-6 shrink-0"></i>
        <span class="truncate text-xs font-semibold">{{ $brand->name }}</span>
    </span>
@endif
