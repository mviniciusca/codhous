{{-- Status do alerta no editor: situação salva e período de exibição (acompanha os campos de data) --}}
@php
    use App\Models\Alert;

    /** @var Alert|null $record */
    $record = $getRecord();
    $data = $getLivewire()->data ?? [];
    $statuses = Alert::statusCatalog();
    $status = $record?->exists ? $record->status() : null;

    $formatDate = function (mixed $value): ?string {
        if (blank($value) || ! is_scalar($value)) {
            return null;
        }

        try {
            return \Illuminate\Support\Carbon::parse((string) $value)->format('d/m/Y H:i');
        } catch (\Throwable) {
            return null;
        }
    };

    $start = $formatDate($data['start_at'] ?? null);
    $end = $formatDate($data['end_at'] ?? null);
    $period = match (true) {
        $start && $end => "De {$start} até {$end}",
        (bool) $start => "A partir de {$start}, sem término",
        (bool) $end => "Assim que publicar, até {$end}",
        default => 'Assim que publicar, sem término',
    };

    $description = match ($status) {
        Alert::STATUS_ACTIVE => 'Publicado e aparecendo no site agora.',
        Alert::STATUS_SCHEDULED => 'Publicado. Aparece no site quando o período começar.',
        Alert::STATUS_EXPIRED => 'Publicado, mas o período de exibição já terminou.',
        Alert::STATUS_DRAFT => 'Rascunho. Não aparece no site até ser publicado.',
        default => 'Ainda não salvo. Salve como rascunho para revisar depois ou publique para exibir.',
    };
@endphp

<section class="al-panel al-status cx-ui" aria-labelledby="al-status-title">
    <header class="al-panel-head">
        <span class="al-panel-icon" aria-hidden="true"><x-filament::icon icon="heroicon-o-information-circle" /></span>
        <div class="al-panel-text">
            <h2 id="al-status-title" class="al-panel-title">Status e informações</h2>
            <p class="al-panel-desc">{{ $description }}</p>
        </div>
        @if ($status)
            <span class="cx-badge cx-tone-{{ $statuses[$status]['color'] }}">
                <x-filament::icon :icon="$statuses[$status]['icon']" />
                {{ $statuses[$status]['label'] }}
            </span>
        @else
            <span class="cx-badge cx-tone-gray">
                <x-filament::icon icon="heroicon-m-sparkles" />
                Novo
            </span>
        @endif
    </header>

    <dl class="al-facts">
        <div>
            <dt><x-filament::icon icon="heroicon-m-calendar-days" class="cx-icon-xs" /> Exibição</dt>
            <dd>{{ $period }}</dd>
        </div>
        @if ($record?->exists)
            <div>
                <dt><x-filament::icon icon="heroicon-m-clock" class="cx-icon-xs" /> Atualizado</dt>
                <dd>{{ $record->updated_at?->format('d/m/Y H:i') }}</dd>
            </div>
            <div>
                <dt><x-filament::icon icon="heroicon-m-plus-circle" class="cx-icon-xs" /> Criado</dt>
                <dd>{{ $record->created_at?->format('d/m/Y H:i') }}</dd>
            </div>
        @endif
    </dl>
</section>
