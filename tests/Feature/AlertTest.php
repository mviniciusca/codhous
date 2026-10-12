<?php

use App\Filament\Resources\AlertResource;
use App\Filament\Resources\AlertResource\Pages\CreateAlert;
use App\Filament\Resources\AlertResource\Pages\EditAlert;
use App\Filament\Resources\AlertResource\Pages\ListAlerts;
use App\Models\Alert;
use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Setting::create(['settings' => ['maintenance_mode' => false]]);

    $this->actingAs(User::factory()->create());

    $base = ['message' => 'Mensagem', 'style' => Alert::STYLE_INFO, 'is_dismissible' => true];

    $this->banner = Alert::create([...$base, 'name' => 'Banner Black Friday', 'type' => Alert::TYPE_BANNER, 'style' => Alert::STYLE_PROMO, 'position' => Alert::POSITION_TOP, 'is_active' => true, 'sort_order' => 1]);
    $this->modal = Alert::create([...$base, 'name' => 'Cadastro Newsletter', 'type' => Alert::TYPE_MODAL, 'position' => Alert::POSITION_CENTER, 'is_active' => false, 'sort_order' => 2]);
    $this->toast = Alert::create([...$base, 'name' => 'Frete grátis', 'type' => Alert::TYPE_TOAST, 'position' => Alert::POSITION_BOTTOM_RIGHT, 'is_active' => true, 'start_at' => now()->addDays(3), 'sort_order' => 3]);
});

function validAlertData(array $overrides = []): array
{
    return [
        'name' => 'Aviso de entrega',
        'title' => 'Atenção!',
        'message' => 'Confira as novas regras de entrega.',
        'type' => Alert::TYPE_TOAST,
        'style' => Alert::STYLE_WARNING,
        'position' => Alert::POSITION_TOP_RIGHT,
        'is_dismissible' => true,
        'use_cookie' => false,
        'sort_order' => 0,
        ...$overrides,
    ];
}

// ---------------------------------------------------------------- Model

test('status considera publicação e período de exibição', function () {
    expect($this->banner->status())->toBe(Alert::STATUS_ACTIVE)
        ->and($this->modal->status())->toBe(Alert::STATUS_DRAFT)
        ->and($this->toast->status())->toBe(Alert::STATUS_SCHEDULED)
        ->and(Alert::make(['is_active' => true, 'end_at' => now()->subDay()])->status())->toBe(Alert::STATUS_EXPIRED);
});

test('posição incompatível com o tipo vira a padrão do tipo ao salvar', function () {
    $modal = Alert::create(validAlertData(['type' => Alert::TYPE_MODAL, 'position' => Alert::POSITION_TOP]));
    $banner = Alert::create(validAlertData(['type' => Alert::TYPE_BANNER, 'position' => Alert::POSITION_TOP_LEFT]));
    $toast = Alert::create(validAlertData(['type' => Alert::TYPE_TOAST, 'position' => Alert::POSITION_CENTER]));

    expect($modal->position)->toBe(Alert::POSITION_CENTER)
        ->and($banner->position)->toBe(Alert::POSITION_TOP)
        ->and($toast->position)->toBe(Alert::POSITION_TOP_RIGHT);
});

test('links do botão aceitam só destinos seguros', function (string $url, bool $safe) {
    expect(Alert::sanitizeUrl($url) !== null)->toBe($safe);
})->with([
    ['https://codhous.com.br/oferta', true],
    ['http://exemplo.com', true],
    ['/politica-de-privacidade', true],
    ['#contato', true],
    ['mailto:contato@codhous.com.br', true],
    ['tel:+5521999999999', true],
    ['javascript:alert(1)', false],
    ['JavaScript:alert(1)', false],
    ['data:text/html,<script>alert(1)</script>', false],
    ['//evil.example.com', false],
    ['/\\evil.example.com', false],
    ["java\tscript:alert(1)", false],
    ['vbscript:msgbox', false],
]);

// ---------------------------------------------------------------- Site público

test('site renderiza cada tipo de alerta com conteúdo escapado e link seguro', function () {
    $alerts = collect([
        Alert::make(validAlertData(['type' => Alert::TYPE_MODAL, 'title' => '<script>alert(1)</script>', 'cta_label' => 'Saiba mais', 'cta_url' => 'javascript:alert(1)'])),
        Alert::make(validAlertData(['type' => Alert::TYPE_BANNER, 'position' => Alert::POSITION_BOTTOM, 'cta_label' => 'Ver', 'cta_url' => '/contato'])),
        Alert::make(validAlertData(['type' => Alert::TYPE_TOAST, 'position' => Alert::POSITION_BOTTOM_LEFT])),
    ]);

    $html = Blade::render('<x-site-alerts :alerts="$alerts" />', ['alerts' => $alerts]);

    expect($html)
        ->toContain('&lt;script&gt;alert(1)&lt;/script&gt;')
        ->not->toContain('<script>alert(1)')
        ->not->toContain('javascript:')
        ->toContain('href="/contato"')
        ->toContain('fixed inset-0 flex items-center justify-center')
        ->toContain('absolute bottom-0 left-0 right-0')
        ->toContain('absolute bottom-6 left-6')
        ->toContain('alertWidget');
});

test('site mostra só alertas ativos dentro do período', function () {
    $this->banner->update(['title' => 'Título ativo']);
    $this->modal->update(['title' => 'Título rascunho']);
    $this->toast->update(['title' => 'Título agendado']);

    expect(Blade::render('<x-site-alerts />'))
        ->toContain('Título ativo')
        ->not->toContain('Título rascunho')
        ->not->toContain('Título agendado');
});

// ---------------------------------------------------------------- Listagem

test('lista mostra cards com tipo, estilo, status e indicadores', function () {
    livewire(ListAlerts::class)
        ->assertOk()
        ->assertSee('Banner Black Friday')
        ->assertSee('Cadastro Newsletter')
        ->assertSee('Banner fixo')
        ->assertSee('Promoção')
        ->assertSee('Rascunho')
        ->assertSee('Agendado')
        ->assertSee('Começa em '.$this->toast->start_at->format('d/m/Y'))
        ->assertSee('Exibido no topo')
        ->assertViewHas('stats', fn (array $stats) => $stats['total'] === 3 && $stats['active'] === 1 && $stats['draft'] === 1 && $stats['scheduled'] === 1);
});

test('filtros por busca, tipo, estilo e status', function () {
    livewire(ListAlerts::class)
        ->set('search', 'newsletter')
        ->assertSee('Cadastro Newsletter')->assertDontSee('Banner Black Friday')
        ->set('search', '')
        ->set('type', Alert::TYPE_TOAST)
        ->assertSee('Frete grátis')->assertDontSee('Cadastro Newsletter')
        ->set('type', '')
        ->set('style', Alert::STYLE_PROMO)
        ->assertSee('Banner Black Friday')->assertDontSee('Frete grátis')
        ->set('style', '')
        ->set('status', Alert::STATUS_DRAFT)
        ->assertSee('Cadastro Newsletter')->assertDontSee('Banner Black Friday')
        ->set('status', 'invalido')
        ->assertSet('status', '');
});

test('interruptor publica e despublica pela lista', function () {
    livewire(ListAlerts::class)
        ->callAction('toggleActive', arguments: ['record' => $this->modal->id])
        ->assertNotified('Alerta publicado')
        ->callAction('toggleActive', arguments: ['record' => $this->banner->id])
        ->assertNotified('Alerta desativado');

    expect($this->modal->fresh()->is_active)->toBeTrue()
        ->and($this->banner->fresh()->is_active)->toBeFalse();
});

test('duplicar cria rascunho no fim da lista, sem reaproveitar o cookie', function () {
    $this->banner->update(['cookie_key' => 'black-friday']);

    livewire(ListAlerts::class)->callAction('duplicate', arguments: ['record' => $this->banner->id]);

    $copy = Alert::latest('id')->first();

    expect($copy->name)->toBe('Banner Black Friday (cópia)')
        ->and($copy->is_active)->toBeFalse()
        ->and($copy->cookie_key)->toBeNull()
        ->and($copy->sort_order)->toBe(4);
});

test('mover para baixo troca a ordem de exibição', function () {
    livewire(ListAlerts::class)->call('moveAlert', $this->banner->id, 1);

    expect(Alert::orderBy('sort_order')->pluck('id')->all())->toBe([$this->modal->id, $this->banner->id, $this->toast->id]);
});

test('excluir pela lista', function () {
    livewire(ListAlerts::class)->callAction('delete', arguments: ['record' => $this->modal->id]);

    expect(Alert::find($this->modal->id))->toBeNull();
});

// ---------------------------------------------------------------- Editor

test('editor abre com prévia ao vivo e barra de ações', function () {
    livewire(CreateAlert::class)
        ->assertOk()
        ->assertSee('Pré-visualização ao vivo')
        ->assertSee('Status e informações')
        ->assertActionExists('publish')
        ->assertSee('Salvar rascunho');
});

test('salvar rascunho cria inativo e publicar cria ativo', function () {
    livewire(CreateAlert::class)
        ->fillForm(validAlertData(['name' => 'Rascunho']))
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified('Rascunho salvo');

    livewire(CreateAlert::class)
        ->fillForm(validAlertData(['name' => 'Publicado']))
        ->callAction('publish')
        ->assertHasNoFormErrors()
        ->assertNotified('Alerta publicado')
        ->assertRedirect(AlertResource::getUrl('index'));

    expect(Alert::where('name', 'Rascunho')->first()->is_active)->toBeFalse()
        ->and(Alert::where('name', 'Publicado')->first()->is_active)->toBeTrue();
});

test('validação no servidor: link inseguro, botão sem link, posição incompatível e datas', function () {
    livewire(CreateAlert::class)
        ->fillForm(validAlertData(['cta_label' => 'Clique', 'cta_url' => 'javascript:alert(1)']))
        ->call('create')
        ->assertHasFormErrors(['cta_url']);

    livewire(CreateAlert::class)
        ->fillForm(validAlertData(['cta_label' => 'Clique', 'cta_url' => null]))
        ->call('create')
        ->assertHasFormErrors(['cta_url']);

    livewire(CreateAlert::class)
        ->fillForm(validAlertData(['type' => Alert::TYPE_BANNER, 'position' => Alert::POSITION_TOP_RIGHT]))
        ->call('create')
        ->assertHasFormErrors(['position']);

    livewire(CreateAlert::class)
        ->fillForm(validAlertData(['style' => 'inexistente']))
        ->call('create')
        ->assertHasFormErrors(['style']);

    livewire(CreateAlert::class)
        ->fillForm(validAlertData(['start_at' => '2026-10-20 10:00', 'end_at' => '2026-10-19 10:00']))
        ->call('create')
        ->assertHasFormErrors(['end_at']);

    expect(Alert::count())->toBe(3);
});

test('trocar o tipo ajusta a posição e o modal não mostra o campo', function () {
    livewire(CreateAlert::class)
        ->fillForm(['type' => Alert::TYPE_BANNER])
        ->assertFormSet(['position' => Alert::POSITION_TOP])
        ->assertFormFieldIsVisible('position')
        ->fillForm(['type' => Alert::TYPE_MODAL])
        ->assertFormFieldIsHidden('position');
});

test('lembrar fechamento só aparece quando o alerta pode ser fechado', function () {
    livewire(CreateAlert::class)
        ->fillForm(['is_dismissible' => false])
        ->assertFormFieldIsHidden('use_cookie')
        ->fillForm(['is_dismissible' => true, 'use_cookie' => true])
        ->assertFormFieldIsVisible('use_cookie')
        ->assertFormFieldIsVisible('cookie_duration_days');
});

test('prévia renderiza o estado não salvo com o componente do site, escapado', function () {
    $html = livewire(CreateAlert::class)
        ->fillForm(validAlertData(['title' => '<img src=x onerror=alert(1)>', 'cta_label' => 'Ver', 'cta_url' => 'javascript:alert(1)']))
        ->call('renderAlertPreview')
        ->effects['returns'][0];

    expect($html)
        ->toContain('&lt;img src=x onerror=alert(1)&gt;')
        ->not->toContain('javascript:')
        ->toContain('absolute top-6 right-6');
});

test('editar alerta publicado mantém publicado ao salvar e despublica pela ação', function () {
    livewire(EditAlert::class, ['record' => $this->banner->getRouteKey()])
        ->assertActionExists('unpublish')
        ->fillForm(['title' => 'Novo título'])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified('Alterações salvas');

    expect($this->banner->fresh())->title->toBe('Novo título')->is_active->toBeTrue();

    livewire(EditAlert::class, ['record' => $this->banner->getRouteKey()])
        ->callAction('unpublish')
        ->assertNotified('Alerta despublicado');

    expect($this->banner->fresh()->is_active)->toBeFalse();
});

test('editar rascunho e publicar', function () {
    livewire(EditAlert::class, ['record' => $this->modal->getRouteKey()])
        ->assertActionExists('publish')
        ->callAction('publish')
        ->assertNotified('Alerta publicado');

    expect($this->modal->fresh()->is_active)->toBeTrue();
});

test('dados existentes abrem no editor sem perder campos', function () {
    $legacy = Alert::create(validAlertData([
        'name' => 'Consentimento de cookies', 'type' => Alert::TYPE_BANNER, 'style' => Alert::STYLE_CONSENT,
        'position' => Alert::POSITION_BOTTOM, 'use_cookie' => true, 'cookie_key' => 'cookie_consent_accepted',
        'cookie_duration_days' => 365, 'cta_label' => 'Política de privacidade', 'cta_url' => '/politica-de-privacidade',
    ]));

    livewire(EditAlert::class, ['record' => $legacy->getRouteKey()])
        ->assertFormSet(['cookie_key' => 'cookie_consent_accepted', 'cookie_duration_days' => 365, 'cta_url' => '/politica-de-privacidade'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($legacy->fresh())->cookie_key->toBe('cookie_consent_accepted')->position->toBe(Alert::POSITION_BOTTOM);
});

// ---------------------------------------------------------------- Prévia e acesso

test('rota de prévia mostra só o alerta pedido, sem os ativos', function () {
    Page::create(['title' => 'Início', 'slug' => '/', 'is_visible' => true, 'content' => []]);
    $this->banner->update(['title' => 'Título do banner ativo']);
    $this->modal->update(['title' => 'Título do modal em rascunho']);

    $this->get(route('alerts.preview', ['alerta' => $this->modal->id]))
        ->assertOk()
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertSee('Título do modal em rascunho')
        ->assertDontSee('Título do banner ativo')
        ->assertSee('al-preview-root', escape: false);
});

test('perfil sem a área Website não acessa alertas nem a prévia', function () {
    $this->actingAs(User::factory()->withRole('Comercial')->create());

    expect(AlertResource::canViewAny())->toBeFalse();

    livewire(ListAlerts::class)->assertForbidden();
    $this->get(route('alerts.preview'))->assertForbidden();
});
