<?php

use App\Filament\Resources\ContentSectionResource\Pages\ListContentSections;
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

    $this->hero = ContentSection::create(['slug' => 'hero', 'name' => 'Hero principal', 'type' => ContentSection::TYPE_HERO, 'content' => [], 'sort_order' => 1]);
    $this->faq = ContentSection::create(['slug' => 'faq', 'name' => 'Perguntas', 'type' => ContentSection::TYPE_FAQ, 'content' => [], 'sort_order' => 2]);
    $this->team = ContentSection::create(['slug' => 'team', 'name' => 'Equipe', 'type' => ContentSection::TYPE_TEAM, 'content' => [], 'is_active' => false, 'sort_order' => 3]);

    $reference = fn (ContentSection $section) => ['type' => 'module_reference', 'data' => ['content_section_id' => $section->id]];

    Page::create(['title' => 'Início', 'slug' => '/', 'is_visible' => true, 'content' => [
        ['type' => 'rich_text', 'data' => []],
        $reference($this->hero),
    ]]);
    Page::create(['title' => 'Sobre', 'slug' => 'sobre', 'is_visible' => true, 'content' => [$reference($this->faq), $reference($this->hero)]]);
});

test('mapa de uso aponta páginas e posição na homepage', function () {
    $map = ContentSection::usageMap();

    expect($map[$this->hero->id][0])->toMatchArray(['title' => 'Início', 'is_home' => true, 'position' => 2])
        ->and($map[$this->hero->id])->toHaveCount(2)
        ->and($map[$this->faq->id][0])->toMatchArray(['title' => 'Sobre', 'is_home' => false, 'position' => 1])
        ->and($map)->not->toHaveKey($this->team->id);
});

test('lista mostra cards com skeleton, tipo, status e uso', function () {
    livewire(ListContentSections::class)
        ->assertOk()
        ->assertSee('Hero principal')
        ->assertSee('Homepage · Posição 2')
        ->assertSee('Em uso em Sobre')
        ->assertSee('Não utilizada')
        ->assertSee('Inativa')
        ->assertSee('Editar conteúdo')
        ->assertSee('stp-pv', escape: false)
        ->assertViewHas('stats', ['total' => 3, 'active' => 2, 'used' => 2, 'home' => 1]);
});

test('filtros por busca, tipo, status e uso', function () {
    livewire(ListContentSections::class)
        ->set('search', 'pergunt')
        ->assertSee('Perguntas')->assertDontSee('Hero principal')
        ->set('search', 'equipe')
        ->assertSee('Equipe')->assertDontSee('Perguntas')
        ->set('search', '')
        ->set('type', ContentSection::TYPE_HERO)
        ->assertSee('Hero principal')->assertDontSee('Perguntas')
        ->set('type', 'inexistente')
        ->assertSet('type', '')
        ->set('status', 'inativas')
        ->assertSee('Equipe')->assertDontSee('Hero principal')
        ->set('status', 'todas')
        ->set('usage', 'home')
        ->assertSee('Hero principal')->assertDontSee('Perguntas')
        ->set('usage', 'sem-uso')
        ->assertSee('Equipe')->assertDontSee('Hero principal')
        ->set('search', 'nada-com-isso')
        ->assertSee('Nenhuma seção encontrada')
        ->call('clearFilters')
        ->assertSet('usage', '')
        ->assertSee('Hero principal');
});

test('estado vazio quando não há seções', function () {
    ContentSection::query()->delete();

    livewire(ListContentSections::class)
        ->assertSee('Nenhuma seção criada')
        ->assertSee('Criar primeira seção');
});

test('ativa e desativa uma seção; desativar uma seção em uso pede confirmação', function () {
    livewire(ListContentSections::class)
        ->callAction('toggleActive', arguments: ['record' => $this->team->id])
        ->assertNotified('Seção ativada');

    expect($this->team->fresh()->is_active)->toBeTrue();

    livewire(ListContentSections::class)
        ->mountAction('toggleActive', arguments: ['record' => $this->hero->id])
        ->assertActionMounted('toggleActive')
        ->assertSee('A seção deixa de aparecer em: Início, Sobre')
        ->callMountedAction()
        ->assertNotified('Seção desativada');

    expect($this->hero->fresh()->is_active)->toBeFalse();
});

test('reordena a lista completa e recusa ordem inválida ou com filtro', function () {
    $ids = [$this->team->id, $this->hero->id, $this->faq->id];

    livewire(ListContentSections::class)
        ->call('reorderSections', $ids)
        ->assertNotified('Ordem atualizada');

    expect(ContentSection::ordered()->pluck('id')->all())->toBe($ids);

    // Lista incompleta (ou com id de fora) não é gravada
    livewire(ListContentSections::class)
        ->call('reorderSections', [$this->hero->id, 999])
        ->assertNotified('A ordem não foi salva');

    // Com filtro ativo a ordem também não é gravada
    livewire(ListContentSections::class)
        ->set('status', 'ativas')
        ->call('reorderSections', [$this->hero->id, $this->faq->id, $this->team->id])
        ->assertNotified('A ordem não foi salva');

    expect(ContentSection::ordered()->pluck('id')->all())->toBe($ids);

    livewire(ListContentSections::class)->call('moveSection', $this->faq->id, -1);

    expect(ContentSection::ordered()->pluck('id')->all())->toBe([$this->team->id, $this->faq->id, $this->hero->id]);
});

test('duplica a seção como inativa e com slug único', function () {
    livewire(ListContentSections::class)
        ->callAction('duplicate', arguments: ['record' => $this->faq->id])
        ->callAction('duplicate', arguments: ['record' => $this->faq->id])
        ->assertNotified('Seção duplicada');

    expect(ContentSection::where('slug', 'faq-copia')->first())
        ->name->toBe('Perguntas (cópia)')
        ->is_active->toBeFalse()
        ->type->toBe(ContentSection::TYPE_FAQ)
        ->and(ContentSection::where('slug', 'faq-copia-2')->exists())->toBeTrue();
});

test('exclui com confirmação avisando as páginas que usam a seção', function () {
    livewire(ListContentSections::class)
        ->mountAction('delete', arguments: ['record' => $this->faq->id])
        ->assertSee('Ela é usada em: Sobre')
        ->callMountedAction();

    expect(ContentSection::find($this->faq->id))->toBeNull();
});

test('ações em massa ativam, desativam e excluem a seleção', function () {
    livewire(ListContentSections::class)
        ->set('selected', [$this->hero->id, $this->team->id])
        ->assertSee('2 seções selecionadas')
        ->callAction('bulkDeactivate')
        ->assertSet('selected', []);

    expect(ContentSection::where('is_active', true)->pluck('id')->all())->toBe([$this->faq->id]);

    livewire(ListContentSections::class)
        ->set('selected', [$this->hero->id, $this->team->id])
        ->callAction('bulkDelete');

    expect(ContentSection::pluck('id')->all())->toBe([$this->faq->id]);
});

test('id inexistente não monta nem executa ação', function () {
    livewire(ListContentSections::class)
        ->call('mountAction', 'delete', ['record' => 999])
        ->assertActionNotMounted('delete')
        ->call('mountAction', 'toggleActive', ['record' => 999])
        ->assertActionNotMounted('toggleActive');

    expect(ContentSection::count())->toBe(3);
});

test('sem acesso à área Website a lista e as alterações são bloqueadas', function () {
    $this->actingAs(User::factory()->withRole('Comercial')->create());

    $this->get('/admin/content-sections')->assertForbidden();
    livewire(ListContentSections::class)->assertForbidden();

    expect(ContentSection::count())->toBe(3);
});
