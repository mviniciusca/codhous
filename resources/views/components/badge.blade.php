@props([
'icon' =>
'<svg class="h-10 w-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
    stroke="currentColor" class="h-6 w-6">
    <path stroke-linecap="round" stroke-linejoin="round"
        d="M21.75 17.25v-.228a4.5 4.5 0 0 0-.12-1.03l-2.268-9.64a3.375 3.375 0 0 0-3.285-2.602H7.923a3.375 3.375 0 0 0-3.285 2.602l-2.268 9.64a4.5 4.5 0 0 0-.12 1.03v.228m19.5 0a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3m19.5 0a3 3 0 0 0-3-3H5.25a3 3 0 0 0-3 3m16.5 0h.008v.008h-.008v-.008Zm-3 0h.008v.008h-.008v-.008Z" />
</svg>'])

<span class="flex w-full flex-wrap items-center gap-1">
    <span>{!! $icon !!}</span>

    @if(!$status->maintenance_mode)
    <span
        class="animate-pulse rounded-md dark:bg-white bg-primary-500 px-3 py-1 text-xs leading-normal tracking-normal dark:text-primary-600 text-white">
        {{ __('Live') }}
    </span>
    @else
    <span
        class="animate-pulse rounded-md dark:bg-white bg-primary-500 px-3 py-1 text-xs leading-normal tracking-normal dark:text-primary-600 text-white">
        {{ __('Maintenance') }}
    </span>
    @if($status->discovery_mode)
    <span
        class="animate-pulse rounded-md dark:bg-white bg-primary-500 px-3 py-1 text-xs leading-normal tracking-normal dark:text-primary-600 text-white">
        {{ __('Discovery') }}
    </span>
    @endif
    @endif

</span>