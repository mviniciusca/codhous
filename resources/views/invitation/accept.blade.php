<x-invitation.layout title="Criar acesso">
    <h1>Crie seu acesso</h1>
    <p class="lead">
        {{ $invitation->inviter?->name ? $invitation->inviter->name.' convidou você' : 'Você foi convidado' }}
        para a equipe com o perfil <strong>{{ $invitation->role->name }}</strong>.
    </p>

    <form method="POST" action="{{ route('invitation.accept', ['token' => $token]) }}" novalidate>
        @csrf

        <div class="field">
            <label for="email">E-mail</label>
            <input id="email" type="email" value="{{ $invitation->email }}" readonly aria-describedby="email-hint">
            <span id="email-hint" class="hint">É com este e-mail que você vai entrar.</span>
        </div>

        <div class="field">
            <label for="name">Nome</label>
            <input id="name" name="name" type="text" value="{{ old('name', $invitation->name) }}" required autocomplete="name" autofocus @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
            @error('name') <span id="name-error" class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field">
            <label for="password">Senha</label>
            <input id="password" name="password" type="password" required autocomplete="new-password" minlength="8" aria-describedby="password-hint @error('password') password-error @enderror" @error('password') aria-invalid="true" @enderror>
            <span id="password-hint" class="hint">Pelo menos 8 caracteres.</span>
            @error('password') <span id="password-error" class="error">{{ $message }}</span> @enderror
        </div>

        <div class="field">
            <label for="password_confirmation">Confirme a senha</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">
        </div>

        <button type="submit">Entrar no painel</button>
    </form>

    <p class="foot">Convite válido até {{ $invitation->expires_at->timezone(config('app.display_timezone'))->format('d/m/Y \à\s H:i') }}.</p>
</x-invitation.layout>
