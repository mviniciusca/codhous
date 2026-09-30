<div class="fi-wi-widget py-2">
    <div class="flex items-center gap-x-4 px-4">
        @if(auth()->user()->getFilamentAvatarUrl())
            <div class="flex-shrink-0">
                <img src="{{ auth()->user()->getFilamentAvatarUrl() }}" alt="Avatar" style="width: 64px; height: 64px; object-fit: cover;" class="rounded-full">
            </div>
        @endif
        <div class="flex flex-col gap-y-1">
            <h2 class="text-xl font-semibold tracking-tight text-gray-950 dark:text-white">
                {{ $this->getGreeting() }}, {{ $this->getUserName() }}!
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Bem-vindo de volta ao seu painel de controle. Aqui está o que está acontecendo hoje.
            </p>
        </div>
    </div>
</div>
