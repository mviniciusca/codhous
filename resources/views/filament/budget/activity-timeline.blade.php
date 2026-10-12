@php
    /** @var \Illuminate\Support\Collection $groups */
    $tz = config('app.display_timezone');
@endphp

<div class="bq-timeline">
    @forelse ($groups as $day => $entries)
        <div class="bq-tl-day">{{ $day }}</div>

        <ol class="bq-tl-list">
            @foreach ($entries as $entry)
                <li class="bq-tl-item" wire:key="activity-{{ $entry['id'] }}">
                    <span class="bq-tl-icon bq-tl-icon--{{ $entry['color'] }}">
                        <x-filament::icon :icon="$entry['icon']" />
                    </span>

                    <div class="bq-tl-body">
                        <div class="bq-tl-head">
                            <strong>{{ $entry['title'] }}</strong>
                            <span class="bq-tl-meta">
                                {{ $entry['actor'] }} · <time datetime="{{ $entry['at']->toIso8601String() }}" title="{{ $entry['at']->copy()->timezone($tz)->format('d/m/Y H:i:s') }}">{{ $entry['at']->copy()->timezone($tz)->format('H:i') }}</time>
                            </span>
                        </div>

                        @foreach ($entry['details'] as $detail)
                            @if (filled($detail))
                                <p class="bq-tl-detail">{{ $detail }}</p>
                            @endif
                        @endforeach

                        @if (! empty($entry['changes']))
                            <dl class="bq-tl-changes">
                                @foreach ($entry['changes'] as $change)
                                    <div class="bq-tl-change">
                                        <dt>{{ $change['label'] }}</dt>
                                        <dd>
                                            <span class="bq-tl-old">{{ $change['old'] }}</span>
                                            <x-filament::icon icon="heroicon-m-arrow-long-right" class="bq-tl-arrow" />
                                            <span class="bq-tl-new">{{ $change['new'] }}</span>
                                            @if ($change['delta'])
                                                <span class="bq-tl-delta bq-tl-delta--{{ $change['trend'] ?? 'neutral' }}">{{ $change['delta'] }}</span>
                                            @endif
                                        </dd>
                                    </div>
                                @endforeach
                            </dl>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>
    @empty
        <p class="bq-tl-empty">Nenhuma atividade registrada ainda.</p>
    @endforelse

    @if ($hasMore)
        <div class="bq-tl-more">
            <x-filament::button color="gray" size="sm" wire:click="loadMoreTimeline" wire:loading.attr="disabled" wire:target="loadMoreTimeline">
                Ver atividades anteriores
            </x-filament::button>
        </div>
    @endif
</div>
