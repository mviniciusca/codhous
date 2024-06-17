@props(['menu' => $nav['navigation']])

@if($menu)
<div class="flex items-center lg:order-2">
    <div class="hidden w-full items-center lg:order-1 lg:flex lg:w-auto" id="mobile-menu-2">
        <ul class="mt-4 flex flex-col justify-center text-center font-medium lg:mt-0 lg:flex-row lg:space-x-8">
            @foreach ($menu as $item)
            <li>
                <a target="{{ $item['target'] }}" href="{{ $item['menu_url'] }}"
                    class="rounded-md px-3 py-2 text-sm transition-all duration-100 hover:bg-primary-50 hover:text-secondary-500 dark:hover:bg-primary-900">
                    {{ $item['menu_title'] }}
                </a>
            </li>
            @endforeach
        </ul>
    </div>
    @endif
