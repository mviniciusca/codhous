@props([
    'items' => []
])

<nav class="flex px-4 py-3 text-zinc-500 bg-zinc-50/50 rounded-lg border border-zinc-200/50 backdrop-blur-sm" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="/" class="inline-flex items-center text-sm font-medium hover:text-primary transition-colors">
                <i data-lucide="home" class="w-4 h-4 mr-2"></i>
                Home
            </a>
        </li>
        @foreach($items as $item)
            <li>
                <div class="flex items-center">
                    <i data-lucide="chevron-right" class="w-4 h-4 text-zinc-400"></i>
                    @if(isset($item['url']))
                        <a href="{{ $item['url'] }}" class="ml-1 text-sm font-medium hover:text-primary md:ml-2 transition-colors">{{ $item['label'] }}</a>
                    @else
                        <span class="ml-1 text-sm font-semibold text-zinc-950 md:ml-2">{{ $item['label'] }}</span>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>
</nav>