<div class="w-full max-w-sm">

    {{-- Search Form --}}
    <form>
        <div class="border-b-2py-2 flex items-center">
            <input wire:model='query'
                class="mr-3 w-full appearance-none border-none bg-transparent py-1 px-2 leading-tight text-gray-400 focus:outline-none"
                type="text" placeholder="Digite sua pesquisa">
            <button class="flex-shrink-0 rounded py-1 px-2 text-sm text-white" type="button">
                <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path
                        d="M12.9 14.32a8 8 0 1 1 1.41-1.41l5.35 5.33-1.42 1.42-5.33-5.34zM8 14A6 6 0 1 0 8 2a6 6 0 0 0 0 12z" />
                </svg>
            </button>
        </div>
    </form>

    {{-- Listing Results --}}
    <livewire:dashboard.header.search.listing />

    {{-- End --}}
</div>
