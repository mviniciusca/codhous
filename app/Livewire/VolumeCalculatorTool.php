<?php

namespace App\Livewire;

use App\Enums\AccessArea;
use App\Filament\Clusters\ToolsCluster\Pages\VolumeCalculatorPage;
use App\Models\VolumeCalculation;
use App\Models\VolumeFormula;
use App\Services\VolumeCalculator\CalculationResult;
use App\Services\VolumeCalculator\Expression\ExpressionError;
use App\Services\VolumeCalculator\InvalidInputs;
use App\Services\VolumeCalculator\VolumeCalculator;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Calculadora de volume do painel. É a mesma no modal global (topbar e Visão
 * geral) e na página Ferramentas › Calculadora de volume. A conta é sempre do
 * servidor (VolumeCalculator); a tela só coleta as medidas e mostra a memória.
 */
class VolumeCalculatorTool extends Component
{
    public const MARGIN_OPTIONS = [0, 5, 10, 15, 20];

    /** modal | page */
    #[Locked]
    public string $variant = 'modal';

    public ?int $formulaId = null;

    /** @var array<string, string> variável => valor digitado */
    public array $values = [];

    /** Margem em %, como texto do select */
    public string $margin = '';

    public string $reference = '';

    public static function canUse(): bool
    {
        return (bool) auth()->user()?->canAccessArea(AccessArea::Comercial);
    }

    public function mount(string $variant = 'modal'): void
    {
        abort_unless(static::canUse(), 403);

        $this->variant = $variant === 'page' ? 'page' : 'modal';
        $this->selectFormula($this->formulas->first()?->getKey());
    }

    public function hydrate(): void
    {
        abort_unless(static::canUse(), 403);
    }

    /** @return Collection<int, VolumeFormula> */
    #[Computed]
    public function formulas(): Collection
    {
        return VolumeFormula::active()->ordered()->has('currentVersion')->with('currentVersion')->get();
    }

    #[Computed]
    public function formula(): ?VolumeFormula
    {
        return $this->formulas->firstWhere('id', $this->formulaId);
    }

    public function selectFormula(?int $id): void
    {
        $formula = $this->formulas->firstWhere('id', $id) ?? $this->formulas->first();

        $this->formulaId = $formula?->getKey();
        $this->values = collect($formula?->definition()?->variables ?? [])->mapWithKeys(fn ($variable) => [$variable->key => ''])->all();
        $this->margin = $formula ? $this->formatMargin($formula->definition()->marginPercent) : '';
        $this->resetErrorBag();
        unset($this->formula, $this->calculation);
    }

    public function fillExample(): void
    {
        foreach ($this->formula?->definition()?->variables ?? [] as $variable) {
            if ($variable->example !== null) {
                $this->values[$variable->key] = $this->formatMargin($variable->example);
            }
        }
    }

    public function clear(): void
    {
        $this->selectFormula($this->formulaId);
        $this->reference = '';
    }

    /** @return array<int, array{value: string, label: string}> */
    public function marginOptions(): array
    {
        $default = $this->formula?->definition()?->marginPercent ?? 10;

        return collect(self::MARGIN_OPTIONS)
            ->push($default)
            ->unique(fn ($value) => (string) (float) $value)
            ->sort()
            ->map(fn ($value) => [
                'value' => $this->formatMargin($value),
                'label' => $this->formatMargin($value).'%'.((float) $value === (float) $default ? ' (padrão)' : ''),
            ])
            ->values()
            ->all();
    }

    /**
     * Resultado do cálculo com o estado atual. Campos vazios não geram erro
     * enquanto o usuário digita; só os preenchidos com valor inválido.
     *
     * @return array{result: ?CalculationResult, errors: array<string, string>, error: ?string, complete: bool}
     */
    #[Computed]
    public function calculation(): array
    {
        $definition = $this->formula?->definition();
        $state = ['result' => null, 'errors' => [], 'error' => null, 'complete' => false];

        if (! $definition) {
            return $state;
        }

        $state['complete'] = collect($definition->variables)->every(fn ($variable) => trim((string) ($this->values[$variable->key] ?? '')) !== '');

        try {
            $state['result'] = app(VolumeCalculator::class)->calculate($definition, $this->values, $this->marginValue());
        } catch (InvalidInputs $e) {
            $state['errors'] = collect($e->errors)
                ->filter(fn ($message, $key) => ! array_key_exists($key, $this->values) || trim((string) $this->values[$key]) !== '')
                ->all();
        } catch (ExpressionError $e) {
            $state['error'] = $e->getMessage();
        }

        return $state;
    }

    public function save(): void
    {
        $calculation = $this->calculation;
        $version = $this->formula?->currentVersion;

        if (! $calculation['result'] || ! $version) {
            Notification::make()->title('Preencha todas as medidas antes de salvar')->warning()->send();

            return;
        }

        VolumeCalculation::record($version, $this->values, $calculation['result'], auth()->user(), $this->reference);

        Notification::make()
            ->title('Cálculo salvo no histórico')
            ->body(CalculationResult::formatVolume($calculation['result']->finalVolume).' · '.$this->formula->name.' '.$version->label())
            ->success()
            ->send();

        $this->reference = '';
        $this->dispatch('volume-calculation-saved');
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, VolumeCalculation> */
    #[Computed]
    public function recent()
    {
        return VolumeCalculation::query()
            ->where('user_id', auth()->id())
            ->with('formulaVersion.formula')
            ->latest()
            ->limit(5)
            ->get();
    }

    public function pageUrl(): string
    {
        return VolumeCalculatorPage::getUrl();
    }

    protected function marginValue(): float
    {
        $value = VolumeCalculator::parseNumber($this->margin);

        return $value ?? ($this->formula?->definition()?->marginPercent ?? 0);
    }

    protected function formatMargin(float $value): string
    {
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }

    public function placeholder(): string
    {
        return <<<'HTML'
            <div class="vc-loading" role="status">Carregando calculadora…</div>
            HTML;
    }

    public function render()
    {
        return view('livewire.volume-calculator-tool');
    }
}
