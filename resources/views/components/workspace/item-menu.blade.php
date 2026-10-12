{{-- Menu "…" de um item do workspace: editar e excluir (ações da página com o id nos argumentos) --}}
@props([
    'action',
    'deleteAction',
    'arguments',
    'label',
])

<x-filament::dropdown placement="bottom-end" {{ $attributes->class('ws-menu') }}>
    <x-slot name="trigger">
        <x-filament::icon-button
            icon="heroicon-m-ellipsis-horizontal"
            color="gray"
            size="sm"
            :label="'Ações: '.$label"
        />
    </x-slot>

    <x-filament::dropdown.list>
        <x-filament::dropdown.list.item
            icon="heroicon-m-pencil-square"
            x-on:click="close()"
            wire:click="mountAction('{{ $action }}', {{ \Illuminate\Support\Js::from($arguments) }})"
        >
            Editar
        </x-filament::dropdown.list.item>
        <x-filament::dropdown.list.item
            icon="heroicon-m-trash"
            color="danger"
            x-on:click="close()"
            wire:click="mountAction('{{ $deleteAction }}', {{ \Illuminate\Support\Js::from($arguments) }})"
        >
            Excluir
        </x-filament::dropdown.list.item>
    </x-filament::dropdown.list>
</x-filament::dropdown>
