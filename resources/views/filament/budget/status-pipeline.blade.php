@php
    /** @var \App\Models\Budget $record */
    $tz = config('app.display_timezone');
    $content = $record->content ?? [];
    $status = $record->status;
    $isArchived = $status === 'ignored';

    $steps = [
        'pending' => ['label' => 'Novo pedido', 'hint' => 'Recebido em ' . $record->created_at?->timezone($tz)->format('d/m H:i')],
        'on going' => ['label' => 'Em negociação', 'hint' => 'Proposta enviada'],
        'done' => ['label' => 'Fechado', 'hint' => 'Cliente confirmou'],
    ];
    $order = array_keys($steps);
    $currentIndex = $isArchived ? -1 : array_search($status, $order, true);

    $deliveryDate = ! empty($content['delivery_date']) ? \Illuminate\Support\Carbon::parse($content['delivery_date'])->format('d/m/Y') : null;
    $deliveryPeriod = \App\Livewire\Budget::DELIVERY_PERIODS[$content['delivery_period'] ?? ''] ?? null;

    $nextStep = match ($status) {
        'pending' => 'Revise itens e valores e envie a proposta pelo WhatsApp ou e-mail.',
        'on going' => 'Aguardando retorno do cliente. Quando ele confirmar, marque como Fechado.',
        'done' => $deliveryDate
            ? 'Pedido fechado. Programe a entrega para ' . $deliveryDate . ($deliveryPeriod ? ' (' . mb_strtolower($deliveryPeriod) . ')' : '') . '.'
            : 'Pedido fechado. Combine a data de entrega com o cliente.',
        'ignored' => 'Orçamento arquivado. Reabra para retomar a negociação.',
        default => '',
    };

    $pdfViews = (int) $record->pdfs()->sum('download_count');
@endphp

<div class="bq-pipeline">
    <div class="bq-steps" role="list" aria-label="Etapa do orçamento">
        @foreach ($steps as $value => $step)
            @php
                $index = $loop->index;
                $state = match (true) {
                    $isArchived => 'idle',
                    $index < $currentIndex => 'done',
                    $index === $currentIndex => 'current',
                    default => 'idle',
                };
            @endphp

            <button
                type="button"
                role="listitem"
                class="bq-step bq-step--{{ $state }}"
                wire:click="setStatus(@js($value))"
                wire:loading.attr="disabled"
                @disabled($value === $status)
                @if ($state === 'current') aria-current="step" @endif
                title="{{ $value === $status ? 'Etapa atual' : 'Mover para ' . $step['label'] }}"
            >
                <span class="bq-step-dot">
                    @if ($state === 'done')
                        <x-filament::icon icon="heroicon-m-check" style="width: 16px; height: 16px;" />
                    @else
                        {{ $index + 1 }}
                    @endif
                </span>
                <span class="bq-step-text">
                    <strong>{{ $step['label'] }}</strong>
                    <small>{{ $step['hint'] }}</small>
                </span>
            </button>

            @unless ($loop->last)
                <span class="bq-step-line {{ ! $isArchived && $index < $currentIndex ? 'bq-step-line--done' : '' }}" aria-hidden="true"></span>
            @endunless
        @endforeach
    </div>

    <div class="bq-next">
        <span class="bq-next-label">{{ $isArchived ? 'Arquivado' : 'Próximo passo' }}</span>
        <p>{{ $nextStep }}</p>
    </div>

    <div class="bq-chips">
        <span class="bq-chip">
            <x-filament::icon :icon="$record->user_id ? 'heroicon-m-user' : 'heroicon-m-globe-alt'" />
            {{ $record->user_id ? 'Criado por ' . ($record->user?->name ?? 'equipe') : 'Pedido do site' }}
        </span>
        <span @class(['bq-chip', 'bq-chip--ok' => $record->notified_via_whatsapp])>
            <x-filament::icon :icon="$record->notified_via_whatsapp ? 'heroicon-m-check-circle' : 'heroicon-m-chat-bubble-left-right'" />
            WhatsApp {{ $record->notified_via_whatsapp ? 'enviado' : 'não enviado' }}
        </span>
        <span @class(['bq-chip', 'bq-chip--ok' => $record->notified_via_email])>
            <x-filament::icon :icon="$record->notified_via_email ? 'heroicon-m-check-circle' : 'heroicon-m-envelope'" />
            E-mail {{ $record->notified_via_email ? 'enviado' : 'não enviado' }}
        </span>
        @if ($record->pdfs()->exists())
            <span @class(['bq-chip', 'bq-chip--ok' => $pdfViews > 0])>
                <x-filament::icon icon="heroicon-m-eye" />
                {{ $pdfViews > 0 ? 'Cliente abriu o PDF ' . $pdfViews . '×' : 'PDF ainda não aberto' }}
            </span>
        @endif
        @if ($deliveryDate)
            <span class="bq-chip bq-chip--warn">
                <x-filament::icon icon="heroicon-m-truck" />
                Entrega desejada: {{ $deliveryDate }}{{ $deliveryPeriod ? ' · ' . $deliveryPeriod : '' }}
            </span>
        @endif

        <span class="bq-chips-spacer"></span>

        @if ($isArchived)
            <button type="button" class="bq-link-button" wire:click="setStatus('pending')">Reabrir orçamento</button>
        @else
            <button type="button" class="bq-link-button" wire:click="setStatus('ignored')" wire:confirm="Arquivar este orçamento?">Arquivar</button>
        @endif
    </div>
</div>
