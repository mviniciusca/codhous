{{-- Aba Notas: cartões em grade na largura toda --}}
@php $notes = $this->notes; @endphp

<x-dashboard.panel
    title="Notas rápidas"
    description="Anote ideias, lembretes e informações importantes."
    icon="heroicon-o-document-text"
    :count="$notes->count() ?: null"
>
    <x-slot name="actions">
        <x-filament::button size="sm" icon="heroicon-m-plus" wire:click="mountAction('note')">
            Nova nota
        </x-filament::button>
    </x-slot>

    @if ($notes->isNotEmpty())
        <div class="ws-notes">
            @foreach ($notes as $note)
                <article class="ws-note cx-tone-{{ $note->color->value }}" wire:key="note-{{ $note->id }}">
                    <header class="ws-note-head">
                        <button type="button" class="ws-note-title" wire:click="mountAction('note', { id: {{ $note->id }} })">
                            {{ $note->title }}
                        </button>
                        <x-workspace.item-menu action="note" delete-action="deleteNote" :arguments="['id' => $note->id]" :label="$note->title" />
                    </header>
                    <p class="ws-note-time">
                        <span class="cx-sr">Atualizada</span>
                        {{ \App\Support\WorkspaceDates::moment($note->updated_at, $today) }}
                    </p>
                    @if (filled($note->content))
                        <p class="ws-note-body">{{ $note->content }}</p>
                    @endif
                </article>
            @endforeach

            <button type="button" class="ws-note-new" wire:click="mountAction('note')">
                <span class="ws-note-new-icon" aria-hidden="true">
                    <x-filament::icon icon="heroicon-m-plus" class="cx-icon-sm" />
                </span>
                <strong>Nova nota rápida</strong>
                <span>Registre uma ideia, lembrete ou informação importante.</span>
            </button>
        </div>
    @else
        <x-dashboard.empty icon="heroicon-o-light-bulb" title="Nenhuma nota ainda" description="Guarde ideias, lembretes e informações importantes sem sair do sistema.">
            <x-filament::button size="sm" icon="heroicon-m-plus" wire:click="mountAction('note')">
                Criar primeira nota
            </x-filament::button>
        </x-dashboard.empty>
    @endif
</x-dashboard.panel>
