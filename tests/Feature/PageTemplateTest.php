<?php

use App\Filament\Resources\PageResource\Pages\CreatePage;
use App\Filament\Resources\PageResource\PageTemplates;
use App\Models\ContentSection;
use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Setting::create(['settings' => ['maintenance_mode' => false]]);

    $this->actingAs(User::factory()->create());
});

dataset('templates', fn () => array_keys(PageTemplates::all()));

test('template aplica os blocos esperados e a página é salva', function (string $key) {
    $template = PageTemplates::find($key);
    $expectedTypes = collect(PageTemplates::resolve($template)['blocks'])->pluck('type')->all();

    livewire(CreatePage::class)
        ->callFormComponentAction('content', 'loadTemplate', ['template' => $key])
        ->assertHasNoFormComponentActionErrors()
        ->fillForm(['title' => 'Página '.$key, 'slug' => 'pagina-'.$key])
        ->call('create')
        ->assertHasNoFormErrors();

    $page = Page::where('slug', 'pagina-'.$key)->firstOrFail();

    expect(collect($page->content)->pluck('type')->all())->toBe($expectedTypes);

    // Os textos do template sobrevivem ao salvar (campos existem no schema do bloco)
    collect(PageTemplates::resolve($template)['blocks'])->each(function (array $block, int $index) use ($page) {
        $saved = array_values($page->content)[$index]['data'];

        foreach (['title', 'badge', 'subtitle', 'description', 'quote'] as $field) {
            if (isset($block['data'][$field])) {
                expect($saved[$field] ?? null)->toBe($block['data'][$field]);
            }
        }

        foreach (['items', 'steps'] as $list) {
            if (isset($block['data'][$list])) {
                expect($saved[$list])->toHaveCount(count($block['data'][$list]));
            }
        }
    });
})->with('templates');

test('módulos globais viram referência à seção ativa ou são ignorados', function () {
    $hero = ContentSection::create(['slug' => 'hero', 'name' => 'Hero', 'type' => ContentSection::TYPE_HERO, 'content' => [], 'is_active' => true]);

    $resolved = PageTemplates::resolve(PageTemplates::find('home_conversao'));

    expect($resolved['blocks'][0])->toBe(['type' => 'module_reference', 'data' => ['content_section_id' => $hero->id]])
        ->and($resolved['skipped'])->toContain(ContentSection::typeLabels()[ContentSection::TYPE_BUDGET_FORM]);

    // Contato cai para o banner de atendimento quando não há formulário global
    expect(PageTemplates::resolve(PageTemplates::find('contato'))['blocks'][1]['type'])->toBe('contact_banner');
});

test('substituir apaga os blocos atuais e adicionar mantém', function () {
    $component = livewire(CreatePage::class)
        ->callFormComponentAction('content', 'loadTemplate', ['template' => 'servicos']);

    $countServicos = count($component->get('data.content'));

    $component->callFormComponentAction('content', 'loadTemplate', ['template' => 'faq', 'behavior' => 'append']);
    $countFaq = count(PageTemplates::resolve(PageTemplates::find('faq'))['blocks']);
    expect($component->get('data.content'))->toHaveCount($countServicos + $countFaq);

    $component->callFormComponentAction('content', 'loadTemplate', ['template' => 'faq', 'behavior' => 'replace']);
    expect($component->get('data.content'))->toHaveCount($countFaq);
});

test('página criada a partir de template é exibida no site', function () {
    livewire(CreatePage::class)
        ->callFormComponentAction('content', 'loadTemplate', ['template' => 'sobre'])
        ->fillForm(['title' => 'Sobre', 'slug' => 'sobre-template'])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->get('/sobre-template')
        ->assertOk()
        ->assertSee('Nossa trajetória')
        ->assertSee('O que nos move');
});

test('builder exibe o botão de templates e o seletor visual', function () {
    livewire(CreatePage::class)
        ->assertSeeHtml('id="page-builder-templates"')
        ->assertSeeHtml('id="page-builder-templates-empty"')
        ->mountFormComponentAction('content', 'loadTemplate')
        ->assertSee('Biblioteca de templates')
        ->assertSeeHtml('id="page-template-home_completa"')
        ->assertSee('Sobre a Empresa')
        ->assertDontSee('O que fazer com os blocos atuais?');
});
