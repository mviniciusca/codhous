{{-- Avatar da listagem: mesmo componente do menu do usuário (foto ou iniciais) --}}
<div class="px-3 py-2">
    <x-filament-panels::avatar.user :user="$getRecord()" size="md" />
</div>
