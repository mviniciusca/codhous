{{-- Painel “Testar fórmula” do editor: valida a regra e calcula com os valores de exemplo. Prefixo vf- --}}
@php
    use App\Services\VolumeCalculator\CalculationResult;
    use App\Services\VolumeCalculator\Expression\Expression;
    use App\Services\VolumeCalculator\Expression\ExpressionError;
    use App\Services\VolumeCalculator\InvalidInputs;

    $result = null;
    $problem = null;
    $missing = collect($definition->variables)->filter(fn ($v) => $v->example === null)->pluck('label');

    if (! $errors && $missing->isEmpty()) {
        try {
            $result = $calculator->calculate($definition, collect($definition->variables)->mapWithKeys(fn ($v) => [$v->key => $v->example])->all());
        } catch (InvalidInputs|ExpressionError $e) {
            $problem = $e->getMessage();
        }
    }
@endphp

@include('filament.volume-formulas.styles')

<div class="cx-ui vf-test">
    @if ($errors)
        <div class="vf-box cx-tone-danger" role="alert">
            <p class="vf-status"><x-filament::icon icon="heroicon-m-x-circle" class="cx-icon-sm" /> A fórmula ainda não é válida</p>
            <ul>
                @foreach ($errors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @else
        <p class="vf-status cx-tone-success"><x-filament::icon icon="heroicon-m-check-circle" class="cx-icon-sm" /> Sintaxe e unidades conferidas: resulta em m³</p>

        @if ($missing->isNotEmpty())
            <p class="vf-muted">Informe o valor de exemplo de: {{ $missing->implode(', ') }}.</p>
        @elseif ($problem)
            <div class="vf-box cx-tone-danger">{{ $problem }}</div>
        @elseif ($result)
            <div class="vf-result">
                <span>Volume final</span>
                <strong>{{ CalculationResult::formatVolume($result->finalVolume) }}</strong>
            </div>
            <dl class="vf-rows">
                @foreach ($result->inputs as $input)
                    <div>
                        <dt>{{ $input['label'] }}</dt>
                        <dd>{{ Expression::formatNumber($input['value']) }} {{ $input['unit'] }}@if ($input['unit'] !== $input['base_unit']) <small>= {{ Expression::formatNumber($input['base_value']) }} {{ $input['base_unit'] }}</small>@endif</dd>
                    </div>
                @endforeach
            </dl>
            <ol class="vf-steps">
                @foreach ($result->steps as $step)
                    <li>
                        <span>{{ $step['label'] }}</span>
                        <code>{{ $step['detail'] }} = {{ Expression::formatNumber($step['value']) }} m³</code>
                    </li>
                @endforeach
            </ol>
        @endif
    @endif
</div>
