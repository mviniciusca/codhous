<x-filament-panels::page class="fi-inbox-page">
    @include('filament.mail.styles')

    @php
        $folders = \App\Filament\Resources\MailResource::FOLDERS;
        $counts = $this->counts;
        $mails = $this->mails;
        $hasMore = $mails->count() > $this->limit;
        $mails = $mails->take($this->limit);
        $current = $this->current;
        // Contadores: "Entrada" mostra não lidas; "Enviadas" não mostra
        $badge = fn (string $folder) => match ($folder) {
            'inbox' => $counts['unread'],
            'sent' => 0,
            default => $counts[$folder],
        };
    @endphp

    <div @class(['ix', 'cx-ui', 'has-open' => $current]) wire:poll.visible.60s>
        {{-- Pastas --}}
        <div class="ix-bar">
            <nav class="ix-tabs" aria-label="Pastas">
                @foreach ($folders as $key => $folder)
                    <button
                        type="button"
                        wire:click="$set('folder', '{{ $key }}')"
                        @class(['ix-tab', 'is-active' => $this->folder === $key])
                        @if ($this->folder === $key) aria-current="page" @endif
                    >
                        <x-filament::icon :icon="$folder['icon']" class="cx-icon-sm" aria-hidden="true" />
                        {{ $folder['label'] }}
                        @if ($badge($key) > 0)
                            <span @class(['ix-tab-count', 'is-strong' => in_array($key, ['inbox', 'unread'], true)])>{{ $badge($key) }}</span>
                        @endif
                    </button>
                @endforeach
            </nav>
        </div>

        <div class="ix-shell">
            {{-- Lista --}}
            <section class="ix-list" aria-label="Mensagens · {{ $folders[$this->folder]['label'] }}">
                <div class="ix-search">
                    <x-filament::input.wrapper prefix-icon="heroicon-m-magnifying-glass">
                        <x-filament::input
                            type="search"
                            wire:model.live.debounce.400ms="search"
                            placeholder="Buscar mensagens"
                            aria-label="Buscar mensagens"
                        />
                    </x-filament::input.wrapper>
                </div>

                <ul class="ix-items" role="list" wire:loading.class="is-loading" wire:target="folder,search">
                    @forelse ($mails as $mail)
                        @php $unread = ! $mail->is_sent && ! $mail->is_read && $this->folder !== 'trash'; @endphp
                        <li
                            @class(['ix-item', 'is-unread' => $unread, 'is-active' => $current?->id === $mail->id])
                            wire:key="mail-{{ $mail->id }}"
                        >
                            <button type="button" class="ix-item-hit" wire:click="open({{ $mail->id }})" @if ($current?->id === $mail->id) aria-current="true" @endif>
                                <span class="cx-sr">{{ $unread ? 'Não lida: ' : '' }}{{ $mail->name }}, {{ $mail->subject }}</span>
                            </button>

                            <span class="ix-unread-dot" aria-hidden="true"></span>
                            <span class="ix-avatar ix-avatar-{{ $this->avatarTone($mail->email) }}" aria-hidden="true">
                                @if ($mail->is_sent)
                                    <x-filament::icon icon="heroicon-m-paper-airplane" class="cx-icon-sm" />
                                @else
                                    {{ $this->initials($mail->name) }}
                                @endif
                            </span>

                            <span class="ix-item-body" aria-hidden="true">
                                <span class="ix-item-top">
                                    <span class="ix-item-name">{{ $mail->is_sent ? 'Para: ' : '' }}{{ $mail->name }}</span>
                                    <time class="ix-item-date" datetime="{{ $mail->created_at?->toIso8601String() }}">{{ $this->shortDate($mail->created_at) }}</time>
                                </span>
                                <span class="ix-item-subject">{{ $mail->subject }}</span>
                                <span class="ix-item-preview">{{ $this->preview($mail) }}</span>
                            </span>

                            <button
                                type="button"
                                wire:click="toggleFavorite({{ $mail->id }})"
                                @class(['ix-star', 'is-on' => $mail->is_favorite])
                                aria-label="{{ $mail->is_favorite ? 'Remover de importantes' : 'Marcar como importante' }}"
                                aria-pressed="{{ $mail->is_favorite ? 'true' : 'false' }}"
                            >
                                <x-filament::icon :icon="$mail->is_favorite ? 'heroicon-s-star' : 'heroicon-o-star'" class="cx-icon-sm" />
                            </button>
                        </li>
                    @empty
                        <li>
                            @if (filled($this->search))
                                <x-dashboard.empty icon="heroicon-o-magnifying-glass" title="Nada encontrado" description="Nenhuma mensagem em {{ Str::lower($folders[$this->folder]['label']) }} corresponde a “{{ $this->search }}”." />
                            @else
                                <x-dashboard.empty
                                    :icon="$this->folder === 'unread' ? 'heroicon-o-check-circle' : 'heroicon-o-inbox'"
                                    :tone="$this->folder === 'unread' ? 'success' : 'gray'"
                                    :title="match ($this->folder) {
                                        'unread' => 'Tudo lido',
                                        'starred' => 'Nenhuma mensagem importante',
                                        'sent' => 'Nada enviado ainda',
                                        'spam' => 'Sem spam',
                                        'trash' => 'Lixeira vazia',
                                        default => 'Caixa vazia',
                                    }"
                                    :description="match ($this->folder) {
                                        'starred' => 'Use a estrela para separar o que precisa de acompanhamento.',
                                        'sent' => 'Respostas e mensagens novas enviadas daqui aparecem nesta pasta.',
                                        'inbox' => 'Mensagens do formulário de contato do site aparecem aqui.',
                                        default => null,
                                    }"
                                />
                            @endif
                        </li>
                    @endforelse
                </ul>

                @if ($hasMore)
                    <div class="ix-more">
                        <x-filament::button color="gray" size="sm" wire:click="loadMore" wire:target="loadMore">Carregar mais</x-filament::button>
                    </div>
                @endif
            </section>

            {{-- Leitura --}}
            <section class="ix-reader" aria-label="Mensagem" wire:loading.class="is-loading" wire:target="open">
                @if ($current)
                    @php
                        $contact = $this->contact;
                        $history = $this->history;
                        $whatsapp = ! $current->is_sent ? $this->whatsappUrl($current) : null;
                        $canReply = ! $current->is_sent && ! $current->is_spam && ! $current->trashed();
                    @endphp

                    <div class="ix-toolbar" wire:key="toolbar-{{ $current->id }}">
                        <button type="button" class="ix-tool ix-back" wire:click="close" aria-label="Voltar para a lista">
                            <x-filament::icon icon="heroicon-m-arrow-left" class="cx-icon-sm" />
                        </button>

                        @if ($current->trashed())
                            <button type="button" class="ix-tool" wire:click="restore({{ $current->id }})">
                                <x-filament::icon icon="heroicon-m-arrow-uturn-left" class="cx-icon-sm" /> Restaurar
                            </button>
                            <button type="button" class="ix-tool is-danger" wire:click="mountAction('forceDelete', { mail: {{ $current->id }} })">
                                <x-filament::icon icon="heroicon-m-trash" class="cx-icon-sm" /> Excluir definitivamente
                            </button>
                        @else
                            <button type="button" @class(['ix-tool', 'is-on' => $current->is_favorite]) wire:click="toggleFavorite({{ $current->id }})" aria-pressed="{{ $current->is_favorite ? 'true' : 'false' }}">
                                <x-filament::icon :icon="$current->is_favorite ? 'heroicon-s-star' : 'heroicon-o-star'" class="cx-icon-sm" />
                                <span class="ix-tool-label">Importante</span>
                            </button>
                            @unless ($current->is_sent)
                                <button type="button" class="ix-tool" wire:click="markUnread({{ $current->id }})">
                                    <x-filament::icon icon="heroicon-o-envelope" class="cx-icon-sm" />
                                    <span class="ix-tool-label">Marcar como não lida</span>
                                </button>
                                <button type="button" class="ix-tool" wire:click="markSpam({{ $current->id }}, {{ $current->is_spam ? 'false' : 'true' }})">
                                    <x-filament::icon :icon="$current->is_spam ? 'heroicon-o-inbox-arrow-down' : 'heroicon-o-shield-exclamation'" class="cx-icon-sm" />
                                    <span class="ix-tool-label">{{ $current->is_spam ? 'Não é spam' : 'Spam' }}</span>
                                </button>
                            @endunless
                            <button type="button" class="ix-tool is-danger" wire:click="mountAction('delete', { mail: {{ $current->id }} })" aria-label="Mover para a lixeira">
                                <x-filament::icon icon="heroicon-o-trash" class="cx-icon-sm" />
                            </button>
                        @endif
                    </div>

                    <div class="ix-scroll" wire:key="reader-{{ $current->id }}">
                        <article class="ix-message">
                            <header class="ix-head">
                                <h2 class="ix-subject">{{ $current->subject }}</h2>
                                <div class="ix-from">
                                    <span class="ix-avatar ix-avatar-lg ix-avatar-{{ $this->avatarTone($current->email) }}" aria-hidden="true">
                                        @if ($current->is_sent)
                                            <x-filament::icon icon="heroicon-m-paper-airplane" class="cx-icon" />
                                        @else
                                            {{ $this->initials($current->name) }}
                                        @endif
                                    </span>
                                    <div class="ix-from-text">
                                        <strong>{{ $current->is_sent ? 'Para: ' : '' }}{{ $current->name }}</strong>
                                        <a href="mailto:{{ $current->email }}">{{ $current->email }}</a>
                                        <span class="ix-from-meta">
                                            {{ $current->is_sent ? 'Enviada pelo painel' : 'Recebida pelo formulário do site' }}
                                            @if ($current->is_spam) · <span class="ix-flag">Spam</span> @endif
                                            @if ($current->trashed()) · <span class="ix-flag">Na lixeira</span> @endif
                                        </span>
                                    </div>
                                    <time class="ix-when" datetime="{{ $current->created_at?->toIso8601String() }}">{{ $this->longDate($current->created_at) }}</time>
                                </div>
                            </header>

                            <div class="ix-body">{{ $this->body($current) }}</div>
                        </article>

                        {{-- Contato --}}
                        <aside class="ix-aside" aria-label="Contato">
                            <div class="ix-card">
                                <h3 class="ix-card-title">Contato</h3>
                                <ul class="ix-contact" role="list">
                                    <li>
                                        <x-filament::icon icon="heroicon-o-envelope" class="cx-icon-sm" aria-hidden="true" />
                                        <a href="mailto:{{ $current->email }}">{{ $current->email }}</a>
                                    </li>
                                    @if ($current->phone)
                                        <li>
                                            <x-filament::icon icon="heroicon-o-phone" class="cx-icon-sm" aria-hidden="true" />
                                            <a href="tel:{{ preg_replace('/\D/', '', $current->phone) }}">{{ $current->phone }}</a>
                                        </li>
                                    @endif
                                    <li>
                                        <x-filament::icon icon="heroicon-o-user" class="cx-icon-sm" aria-hidden="true" />
                                        @if ($contact['customer'])
                                            <a href="{{ $contact['customer']['url'] }}">Cliente cadastrado</a>
                                        @else
                                            <span class="ix-muted">Não é cliente cadastrado</span>
                                        @endif
                                    </li>
                                </ul>
                                @if ($whatsapp)
                                    <a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="ix-whatsapp">
                                        <x-filament::icon icon="heroicon-o-chat-bubble-left-right" class="cx-icon-sm" aria-hidden="true" />
                                        Chamar no WhatsApp
                                    </a>
                                @endif
                            </div>

                            @if ($contact['budgets']->isNotEmpty())
                                <div class="ix-card">
                                    <h3 class="ix-card-title">Orçamentos deste e-mail</h3>
                                    <ul class="ix-links" role="list">
                                        @foreach ($contact['budgets'] as $budget)
                                            <li>
                                                <a href="{{ $budget['url'] }}">
                                                    <span class="ix-link-main">{{ $budget['code'] }}</span>
                                                    <span class="ix-link-sub">{{ $budget['total'] }}</span>
                                                    <x-dashboard.status :status="$budget['status']" />
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if ($history->isNotEmpty())
                                <div class="ix-card">
                                    <h3 class="ix-card-title">Outras mensagens</h3>
                                    <ul class="ix-links" role="list">
                                        @foreach ($history as $item)
                                            <li>
                                                <button type="button" wire:click="open({{ $item->id }})">
                                                    <x-filament::icon :icon="$item->is_sent ? 'heroicon-m-arrow-up-right' : 'heroicon-m-arrow-down-left'" @class(['cx-icon-sm', 'ix-dir', 'is-out' => $item->is_sent]) />
                                                    <span class="cx-sr">{{ $item->is_sent ? 'Enviada:' : 'Recebida:' }}</span>
                                                    <span class="ix-link-main">{{ $item->subject }}</span>
                                                    <span class="ix-link-sub">{{ $this->shortDate($item->created_at) }}</span>
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </aside>

                        {{-- Resposta --}}
                        @if ($canReply)
                            <form wire:submit="sendReply" class="ix-composer">
                                <div class="ix-composer-head">
                                    <x-filament::icon icon="heroicon-m-arrow-uturn-left" class="cx-icon-sm" aria-hidden="true" />
                                    Responder para <strong>{{ $current->name }}</strong>
                                    <span class="ix-muted">&lt;{{ $current->email }}&gt;</span>
                                </div>

                                {{ $this->replyForm }}

                                <div class="ix-composer-foot">
                                    @if ($whatsapp)
                                        <a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="cx-link">
                                            <x-filament::icon icon="heroicon-o-chat-bubble-left-right" class="cx-icon-sm" />
                                            Responder no WhatsApp
                                        </a>
                                    @endif
                                    <x-filament::button type="submit" icon="heroicon-m-paper-airplane" wire:target="sendReply">
                                        Enviar resposta
                                    </x-filament::button>
                                </div>
                            </form>
                        @endif
                    </div>
                @else
                    <x-dashboard.empty
                        class="ix-placeholder"
                        icon="heroicon-o-envelope-open"
                        title="Selecione uma mensagem"
                        description="{{ $counts['unread'] > 0 ? $counts['unread'].' '.($counts['unread'] === 1 ? 'mensagem não lida' : 'mensagens não lidas').' na entrada.' : 'Nenhuma mensagem não lida na entrada.' }}"
                    />
                @endif
            </section>
        </div>
    </div>
</x-filament-panels::page>
