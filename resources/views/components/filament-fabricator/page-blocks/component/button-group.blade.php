@aware(['page'])
@props(['btn_full_text' => null, 'btn_full_icon' => null, 'btn_full_link' => null,
'btn_full_status' => null,'btn_text'=> null, 'btn_icon' => null, 'btn_link' => null,
'btn_status' => null])

@if($btn_full_status)
<div class="mb-8 flex flex-col space-y-4 sm:flex-row sm:justify-center sm:space-x-4 sm:space-y-0 lg:mb-16">
    <a href="{{ $btn_full_link }}"
        class="inline-flex items-center justify-center rounded-lg bg-secondary-700 px-5 py-3 text-center text-base font-medium text-primary-50 hover:bg-secondary-800 focus:ring-4 focus:ring-secondary-300 dark:focus:ring-secondary-900">
        {!! $btn_full_text !!}
        <x-ionicon :icon="$btn_full_icon" />
    </a>
    @endif

    @if($btn_status)
    <a href="{{ $btn_link }}"
        class="hover:bg-gray-100 inline-flex items-center justify-center rounded-lg border border-primary-500 px-5 py-3 text-center text-base font-medium focus:ring-4 focus:ring-primary-100 dark:border-primary-700 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
        <x-ionicon :icon="$btn_icon" />
        {!! $btn_text !!}
    </a>
    @endif

</div>
