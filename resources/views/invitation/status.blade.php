<x-invitation.layout :title="$state === 'signed-in' ? 'Você já está conectado' : 'Convite indisponível'">
    @if ($state === 'signed-in')
        <h1>Você já está conectado</h1>
        <p class="lead">Este convite cria uma conta nova. Saia da conta atual para aceitá-lo, ou continue no painel.</p>
        <a class="btn" href="{{ url('/admin') }}">Ir para o painel</a>
    @else
        <h1>Convite indisponível</h1>
        <p class="lead">O link expirou, já foi usado ou foi revogado. Peça a um administrador para enviar um novo convite.</p>
    @endif
</x-invitation.layout>
