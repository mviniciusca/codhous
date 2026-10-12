{{-- Host do modal “Meu perfil”: abre com o evento open-profile-modal (menu do usuário) --}}
<div x-data x-on:open-profile-modal.window="$wire.mountAction('editProfile')">
    <x-filament-actions::modals />
</div>
