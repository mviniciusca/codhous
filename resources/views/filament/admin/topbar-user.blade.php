{{-- Topbar: quem está conectado (o avatar ao lado abre o menu) --}}
@auth
    <div class="cx-topbar-user" aria-hidden="true">
        <span class="cx-topbar-user-name">{{ auth()->user()->name }}</span>
        <span class="cx-topbar-user-mail">{{ auth()->user()->email }}</span>
    </div>
@endauth
