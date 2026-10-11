<?php

use App\Filament\Resources\PageResource;
use App\Filament\Resources\PageResource\Pages\CreatePage;
use App\Filament\Resources\PageResource\Pages\EditPage;
use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Setting::create(['settings' => ['maintenance_mode' => false]]);

    $this->actingAs(User::factory()->create());

    $this->page = Page::create([
        'title' => 'Sobre',
        'slug' => 'sobre',
        'is_visible' => true,
        'is_active_in_menu' => false,
        'content' => [
            ['type' => 'rich_text', 'data' => ['content' => '<p>Texto publicado</p>']],
        ],
        'meta' => ['description' => 'Descrição publicada'],
        'published_at' => now(),
    ]);
});

test('salvar rascunho não altera a versão publicada', function () {
    livewire(EditPage::class, ['record' => $this->page->getRouteKey()])
        ->fillForm(['title' => 'Sobre (novo)', 'meta.description' => 'Descrição em rascunho'])
        ->call('saveDraft', true)
        ->assertSet('hasPendingChanges', false)
        ->assertNotSet('draftSavedAt', null);

    $page = $this->page->fresh();

    expect($page->title)->toBe('Sobre')
        ->and($page->meta['description'])->toBe('Descrição publicada')
        ->and($page->draft['title'])->toBe('Sobre (novo)')
        ->and($page->draft['meta']['description'])->toBe('Descrição em rascunho')
        ->and($page->draft_saved_at)->not->toBeNull();

    $this->get('/sobre')->assertOk()->assertSee('Texto publicado');
});

test('editor detecta alterações pendentes e o rascunho as limpa', function () {
    livewire(EditPage::class, ['record' => $this->page->getRouteKey()])
        ->assertSet('hasPendingChanges', false)
        ->fillForm(['title' => 'Outro título'])
        ->call('$refresh')
        ->assertSet('hasPendingChanges', true)
        ->call('saveDraft', true)
        ->assertSet('hasPendingChanges', false);
});

test('ao reabrir o editor o rascunho é carregado', function () {
    $this->page->update(['draft' => ['title' => 'Título do rascunho'], 'draft_saved_at' => now()]);

    livewire(EditPage::class, ['record' => $this->page->getRouteKey()])
        ->assertFormSet(['title' => 'Título do rascunho', 'slug' => 'sobre'])
        ->assertSet('hasPendingChanges', false)
        ->assertNotSet('draftSavedAt', null);
});

test('publicar aplica o rascunho e o limpa', function () {
    livewire(EditPage::class, ['record' => $this->page->getRouteKey()])
        ->fillForm(['title' => 'Sobre nós'])
        ->call('saveDraft', true)
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertSet('draftSavedAt', null);

    $page = $this->page->fresh();

    expect($page->title)->toBe('Sobre nós')
        ->and($page->slug)->toBe('sobre') // mudar o nome não muda o endereço de uma página existente
        ->and($page->draft)->toBeNull()
        ->and($page->published_at)->not->toBeNull();
});

test('descartar rascunho volta para a versão publicada', function () {
    $this->page->update(['draft' => ['title' => 'Rascunho'], 'draft_saved_at' => now()]);

    livewire(EditPage::class, ['record' => $this->page->getRouteKey()])
        ->callAction('discardDraft')
        ->assertFormSet(['title' => 'Sobre'])
        ->assertSet('draftSavedAt', null);

    expect($this->page->fresh()->draft)->toBeNull();
});

test('rascunho de página nova cria registro oculto e abre o editor', function () {
    livewire(CreatePage::class)
        ->fillForm(['title' => 'Trabalhe Conosco', 'slug' => 'trabalhe-conosco', 'is_active_in_menu' => true])
        ->call('saveDraft')
        ->assertRedirect(PageResource::getUrl('edit', ['record' => Page::where('slug', 'trabalhe-conosco')->first()]));

    $page = Page::where('slug', 'trabalhe-conosco')->firstOrFail();

    expect($page->is_visible)->toBeFalse()
        ->and($page->is_active_in_menu)->toBeFalse()
        ->and($page->published_at)->toBeNull()
        ->and($page->draft['is_active_in_menu'])->toBeTrue();

    $this->get('/trabalhe-conosco')->assertNotFound();
});

test('página inicial mantém o endereço fixo ao publicar', function () {
    $home = Page::create(['title' => 'Home', 'slug' => '/', 'is_visible' => true, 'content' => [], 'published_at' => now()]);

    livewire(EditPage::class, ['record' => $home->getRouteKey()])
        ->assertFormFieldIsDisabled('slug')
        ->fillForm(['title' => 'Início'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($home->fresh()->slug)->toBe('/');
});

test('erro ao publicar leva para a aba com o problema', function () {
    livewire(EditPage::class, ['record' => $this->page->getRouteKey()])
        ->fillForm(['title' => ''])
        ->call('save')
        ->assertHasFormErrors(['title' => 'required'])
        ->assertDispatched('page-editor-tab', tab: 'settings');
});

test('editor renderiza cabeçalho compacto e abas', function () {
    livewire(EditPage::class, ['record' => $this->page->getRouteKey()])
        ->assertSeeHtml('id="page-editor-tab-content"')
        ->assertSeeHtml('id="page-editor-tab-settings"')
        ->assertSee('Publicar')
        ->assertSee('Salvar rascunho')
        ->assertSee('Prévia no Google');
});
