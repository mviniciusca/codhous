{{-- Memória de cálculo de um cálculo salvo (histórico) --}}
@php
    use App\Services\VolumeCalculator\CalculationResult;
    use App\Services\VolumeCalculator\Expression\Expression;

    $result = $calculation->calculationResult();
    $version = $calculation->formulaVersion;
@endphp

@include('filament.volume-formulas.styles')

<div class="cx-ui vf-test">
    <dl class="vf-rows">
        <div><dt>Identificação</dt><dd>{{ $calculation->reference ?: '—' }}</dd></div>
        <div><dt>Fórmula</dt><dd>{{ $version?->formula?->name }} · {{ $version?->label() }}</dd></div>
        <div><dt>Calculado por</dt><dd>{{ $calculation->user?->name ?? '—' }} em {{ $calculation->created_at->format('d/m/Y H:i') }}</dd></div>
    </dl>

    <ol class="vf-steps">
        <li>
            <span>Fórmula</span>
            <code>{{ $result->formula }}</code>
        </li>
        <li>
            <span>Medidas</span>
            <dl class="vf-rows">
                @foreach ($result->inputs as $input)
                    <div>
                        <dt>{{ $input['label'] }}</dt>
                        <dd>{{ Expression::formatNumber($input['value']) }} {{ $input['unit'] }}@if ($input['unit'] !== $input['base_unit']) <small>= {{ Expression::formatNumber($input['base_value']) }} {{ $input['base_unit'] }}</small>@endif</dd>
                    </div>
                @endforeach
            </dl>
        </li>
        @foreach ($result->steps as $step)
            <li>
                <span>{{ $step['label'] }}</span>
                <code>{{ $step['detail'] }} = {{ Expression::formatNumber($step['value']) }} m³</code>
            </li>
        @endforeach
    </ol>

    <div class="vf-result">
        <span>Volume final</span>
        <strong>{{ CalculationResult::formatVolume($result->finalVolume) }}</strong>
    </div>
</div>
