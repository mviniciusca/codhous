@php $tz = config('app.display_timezone'); @endphp

@if (empty($changes))
    <p style="font-size: 14px; color: rgb(var(--gray-500));">Nenhuma alteração registrada ainda.</p>
@else
    <ol style="list-style: none; margin: 0; padding: 0; display: grid; gap: 12px;">
        @foreach ($changes as $change)
            <li style="padding-bottom: 12px; border-bottom: 1px solid rgba(var(--gray-400), .25);">
                <div style="display: flex; justify-content: space-between; gap: 12px; font-size: 13px;">
                    <strong>{{ $change['title'] }}</strong>
                    <span style="color: rgb(var(--gray-500)); white-space: nowrap;">
                        {{ $change['actor'] }} · {{ $change['at']->copy()->timezone($tz)->format('d/m/Y H:i') }}
                    </span>
                </div>
                @foreach ($change['lines'] as $line)
                    <div style="font-size: 13px; color: rgb(var(--gray-600)); margin-top: 2px; font-variant-numeric: tabular-nums;">{{ $line }}</div>
                @endforeach
            </li>
        @endforeach
    </ol>
@endif
