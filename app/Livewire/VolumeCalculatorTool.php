<?php

namespace App\Livewire;

use App\Enums\AccessArea;
use App\Filament\Clusters\ToolsCluster\Pages\VolumeCalculatorPage;
use App\Livewire\Concerns\CalculatesVolume;
use App\Models\VolumeCalculation;
use App\Services\VolumeCalculator\CalculationResult;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
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
    use CalculatesVolume;

    /** modal | page */
    #[Locked]
    public string $variant = 'modal';

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

    public function clear(): void
    {
        $this->selectFormula($this->formulaId);
        $this->reference = '';
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

    /** @return Collection<int, VolumeCalculation> */
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
