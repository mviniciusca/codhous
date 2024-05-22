@aware(['page'])
@props(['title', 'info', 'link', 'status', 'bumper_position' ])

@if($status)
<div class="text-center">
    <div class="mx-auto max-w-7xl">
        <a href="{{ $link }}"
            class="mb-2 inline-flex items-center justify-between rounded-full bg-primary-200 px-1 py-1 pr-4 text-sm hover:opacity-80 dark:bg-primary-950"
            role="alert">
            <span class="mr-3 rounded-full bg-secondary-600 px-4 py-1.5 text-xs text-primary-50">{{ $info }}</span>
            <span class="text-sm font-medium">{{ $title }}</span>
            <svg class="ml-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                    clip-rule="evenodd"></path>
            </svg>
        </a>
    </div>
</div>
@endif
