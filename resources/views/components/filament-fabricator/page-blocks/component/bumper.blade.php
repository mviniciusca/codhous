@aware(['page'])
@props(['title' => null, 'info' => null, 'link'=> null, 'status' => true])

@if($status)
<div class="px-4 py-4 md:py-8">
    <div class="mx-auto max-w-7xl">
        <a href="#"
            class="text-gray-700 dark:bg-purple-100 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-700 mb-7 inline-flex items-center justify-between rounded-full bg-primary-200 px-1 py-1 pr-4 text-sm"
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
