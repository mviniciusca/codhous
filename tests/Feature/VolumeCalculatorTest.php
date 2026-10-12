<?php

use App\Filament\Clusters\ToolsCluster\Pages\VolumeCalculatorPage;
use App\Filament\Pages\Dashboard;
use App\Filament\Resources\VolumeCalculationResource;
use App\Filament\Resources\VolumeCalculationResource\Pages\ListVolumeCalculations;
use App\Filament\Resources\VolumeFormulaResource;
use App\Filament\Resources\VolumeFormulaResource\Pages\CreateVolumeFormula;
use App\Filament\Resources\VolumeFormulaResource\Pages\EditVolumeFormula;
use App\Filament\Resources\VolumeFormulaResource\RelationManagers\VersionsRelationManager;
use App\Livewire\VolumeCalculatorTool;
use App\Models\User;
use App\Models\VolumeCalculation;
use App\Models\VolumeFormula;
use App\Services\VolumeCalculator\FormulaDefinition;
use App\Services\VolumeCalculator\VolumeCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['name' => 'Ana Admin']);
    $this->actingAs($this->user);
    $this->slab = VolumeFormula::where('slug', 'laje-piso')->firstOrFail();
});

test('a migração instala as fórmulas padrão, válidas e na versão 1', function () {
    $formulas = VolumeFormula::ordered()->with('currentVersion')->get();

    expect($formulas->pluck('slug')->all())->toBe(['laje-piso', 'pilar-estaca-circular', 'pilar-viga-retangular'])
        ->and($formulas->every(fn ($f) => $f->is_system && $f->currentVersion->version === 1 && $f->definition()->isValid()))->toBeTrue();
});

test('a calculadora calcula enquanto digita e mostra a memória de cálculo', function () {
    Livewire::test(VolumeCalculatorTool::class, ['variant' => 'page'])
        ->assertSet('formulaId', $this->slab->id)
        ->assertSet('margin', '10')
        ->assertSee('Preencha as medidas')
        ->set('values.largura', '5')
        ->set('values.comprimento', '8')
        ->set('values.espessura', '10')
        ->assertSee('4,40')
        ->assertSee('Memória de cálculo')
        ->assertSee('5 × 8 × 0,1')
        ->assertSee('0,1 m')
        ->set('margin', '5')
        ->assertSee('4,20');
});

test('erros aparecem só nos campos preenchidos com valor inválido', function () {
    Livewire::test(VolumeCalculatorTool::class)
        ->set('values.largura', '-2')
        ->assertSee('Largura deve ser maior que zero.')
        ->assertDontSee('Informe comprimento.');
});

test('trocar de fórmula limpa as medidas e usa a margem padrão dela', function () {
    $cylinder = VolumeFormula::where('slug', 'pilar-estaca-circular')->first();

    Livewire::test(VolumeCalculatorTool::class)
        ->set('values.largura', '5')
        ->call('selectFormula', $cylinder->id)
        ->assertSet('values', ['diametro' => '', 'altura' => '', 'quantidade' => ''])
        ->call('fillExample')
        ->assertSet('values', ['diametro' => '30', 'altura' => '6', 'quantidade' => '10'])
        ->assertSee('4,66'); // π × 0,15² × 6 × 10 × 1,1
});

test('salvar grava o cálculo com a versão da fórmula usada', function () {
    Livewire::test(VolumeCalculatorTool::class)
        ->set('values', ['largura' => '5', 'comprimento' => '8', 'espessura' => '10'])
        ->set('reference', 'Obra Rua A — térreo')
        ->call('save')
        ->assertNotified('Cálculo salvo no histórico')
        ->assertSet('reference', '')
        ->assertDispatched('volume-calculation-saved');

    $calculation = VolumeCalculation::sole();

    expect($calculation->reference)->toBe('Obra Rua A — térreo')
        ->and($calculation->user_id)->toBe($this->user->id)
        ->and($calculation->formulaVersion->is($this->slab->currentVersion))->toBeTrue()
        ->and($calculation->final_volume)->toBe(4.4)
        ->and($calculation->base_volume)->toBe(4.0)
        ->and($calculation->result['steps'][0]['detail'])->toBe('5 × 8 × 0,1');
});

test('não salva cálculo incompleto', function () {
    Livewire::test(VolumeCalculatorTool::class)
        ->set('values.largura', '5')
        ->call('save')
        ->assertNotified('Preencha todas as medidas antes de salvar');

    expect(VolumeCalculation::count())->toBe(0);
});

test('alterar a regra cria versão nova e o cálculo antigo continua reproduzível', function () {
    Livewire::test(VolumeCalculatorTool::class)
        ->set('values', ['largura' => '5', 'comprimento' => '8', 'espessura' => '10'])
        ->call('save');
    $old = VolumeCalculation::sole();

    $changed = FormulaDefinition::fromArray(array_merge($this->slab->definition()->toArray(), ['margin_percent' => 5, 'rounding' => 'up', 'rounding_step' => 0.5]));

    expect($this->slab->saveVersion($this->slab->definition(), $this->user))->toBeNull()
        ->and($this->slab->saveVersion($changed, $this->user, 'margem menor')->version)->toBe(2)
        ->and($this->slab->fresh()->currentVersion->version)->toBe(2);

    $replayed = app(VolumeCalculator::class)->calculate($old->formulaVersion->toDefinition(), $old->inputs, $old->margin_percent);
    expect($replayed->finalVolume)->toBe($old->final_volume)
        ->and(fn () => $old->formulaVersion->update(['notes' => 'x']))->toThrow(LogicException::class);

    Livewire::test(VolumeCalculatorTool::class)
        ->set('values', ['largura' => '5', 'comprimento' => '8', 'espessura' => '10'])
        ->assertSet('margin', '5')
        ->assertSee('4,50'); // 4,2 arredondado para cima em 0,5
});

test('o editor cria fórmula personalizada e recusa expressão que não resulta em m³', function () {
    $variables = [
        ['label' => 'Área', 'key' => 'area', 'unit' => 'm2', 'example' => 20, 'min' => null, 'max' => null, 'help' => null],
        ['label' => 'Espessura', 'key' => 'espessura', 'unit' => 'cm', 'example' => 8, 'min' => null, 'max' => null, 'help' => null],
    ];

    Livewire::test(CreateVolumeFormula::class)
        ->fillForm([
            'name' => 'Contrapiso por área',
            'shape' => 'slab',
            'is_active' => true,
            'variables' => $variables,
            'expression' => 'area * area',
            'margin_percent' => 8,
            'rounding' => 'none',
        ])
        ->call('create')
        ->assertHasFormErrors(['expression']);

    Livewire::test(CreateVolumeFormula::class)
        ->fillForm([
            'name' => 'Contrapiso por área',
            'shape' => 'slab',
            'is_active' => true,
            'variables' => $variables,
            'expression' => 'area * espessura',
            'margin_percent' => 8,
            'rounding' => 'up',
            'rounding_step' => 0.5,
        ])
        ->assertSee('Sintaxe e unidades conferidas')
        ->assertSee('2 m³') // 20 × 0,08 × 1,08 = 1,728 → 2,00
        ->call('create')
        ->assertHasNoFormErrors();

    $formula = VolumeFormula::where('name', 'Contrapiso por área')->sole();
    expect($formula->is_system)->toBeFalse()
        ->and($formula->slug)->toBe('contrapiso-por-area')
        ->and($formula->currentVersion->definition['expression'])->toBe('area * espessura')
        ->and($formula->currentVersion->created_by)->toBe($this->user->id);
});

test('editar só nome não cria versão; editar a regra cria, com a nota', function () {
    Livewire::test(EditVolumeFormula::class, ['record' => $this->slab->getRouteKey()])
        ->assertFormSet(['expression' => 'largura * comprimento * espessura', 'margin_percent' => 10])
        ->fillForm(['name' => 'Lajes e pisos'])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified('Fórmula salva');

    expect($this->slab->fresh()->name)->toBe('Lajes e pisos')
        ->and($this->slab->versions()->count())->toBe(1);

    Livewire::test(EditVolumeFormula::class, ['record' => $this->slab->getRouteKey()])
        ->fillForm(['margin_percent' => 7, 'version_notes' => 'Margem da obra padrão'])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified('Fórmula salva como v2');

    $current = $this->slab->fresh()->currentVersion;
    expect($current->version)->toBe(2)
        ->and($current->notes)->toBe('Margem da obra padrão')
        ->and($current->definition['margin_percent'])->toEqual(7);
});

test('restaurar uma versão antiga cria uma versão nova', function () {
    $this->slab->saveVersion(FormulaDefinition::fromArray(array_merge($this->slab->definition()->toArray(), ['margin_percent' => 3])), $this->user);
    $v1 = $this->slab->versions()->where('version', 1)->first();

    Livewire::test(VersionsRelationManager::class, ['ownerRecord' => $this->slab->fresh(), 'pageClass' => EditVolumeFormula::class])
        ->assertCanSeeTableRecords($this->slab->versions)
        ->callTableAction('restore', $v1)
        ->assertNotified('Versão restaurada como v3');

    expect($this->slab->fresh()->definition()->marginPercent)->toEqual(10.0);
});

test('fórmula do sistema ou já usada não pode ser excluída', function () {
    $custom = VolumeFormula::create(['name' => 'Teste']);
    $custom->saveVersion($this->slab->definition());

    expect($this->slab->canBeDeleted())->toBeFalse()
        ->and($custom->canBeDeleted())->toBeTrue()
        ->and(fn () => $this->slab->delete())->toThrow(InvalidArgumentException::class);

    VolumeCalculation::record($custom->currentVersion, ['largura' => 1, 'comprimento' => 1, 'espessura' => 10],
        app(VolumeCalculator::class)->calculate($custom->definition(), ['largura' => 1, 'comprimento' => 1, 'espessura' => 10]), $this->user);

    expect($custom->canBeDeleted())->toBeFalse();
});

test('a calculadora fica acessível pela topbar, pela visão geral e pela página', function () {
    $this->get(Dashboard::getUrl())
        ->assertOk()
        ->assertSee('Calcular volume')
        ->assertSee('aria-label="Calculadora de volume"', false)
        ->assertSee('volume-calculator');

    $this->get(VolumeCalculatorPage::getUrl())
        ->assertOk()
        ->assertSee('Calculadora de volume')
        ->assertSee('Lajes / Pisos')
        ->assertSee('Pilares / Estacas');

    $this->get(VolumeFormulaResource::getUrl())->assertOk()->assertSee('Fórmulas de volume');
    $this->get(VolumeFormulaResource::getUrl('edit', ['record' => $this->slab]))->assertOk()->assertSee('Testar fórmula');
    $this->get(VolumeCalculationResource::getUrl())->assertOk()->assertSee('Nenhum cálculo salvo');
});

test('o histórico lista os cálculos e mostra a memória', function () {
    $result = app(VolumeCalculator::class)->calculate($this->slab->definition(), ['largura' => 5, 'comprimento' => 8, 'espessura' => 10]);
    $calculation = VolumeCalculation::record($this->slab->currentVersion, ['largura' => '5', 'comprimento' => '8', 'espessura' => '10'], $result, $this->user, 'Casa do João');

    Livewire::test(ListVolumeCalculations::class)
        ->assertCanSeeTableRecords([$calculation])
        ->assertSee('Casa do João')
        ->assertSee('4,40 m³')
        ->mountTableAction('view', $calculation)
        ->assertSee('Memória de cálculo')
        ->assertSee('5 × 8 × 0,1');
});

test('sem a área Comercial não há calculadora nem histórico; fórmulas exigem Produtos e operação', function () {
    $this->actingAs(User::factory()->withRole('Marketing')->create());

    $this->get(VolumeCalculatorPage::getUrl())->assertForbidden();
    $this->get(VolumeCalculationResource::getUrl())->assertForbidden();
    $this->get(VolumeFormulaResource::getUrl())->assertForbidden();
    $this->get(Dashboard::getUrl())
        ->assertOk()
        ->assertDontSee('Calcular volume')
        ->assertDontSee('aria-label="Calculadora de volume"', false);

    Livewire::test(VolumeCalculatorTool::class)->assertForbidden();
});

test('perfil Comercial usa a calculadora e edita fórmulas', function () {
    $this->actingAs(User::factory()->withRole('Comercial')->create());

    $this->get(VolumeCalculatorPage::getUrl())->assertOk();
    $this->get(VolumeFormulaResource::getUrl())->assertOk();
});
