{{--
    Menu do usuário (sobrescreve filament-panels::components.user-menu).
    Perfil abre em modal (App\Livewire\ProfileModal); tema usa o mesmo evento do Filament.
--}}
@php
    $user = filament()->auth()->user();
    $role = $user?->role()?->name;
    $canTeam = $user?->canAccessArea(\App\Enums\AccessArea::Equipe);
    $canSettings = $user?->canAccessArea(\App\Enums\AccessArea::Configuracoes);
@endphp

<x-filament::dropdown
    placement="bottom-end"
    teleport
    width="xs"
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->class(['fi-user-menu', 'cx-um'])"
>
    <x-slot name="trigger">
        <button type="button" class="cx-um-trigger" aria-label="{{ __('filament-panels::layout.actions.open_user_menu.label') }}">
            <x-filament-panels::avatar.user :user="$user" size="md" />
            <span class="cx-um-trigger-text" aria-hidden="true">
                <span class="cx-um-trigger-name">{{ filament()->getUserName($user) }}</span>
                <span class="cx-um-trigger-role">{{ $role ?? $user->email }}</span>
            </span>
            <x-filament::icon icon="heroicon-m-chevron-down" class="cx-um-chevron" aria-hidden="true" />
        </button>
    </x-slot>

    <div class="cx-um-head">
        <x-filament-panels::avatar.user :user="$user" size="lg" />
        <div class="cx-um-id">
            <strong>{{ filament()->getUserName($user) }}</strong>
            <span>{{ $user->email }}</span>
            @if ($role)
                <span class="cx-um-role">{{ $role }}</span>
            @endif
        </div>
    </div>

    <x-filament::dropdown.list>
        <x-filament::dropdown.list.item
            icon="heroicon-m-user-circle"
            x-on:click="close(); $dispatch('open-profile-modal')"
        >
            Meu perfil
        </x-filament::dropdown.list.item>
        @if ($canTeam)
            <x-filament::dropdown.list.item icon="heroicon-m-user-group" tag="a" :href="\App\Filament\Resources\UserResource::getUrl('index')">
                Equipe e acessos
            </x-filament::dropdown.list.item>
        @endif
        @if ($canSettings)
            <x-filament::dropdown.list.item icon="heroicon-m-cog-6-tooth" tag="a" :href="\App\Filament\Resources\SettingResource::getUrl('index')">
                Configurações do sistema
            </x-filament::dropdown.list.item>
        @endif
    </x-filament::dropdown.list>

    @if (filament()->hasDarkMode() && (! filament()->hasDarkModeForced()))
        <div class="cx-um-section">
            <span class="cx-um-label" id="cx-um-theme">Aparência</span>
            @include('filament.admin.theme-options', ['labelledBy' => 'cx-um-theme'])
        </div>
    @endif

    <x-filament::dropdown.list>
        <x-filament::dropdown.list.item
            icon="heroicon-m-arrow-left-start-on-rectangle"
            :action="filament()->getLogoutUrl()"
            method="post"
            tag="form"
        >
            Sair
        </x-filament::dropdown.list.item>
    </x-filament::dropdown.list>

    <div class="cx-um-version">Codhous v{{ config('app.version') }}</div>
</x-filament::dropdown>
