{{-- Prévia de alerta quando o site ainda não tem página publicada --}}
<x-layouts.app title="Pré-visualização de alerta" :alert-preview="$alerts">
    <div class="mx-auto max-w-3xl px-6 py-24 text-center text-muted-foreground">
        <p class="text-sm">Publique uma página para ver o alerta no contexto do site.</p>
    </div>
</x-layouts.app>
