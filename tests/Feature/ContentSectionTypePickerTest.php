<?php

use App\Filament\Forms\Components\SectionTypePicker;
use App\Filament\Resources\ContentSectionResource\Pages\CreateContentSection;
use App\Filament\Resources\ContentSectionResource\Pages\EditContentSection;
use App\Models\ContentSection;
use App\Models\Setting;
use App\Models\User;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Setting::create(['settings' => ['maintenance_mode' => false]]);

    $this->actingAs(User::factory()->create());
});

test('todo tipo de seção tem metadados e miniatura na galeria', function () {
    $categories = array_keys(ContentSection::typeCategories());

    foreach (ContentSection::typeCatalog() as $type => $meta) {
        expect($meta['description'])->not->toBeEmpty()
            ->and($meta['categories'])->not->toBeEmpty()
            ->and(array_diff($meta['categories'], $categories))->toBeEmpty()
            ->and(view()->exists("filament.forms.components.section-type-picker.previews.{$type}"))->toBeTrue();
    }

    expect(array_keys(ContentSection::typeCatalog()))->toBe(array_keys(ContentSection::typeLabels()));
});

test('construtor exibe biblioteca, prévia e inspetor na criação', function () {
    ContentSection::create(['slug' => 'faq', 'name' => 'FAQ', 'type' => ContentSection::TYPE_FAQ, 'content' => []]);

    livewire(CreateContentSection::class)
        ->assertOk()
        ->assertSee('Biblioteca de componentes')
        ->assertSee('Buscar componentes…')
        ->assertSee('Calculadora de Volume')
        ->assertSee('1 em uso')
        ->assertSee('Escolha um componente')
        ->assertSee('Criar seção')
        ->assertSee('Criar e adicionar outra');
});

test('não avança da etapa Componente sem escolher um tipo', function () {
    livewire(CreateContentSection::class)
        ->call('dispatchFormEvent', 'wizard::nextStep', 'data', 0)
        ->assertHasErrors(['data.type'])
        ->assertNotDispatched('next-wizard-step');

    livewire(CreateContentSection::class)
        ->fillForm(['type' => ContentSection::TYPE_FAQ])
        ->call('dispatchFormEvent', 'wizard::nextStep', 'data', 0)
        ->assertHasNoErrors()
        ->assertDispatched('next-wizard-step');
});

test('prévia ao vivo renderiza o conteúdo ainda não salvo', function () {
    $url = livewire(CreateContentSection::class)
        ->fillForm([
            'type' => ContentSection::TYPE_FAQ,
            'content.header.title' => 'Dúvidas da obra',
            'content.items' => [['question' => 'Qual o prazo de entrega?', 'answer' => 'Em até 24 horas.']],
        ])
        ->call('generateSectionPreviewUrl')
        ->effects['returns'][0];

    expect($url)->toContain('/admin/section-builder/preview/');

    $this->get($url)
        ->assertOk()
        ->assertSee('Dúvidas da obra')
        ->assertSee('Qual o prazo de entrega?')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN');

    expect(ContentSection::count())->toBe(0);

    // O snapshot é do usuário que o gerou
    $this->actingAs(User::factory()->create())->get($url)->assertNotFound();
});

test('prévia não é gerada sem tipo escolhido', function () {
    $url = livewire(CreateContentSection::class)
        ->call('generateSectionPreviewUrl')
        ->effects['returns'][0];

    expect($url)->toBeNull();
});

test('escolher um tipo preenche slug e nome e a seção é criada', function () {
    livewire(CreateContentSection::class)
        ->fillForm(['type' => ContentSection::TYPE_CALCULATOR])
        ->assertFormSet([
            'slug' => 'calculator',
            'name' => 'Calculadora de Volume',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(ContentSection::where('slug', 'calculator')->value('type'))->toBe(ContentSection::TYPE_CALCULATOR);
});

test('tipo é obrigatório e precisa existir', function () {
    livewire(CreateContentSection::class)
        ->fillForm(['type' => null, 'slug' => 'x', 'name' => 'X'])
        ->call('create')
        ->assertHasFormErrors(['type' => 'required']);

    livewire(CreateContentSection::class)
        ->fillForm(['type' => 'inexistente', 'slug' => 'x', 'name' => 'X'])
        ->call('create')
        ->assertHasFormErrors(['type' => 'in']);
});

test('na edição o tipo aparece bloqueado', function () {
    $section = ContentSection::create(['slug' => 'services', 'name' => 'Serviços', 'type' => ContentSection::TYPE_SERVICES, 'content' => []]);

    livewire(EditContentSection::class, ['record' => $section->getRouteKey()])
        ->assertOk()
        ->assertSee('não pode ser alterado depois de criado')
        ->assertSee('Salvar alterações')
        ->assertDontSee('Buscar componentes…');
});

test('a prévia de cada tipo responde mesmo sem conteúdo', function (string $type) {
    $url = livewire(CreateContentSection::class)
        ->fillForm(['type' => $type])
        ->call('generateSectionPreviewUrl')
        ->effects['returns'][0];

    $this->get($url)->assertOk()->assertSee('data-sb-section', escape: false);
})->with(fn () => array_keys(ContentSection::typeLabels()));

test('todos os campos do formulário têm helper text e, quando suportado, prefix icon', function () {
    $form = livewire(CreateContentSection::class)->instance()->form;

    $walk = function (array $components) use (&$walk): array {
        $fields = [];

        foreach ($components as $component) {
            if ($component instanceof Field) {
                $fields[] = $component;
            }

            $fields = [...$fields, ...$walk($component->getChildComponents())];
        }

        return $fields;
    };

    $missingHelper = [];
    $missingPrefix = [];
    $affixFields = [
        TextInput::class,
        Select::class,
        TagsInput::class,
        ColorPicker::class,
        DateTimePicker::class,
    ];

    foreach ($walk($form->getComponents(withHidden: true)) as $field) {
        // A biblioteca de componentes é o próprio seletor, sem rótulo nem ajuda
        if ($field instanceof SectionTypePicker) {
            continue;
        }

        if (blank($field->getHelperText())) {
            $missingHelper[] = $field->getName();
        }

        foreach ($affixFields as $class) {
            if ($field instanceof $class && blank($field->getPrefixIcon())) {
                $missingPrefix[] = $field->getName();
            }
        }
    }

    expect($missingHelper)->toBe([])->and($missingPrefix)->toBe([]);
});
