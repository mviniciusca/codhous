<x-invitation.layout :title="$done ? 'Inscrição cancelada' : 'Cancelar inscrição'">
    @if ($done)
        <h1>Inscrição cancelada</h1>
        <p class="lead">O endereço <strong>{{ $subscriber->email }}</strong> não vai mais receber nossos comunicados por e-mail.</p>
        <a class="btn" href="{{ url('/') }}">Voltar ao site</a>
    @else
        <h1>Cancelar inscrição</h1>
        <p class="lead">Deseja parar de receber os comunicados enviados para <strong>{{ $subscriber->email }}</strong>?</p>
        <form method="POST" action="{{ $action }}">
            <button type="submit">Cancelar inscrição</button>
        </form>
        <p class="foot">Mudou de ideia? É só fechar esta página.</p>
    @endif
</x-invitation.layout>
